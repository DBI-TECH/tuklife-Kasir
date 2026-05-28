<?php
require_once '../../config/database.php';
require_once '../../includes/fungsi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../includes/header.php';

if (!isset($_SESSION['keranjang_menu'])) {
    $_SESSION['keranjang_menu'] = [];
}

$pesan = '';
$itemsByTipe = ambilBarangGroupedByTipe($conn);
$items = [];
foreach ($itemsByTipe as $group) {
    foreach ($group as $b) {
        $items[] = $b;
    }
}

$itemMap = [];
foreach ($items as $it) {
    $itemMap[$it['id_barang']] = $it;
}

function getQtyPost(int $id_barang): int
{
    if (!isset($_POST['qty']) || !is_array($_POST['qty'])) return 0;
    if (!array_key_exists($id_barang, $_POST['qty'])) return 0;
    $q = (int)$_POST['qty'][$id_barang];
    if ($q < 0) $q = 0;
    return $q;
}

if (isset($_GET['action']) && $_GET['action'] === 'hapus') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0 && isset($_SESSION['keranjang_menu'][$id])) {
        unset($_SESSION['keranjang_menu'][$id]);
    }
    header('Location: index.php');
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'refresh') {
    $_SESSION['keranjang_menu'] = [];
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_to_keranjang'])) {
        $added = false;
        foreach ($items as $it) {
            $id_barang = (int)$it['id_barang'];
            $qty = getQtyPost($id_barang);
            if ($qty > 0) {
                $_SESSION['keranjang_menu'][$id_barang] = $qty;
                $added = true;
            } elseif (isset($_SESSION['keranjang_menu'][$id_barang])) {
                unset($_SESSION['keranjang_menu'][$id_barang]);
            }
        }

        if ($added) {
            $pesan = 'Pesanan berhasil ditambahkan/diupdate ke keranjang.';
        } else {
            $pesan = 'Isi qty minimal 1 untuk menambahkan pesanan.';
        }
        } elseif (isset($_POST['submit_transaksi'])) {
        $nama_pemesan = trim($_POST['nama_pemesan'] ?? '');

        if ($nama_pemesan === '') {
            $pesan = 'Nama pemesan wajib diisi.';
        } elseif (empty($_SESSION['keranjang_menu'])) {
            $pesan = 'Keranjang kosong. Tambahkan pesanan terlebih dahulu.';
        } else {
            $cartItems = [];
            $total = 0;
            foreach ($_SESSION['keranjang_menu'] as $id_barang => $qty) {
                $id_barang = (int)$id_barang;
                $qty = (int)$qty;

                if ($id_barang <= 0 || $qty <= 0) {
                    continue;
                }

                if (!isset($itemMap[$id_barang])) {
                    $pesan = 'Salah satu barang di keranjang sudah tidak tersedia.';
                    break;
                }

                $harga = (int)$itemMap[$id_barang]['harga'];
                $total += $harga * $qty;
                $cartItems[] = [
                    'id_barang' => $id_barang,
                    'jumlah' => $qty,
                ];
            }

            if (empty($cartItems) && $pesan === '') {
                $pesan = 'Keranjang tidak berisi barang valid untuk disimpan.';
            }

            if ($pesan === '') {
                mysqli_begin_transaction($conn);
                try {
                    $nama_pemesan_esc = mysqli_real_escape_string($conn, $nama_pemesan);
                    $query = "INSERT INTO transaksi (total, nama_pemesan) VALUES ($total, '$nama_pemesan_esc')";
                    if (!mysqli_query($conn, $query)) {
                        throw new Exception('Gagal menyimpan transaksi: ' . mysqli_error($conn));
                    }
                    $id_transaksi = mysqli_insert_id($conn);

                    foreach ($cartItems as $item) {
                        $q = "INSERT INTO detail_transaksi (id_transaksi, id_barang, jumlah) VALUES ($id_transaksi, {$item['id_barang']}, {$item['jumlah']})";
                        if (!mysqli_query($conn, $q)) {
                            throw new Exception('Gagal menyimpan detail: ' . mysqli_error($conn));
                        }
                    }

                    mysqli_commit($conn);
                    $pesan = 'Transaksi berhasil disimpan. Total: ' . rupiah($total);
                    $_SESSION['keranjang_menu'] = [];
                    $_POST = [];
                } catch (Throwable $e) {
                    mysqli_rollback($conn);
                    $pesan = $e->getMessage();
                }
            }
        }
    }
}

