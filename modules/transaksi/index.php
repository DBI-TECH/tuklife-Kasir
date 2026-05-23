<?php
include '../../config/database.php';
include '../../includes/header.php';
$query = "SELECT * FROM transaksi ORDER BY tgl_transaksi DESC";
$result = mysqli_query($conn, $query);
?>
<h2>Daftar Transaksi</h2>
<a href="tambah.php">+ Transaksi Baru</a>
<table border="1">
    <tr>
        <th>ID Transaksi</th><th>Tanggal</th><th>Total</th><th>Detail</th>
    </tr>
    <?php while($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <td><?= $row['id_transaksi'] ?></td>
        <td><?= $row['tgl_transaksi'] ?></td>
        <td><?= rupiah($row['total']) ?></td>
        <td><a href="detail.php?id=<?= $row['id_transaksi'] ?>">Lihat</a></td>
    </tr>
    <?php endwhile; ?>
</table>
<?php include '../../includes/footer.php'; ?>