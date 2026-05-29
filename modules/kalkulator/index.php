<?php
require_once '../../config/database.php';
require_once '../../includes/fungsi.php';
<<<<<<< HEAD
session_start();
=======

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
>>>>>>> f9fdb3fd35fab447ae2718eaa32767cdbe800f7d
require_once '../../includes/header.php';

if (!isset($_SESSION['keranjang_menu'])) {
    $_SESSION['keranjang_menu'] = [];
}

<<<<<<< HEAD
$itemsByTipe = ambilBarangGroupedByTipe($conn);
$itemMap = [];
foreach ($itemsByTipe as $group) {
    foreach ($group as $b) {
        $itemMap[$b['id_barang']] = $b;
    }
}

// Handle actions
if (isset($_GET['hapus'])) {
    unset($_SESSION['keranjang_menu'][(int)$_GET['hapus']]);
=======
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
>>>>>>> f9fdb3fd35fab447ae2718eaa32767cdbe800f7d
    header('Location: index.php');
    exit;
}

<<<<<<< HEAD
if (isset($_GET['refresh'])) {
=======
if (isset($_GET['action']) && $_GET['action'] === 'refresh') {
>>>>>>> f9fdb3fd35fab447ae2718eaa32767cdbe800f7d
    $_SESSION['keranjang_menu'] = [];
    header('Location: index.php');
    exit;
}

<<<<<<< HEAD
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tambah'])) {
        foreach ($_POST['qty'] as $id => $qty) {
            if ($qty > 0) {
                $_SESSION['keranjang_menu'][(int)$id] = (int)$qty;
            }
        }
        $message = '<div class="alert alert-success">✓ Pesanan ditambahkan</div>';
    }
    
    if (isset($_POST['simpan'])) {
        $nama = trim($_POST['nama_pemesan']);
        if (empty($nama)) {
            $message = '<div class="alert alert-error">✗ Nama pemesan wajib diisi</div>';
        } elseif (empty($_SESSION['keranjang_menu'])) {
            $message = '<div class="alert alert-error">✗ Keranjang kosong</div>';
        } else {
            $total = 0;
            $items = [];
            foreach ($_SESSION['keranjang_menu'] as $id => $qty) {
                if (isset($itemMap[$id])) {
                    $subtotal = $itemMap[$id]['harga'] * $qty;
                    $total += $subtotal;
                    $items[] = ['id_barang' => $id, 'jumlah' => $qty];
                }
            }
            
            mysqli_begin_transaction($conn);
            try {
                $query = "INSERT INTO transaksi (total, nama_pemesan) VALUES ($total, '" . mysqli_real_escape_string($conn, $nama) . "')";
                mysqli_query($conn, $query);
                $id_transaksi = mysqli_insert_id($conn);
                
                foreach ($items as $item) {
                    $q = "INSERT INTO detail_transaksi (id_transaksi, id_barang, jumlah) VALUES ($id_transaksi, {$item['id_barang']}, {$item['jumlah']})";
                    mysqli_query($conn, $q);
                }
                
                mysqli_commit($conn);
                $message = '<div class="alert alert-success">✓ Transaksi berhasil! Total: ' . rupiah($total) . '</div>';
                $_SESSION['keranjang_menu'] = [];
            } catch (Exception $e) {
                mysqli_rollback($conn);
                $message = '<div class="alert alert-error">✗ Gagal menyimpan transaksi</div>';
=======
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
>>>>>>> f9fdb3fd35fab447ae2718eaa32767cdbe800f7d
            }
        }
    }
}

<<<<<<< HEAD
// Calculate total
$cartTotal = 0;
$cartItems = [];
foreach ($_SESSION['keranjang_menu'] as $id => $qty) {
    if (isset($itemMap[$id])) {
        $sub = $itemMap[$id]['harga'] * $qty;
        $cartTotal += $sub;
        $cartItems[] = [
            'id' => $id,
            'nama' => $itemMap[$id]['nama_barang'],
            'harga' => $itemMap[$id]['harga'],
            'qty' => $qty,
            'subtotal' => $sub,
            'tipe' => $itemMap[$id]['tipe']
        ];
    }
}
?>

<h1>Kalkulator Menu</h1>

<?= $message ?>

<form method="POST">
    <div class="form-group">
        <label>Nama Pemesan</label>
        <input type="text" name="nama_pemesan" placeholder="Masukkan nama" value="<?= htmlspecialchars($_POST['nama_pemesan'] ?? '') ?>">
    </div>
    
    <?php foreach ($itemsByTipe as $tipe => $items): ?>
        <div style="margin-bottom: 24px;">
            <div class="category-title"><?= ucfirst($tipe) ?></div>
            <div class="product-grid">
                <?php foreach ($items as $item): ?>
                    <?php 
                    $qty = $_SESSION['keranjang_menu'][$item['id_barang']] ?? 0;
                    ?>
                    <div class="product-card">
                        <div class="product-name"><?= htmlspecialchars($item['nama_barang']) ?></div>
                        <div class="product-price"><?= rupiah($item['harga']) ?></div>
                        <input type="number" name="qty[<?= $item['id_barang'] ?>]" value="<?= $qty ?>" min="0" class="product-qty">
                        <?php if ($qty > 0): ?>
                            <div class="product-subtotal">= <?= rupiah($item['harga'] * $qty) ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
    
    <div style="display: flex; gap: 12px; margin-top: 20px;">
        <button type="submit" name="tambah" class="btn btn-primary">Tambah ke Keranjang</button>
        <button type="submit" name="simpan" class="btn btn-success">Simpan Transaksi</button>
        <a href="?refresh" class="btn btn-outline" onclick="return confirm('Reset keranjang?')">Reset</a>
    </div>
</form>

<?php if (!empty($cartItems)): ?>
<hr>
<h2>Keranjang</h2>
<div class="table-wrapper">
    <table>
        <thead>
            <tr><th>Menu</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th><th></th></tr>
        </thead>
        <tbody>
            <?php foreach ($cartItems as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['nama']) ?></td>
                <td><?= rupiah($item['harga']) ?></td>
                <td><?= $item['qty'] ?></td>
                <td><?= rupiah($item['subtotal']) ?></td>
                <td><a href="?hapus=<?= $item['id'] ?>" class="action-link action-link-danger">Hapus</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="background: #f8fafc;"><td colspan="3"><strong>Total</strong></td><td colspan="2"><strong><?= rupiah($cartTotal) ?></strong></td></tr>
        </tfoot>
    </table>
</div>
<?php endif; ?>

<?php include '../../includes/footer.php'; ?>
=======
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



>>>>>>> f9fdb3fd35fab447ae2718eaa32767cdbe800f7d
