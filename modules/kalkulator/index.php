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

$itemsByTipe = ambilBarangGroupedByTipe($conn);
$itemMap = [];
foreach ($itemsByTipe as $group) {
    foreach ($group as $b) {
        $itemMap[$b['id_barang']] = $b;
    }
}

function getQtyPost(int $id_barang): int
{
    if (!isset($_POST['qty']) || !is_array($_POST['qty'])) return 0;
    if (!array_key_exists($id_barang, $_POST['qty'])) return 0;
    $q = (int)$_POST['qty'][$id_barang];
    if ($q < 0) $q = 0;
    return $q;
}

// Handle actions
if (isset($_GET['action']) && $_GET['action'] === 'hapus' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if (isset($_SESSION['keranjang_menu'][$id])) {
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

$pesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_to_keranjang'])) {
        $added = false;
        foreach ($itemsByTipe as $group) {
            foreach ($group as $it) {
                $id_barang = (int)$it['id_barang'];
                $qty = getQtyPost($id_barang);
                if ($qty > 0) {
                    $_SESSION['keranjang_menu'][$id_barang] = $qty;
                    $added = true;
                } elseif (isset($_SESSION['keranjang_menu'][$id_barang])) {
                    unset($_SESSION['keranjang_menu'][$id_barang]);
                }
            }
        }
        $pesan = $added ? '✓ Pesanan berhasil ditambahkan ke keranjang.' : '⚠ Isi jumlah minimal 1 untuk menambahkan pesanan.';
    } elseif (isset($_POST['submit_transaksi'])) {
        $nama_pemesan = trim($_POST['nama_pemesan'] ?? '');
        
        if ($nama_pemesan === '') {
            $pesan = '✗ Nama pemesan wajib diisi.';
        } elseif (empty($_SESSION['keranjang_menu'])) {
            $pesan = '✗ Keranjang kosong. Tambahkan pesanan terlebih dahulu.';
        } else {
            $cartItems = [];
            $total = 0;
            $error = false;
            
            foreach ($_SESSION['keranjang_menu'] as $id_barang => $qty) {
                $id_barang = (int)$id_barang;
                $qty = (int)$qty;
                if ($id_barang <= 0 || $qty <= 0) continue;
                
                if (!isset($itemMap[$id_barang])) {
                    $pesan = '✗ Salah satu menu di keranjang sudah tidak tersedia.';
                    $error = true;
                    break;
                }
                
                $harga = (int)$itemMap[$id_barang]['harga'];
                $total += $harga * $qty;
                $cartItems[] = ['id_barang' => $id_barang, 'jumlah' => $qty];
            }
            
            if (!$error && empty($cartItems)) {
                $pesan = '✗ Keranjang tidak berisi item valid.';
                $error = true;
            }
            
            if (!$error) {
                mysqli_begin_transaction($conn);
                try {
                    $nama_pemesan_esc = mysqli_real_escape_string($conn, $nama_pemesan);
                    $query = "INSERT INTO transaksi (total, nama_pemesan) VALUES ($total, '$nama_pemesan_esc')";
                    if (!mysqli_query($conn, $query)) {
                        throw new Exception('Gagal menyimpan transaksi');
                    }
                    $id_transaksi = mysqli_insert_id($conn);
                    
                    foreach ($cartItems as $item) {
                        $q = "INSERT INTO detail_transaksi (id_transaksi, id_barang, jumlah) VALUES ($id_transaksi, {$item['id_barang']}, {$item['jumlah']})";
                        if (!mysqli_query($conn, $q)) {
                            throw new Exception('Gagal menyimpan detail transaksi');
                        }
                    }
                    
                    mysqli_commit($conn);
                    $pesan = '✓ Transaksi berhasil disimpan! Total: ' . rupiah($total);
                    $_SESSION['keranjang_menu'] = [];
                    $_POST = [];
                } catch (Exception $e) {
                    mysqli_rollback($conn);
                    $pesan = '✗ ' . $e->getMessage();
                }
            }
        }
    }
}

// Calculate cart total
$totalSementara = 0;
$keranjangItems = [];
foreach ($_SESSION['keranjang_menu'] as $id_barang => $qty) {
    $menu = $itemMap[$id_barang] ?? null;
    if (!$menu) continue;
    $harga = (int)$menu['harga'];
    $totalSementara += $harga * $qty;
    $keranjangItems[] = [
        'id' => $id_barang,
        'nama' => $menu['nama_barang'],
        'harga' => $harga,
        'qty' => $qty,
        'subtotal' => $harga * $qty,
        'tipe' => $menu['tipe'] ?? 'Umum'
    ];
}
?>

