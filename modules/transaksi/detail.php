<?php
require_once '../../config/database.php';
require_once '../../includes/fungsi.php';
require_once '../../includes/header.php';

$id_transaksi = (int)($_GET['id'] ?? 0);

$qTrans = mysqli_query($conn, "SELECT * FROM transaksi WHERE id_transaksi=$id_transaksi");
$transaksi = mysqli_fetch_assoc($qTrans);

$qDetail = mysqli_query(
    $conn,
    "SELECT dt.id_detail, b.nama_barang, b.harga, dt.jumlah, (b.harga * dt.jumlah) AS sub_total
     FROM detail_transaksi dt
     JOIN barang b ON b.id_barang = dt.id_barang
     WHERE dt.id_transaksi = $id_transaksi"
);
?>

<<<<<<< HEAD
<h2><i class="fas fa-file-invoice"></i> Detail Transaksi</h2>

<?php if (!$transaksi): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i> Transaksi tidak ditemukan.
    </div>
<?php else: ?>
    <div style="background: var(--light); padding: 20px; border-radius: 8px; margin-bottom: 25px; border: 1px solid var(--border);">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div>
                <strong><i class="fas fa-calendar"></i> Tanggal:</strong><br>
                <?= date('d/m/Y H:i:s', strtotime($transaksi['tgl_transaksi'])) ?>
            </div>
            <div>
                <strong><i class="fas fa-user"></i> Nama Pemesan:</strong><br>
                <?= htmlspecialchars($transaksi['nama_pemesan'] ?? '-') ?>
            </div>
            <div>
                <strong><i class="fas fa-money-bill"></i> Total:</strong><br>
                <span style="font-size: 20px; color: var(--primary); font-weight: 700;"><?= rupiah($transaksi['total']) ?></span>
            </div>
        </div>
    </div>

    <h3><i class="fas fa-shopping-cart"></i> Item Pesanan</h3>
    <div class="table-wrapper">
        <table class="table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Menu</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Sub Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while($d = mysqli_fetch_assoc($qDetail)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($d['nama_barang']) ?></td>
                    <td><?= rupiah($d['harga']) ?></td>
                    <td><?= $d['jumlah'] ?></td>
                    <td><?= rupiah($d['sub_total']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr style="background: var(--light); font-weight: bold;">
                    <td colspan="4" style="text-align: right;">Grand Total:</td>
                    <td><?= rupiah($transaksi['total']) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
    
    <div class="action-buttons">
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Cetak Struk
        </button>
    </div>
<?php endif; ?>

<?php include '../../includes/footer.php'; ?>
=======
<h2>Detail Transaksi</h2>
<?php if (!$transaksi): ?>
    <p>Transaksi tidak ditemukan.</p>
<?php else: ?>
    <p><b>Tanggal:</b> <?= $transaksi['tgl_transaksi'] ?></p>
    <?php if (array_key_exists('nama_pemesan', $transaksi)): ?>
        <p><b>Nama Pemesan:</b> <?= htmlspecialchars($transaksi['nama_pemesan'] ?? '') ?></p>
    <?php endif; ?>

    <p><b>Total:</b> <?= rupiah($transaksi['total']) ?></p>

    <table border="1" cellpadding="6" cellspacing="0">
        <tr>
            <th>Menu</th>
            <th>Harga</th>
            <th>Jumlah</th>
            <th>Sub Total</th>
        </tr>
        <?php while($d = mysqli_fetch_assoc($qDetail)): ?>
        <tr>
            <td><?= htmlspecialchars($d['nama_barang']) ?></td>
            <td><?= rupiah($d['harga']) ?></td>
            <td><?= $d['jumlah'] ?></td>
            <td><?= rupiah($d['sub_total']) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
<?php endif; ?>

<?php include '../../includes/footer.php'; ?>

>>>>>>> f9fdb3fd35fab447ae2718eaa32767cdbe800f7d
