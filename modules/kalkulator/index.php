<?php
require_once '../../config/database.php';
require_once '../../includes/fungsi.php';
session_start();
require_once '../../includes/header.php';

if (!isset($_SESSION['keranjang_menu'])) {
    $_SESSION['keranjang_menu'] = [];
}

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
    header('Location: index.php');
    exit;
}

if (isset($_GET['refresh'])) {
    $_SESSION['keranjang_menu'] = [];
    header('Location: index.php');
    exit;
}

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
            }
        }
    }
}

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