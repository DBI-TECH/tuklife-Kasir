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