<h1><i class="fas fa-calculator"></i> Kalkulator Menu Kasir</h1>

<?php if (!empty($pesan)): ?>
    <div class="alert alert-<?= strpos($pesan, '✓') !== false ? 'success' : (strpos($pesan, '⚠') !== false ? 'warning' : 'error') ?>">
        <?= htmlspecialchars($pesan) ?>
    </div>
<?php endif; ?>

<form method="POST">
    <div class="form-group">
        <label for="nama_pemesan">Nama Pemesan <span style="color:red;">*</span></label>
        <input type="text" name="nama_pemesan" id="nama_pemesan" 
               value="<?= htmlspecialchars($_POST['nama_pemesan'] ?? '') ?>" 
               placeholder="Masukkan nama pemesan" required>
    </div>
    
    <?php if (empty($itemsByTipe)): ?>
        <div class="alert alert-warning">Belum ada menu yang tersedia.</div>
    <?php else: ?>
        <?php foreach ($itemsByTipe as $tipe => $group): ?>
            <div style="margin-bottom: 30px;">
                <div class="category-title">
                    <h3><i class="fas fa-tag"></i> <?= htmlspecialchars(ucfirst($tipe)) ?></h3>
                </div>
                <div class="product-grid">
                    <?php foreach ($group as $item): 
                        $id_barang = (int)$item['id_barang'];
                        $qtyVal = $_SESSION['keranjang_menu'][$id_barang] ?? 0;
                    ?>
                        <div class="product-card">
                            <div class="product-name"><?= htmlspecialchars($item['nama_barang']) ?></div>
                            <div class="product-price"><?= rupiah($item['harga']) ?></div>
                            <input type="number" name="qty[<?= $id_barang ?>]" 
                                   value="<?= $qtyVal ?>" min="0" class="product-qty">
                            <?php if ($qtyVal > 0): ?>
                                <div class="product-subtotal">= <?= rupiah($item['harga'] * $qtyVal) ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <div style="display: flex; gap: 12px; margin-top: 20px; flex-wrap: wrap;">
        <button type="submit" name="add_to_keranjang" value="1" class="btn btn-primary">
            <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
        </button>
        <button type="submit" name="submit_transaksi" value="1" class="btn btn-success">
            <i class="fas fa-save"></i> Simpan Transaksi
        </button>
        <a href="?action=refresh" class="btn btn-danger" onclick="return confirm('Yakin ingin mengosongkan keranjang?')">
            <i class="fas fa-trash"></i> Kosongkan Keranjang
        </a>
    </div>
</form>

<?php if (!empty($keranjangItems)): ?>
    <hr>
    <h2><i class="fas fa-shopping-cart"></i> Keranjang Pesanan</h2>
    <div class="table-wrapper">
        <table class="table-striped">
            <thead>
                <tr>
                    <th>Menu</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($keranjangItems as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['nama']) ?></div>
                    <td><?= rupiah($item['harga']) ?></div>
                    <td><?= $item['qty'] ?></div>
                    <td><?= rupiah($item['subtotal']) ?></div>
                    <td>
                        <a href="?action=hapus&id=<?= $item['id'] ?>" class="action-link action-link-danger" onclick="return confirm('Hapus item ini?')">
                            <i class="fas fa-trash"></i> Hapus
                        </a>
                     </div>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background: #f8fafc; font-weight: bold;">
                    <td colspan="3"><strong>Total Keseluruhan</strong></div>
                    <td colspan="2"><strong><?= rupiah($totalSementara) ?></strong></div>
                </tr>
            </tfoot>
        </table>
    </div>
<?php endif; ?>

<style>
.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 16px;
    margin-top: 12px;
}
.product-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 12px;
    background: white;
    transition: box-shadow 0.2s;
}
.product-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.product-name {
    font-weight: 600;
    margin-bottom: 8px;
}
.product-price {
    color: #e67e22;
    font-weight: 700;
    margin-bottom: 8px;
}
.product-qty {
    width: 100%;
    padding: 6px;
    border: 1px solid #ccc;
    border-radius: 4px;
    margin: 8px 0;
}
.product-subtotal {
    font-size: 12px;
    color: #27ae60;
    text-align: right;
}
.alert {
    padding: 12px;
    border-radius: 6px;
    margin-bottom: 16px;
}
.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}
.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
.alert-warning {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
}
.btn {
    padding: 10px 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.btn-primary {
    background: #3498db;
    color: white;
}
.btn-success {
    background: #27ae60;
    color: white;
}
.btn-danger {
    background: #e74c3c;
    color: white;
}
</style>

<?php include '../../includes/footer.php'; ?>