$totalSementara = 0;
$keranjangByTipe = [];
foreach ($_SESSION['keranjang_menu'] as $id_barang => $qty) {
    $menu = $itemMap[$id_barang] ?? null;
    if (!$menu) {
        continue;
    }
    $harga = (int)$menu['harga'];
    $totalSementara += $harga * $qty;
    $tipe = trim($menu['tipe'] ?? '') !== '' ? $menu['tipe'] : 'Umum';
    $keranjangByTipe[$tipe][$id_barang] = [
        'nama' => $menu['nama_barang'],
        'harga' => $harga,
        'qty' => $qty,
        'sub' => $harga * $qty,
    ];
}
?>

<h2>Kalkulator Menu Kasir (Keranjang)</h2>

<?php if (!empty($pesan)): ?>
    <p style="color:green; font-weight:700;"><?= htmlspecialchars($pesan) ?></p>
<?php endif; ?>

<form method="POST">
    <div style="margin: 0 0 12px 0;">
        Nama Pemesan: <input type="text" name="nama_pemesan" value="<?= htmlspecialchars($_POST['nama_pemesan'] ?? '') ?>" required>
    </div>

    <table border="1" cellpadding="6" cellspacing="0">
        <?php if (empty($itemsByTipe)): ?>
            <tr><td colspan="4">Belum ada menu untuk ditampilkan.</td></tr>
        <?php else: ?>
            <?php foreach ($itemsByTipe as $tipe => $group): ?>
                <thead>
                    <tr>
                        <th colspan="4" style="background:#f0f0f0;text-align:left;">Kategori: <?= htmlspecialchars(ucwords($tipe)) ?></th>
                    </tr>
                    <tr>
                        <th>Nama Menu</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Sub Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($group as $it):
                        $id_barang = (int)$it['id_barang'];
                        $nama = $it['nama_barang'];
                        $harga = (int)$it['harga'];

                        $qtyVal = 0;
                        if (isset($_POST['qty'][$id_barang])) {
                            $qtyVal = (int)$_POST['qty'][$id_barang];
                            if ($qtyVal < 0) $qtyVal = 0;
                        } elseif (isset($_SESSION['keranjang_menu'][$id_barang])) {
                            $qtyVal = (int)$_SESSION['keranjang_menu'][$id_barang];
                        }
                        $sub = $harga * $qtyVal;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($nama) ?></td>
                        <td><?= rupiah($harga) ?></td>
                        <td>
                            <input type="number" min="0" name="qty[<?= $id_barang ?>]" value="<?= $qtyVal ?>" style="width:90px;" />
                        </td>
                        <td><?= rupiah($sub) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <p style="font-size:18px; font-weight:700; margin-top:12px;">
        Total Semua Harga: <?= rupiah($totalSementara) ?>
    </p>

    <button type="submit" name="add_to_keranjang" value="1">Tambah ke Keranjang</button>
    <button type="submit" name="submit_transaksi" value="1">Simpan Transaksi</button>
    <a href="?action=refresh" style="margin-left:12px;">Refresh Pesanan</a>
</form>

<h3>Keranjang Pesanan</h3>
<?php if (empty($_SESSION['keranjang_menu'])): ?>
    <p>Keranjang kosong.</p>
<?php else: ?>
    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>Nama Menu</th>
                <th>Harga</th>
                <th>Qty</th>
                <th>Sub Total</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($keranjangByTipe as $tipe => $itemsInTipe): ?>
            <tr style="background:#f4f4f4;">
                <td colspan="5"><strong>Kategori: <?= htmlspecialchars(ucwords($tipe)) ?></strong></td>
            </tr>
            <?php foreach ($itemsInTipe as $id_barang => $data): ?>
                <tr>
                    <td><?= htmlspecialchars($data['nama']) ?></td>
                    <td><?= rupiah($data['harga']) ?></td>
                    <td><?= $data['qty'] ?></td>
                    <td><?= rupiah($data['sub']) ?></td>
                    <td><a href="?action=hapus&id=<?= $id_barang ?>" onclick="return confirm('Hapus item dari keranjang?')">Hapus</a></td>
                </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
        </tbody>
    </table>
    <p style="font-weight:700; margin-top:12px;">Total Saat Ini: <?= rupiah($totalSementara) ?></p>
<?php endif; ?>

<?php include '../../includes/footer.php'; ?>



