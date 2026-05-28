<?php
include '../../config/database.php';
include '../../includes/fungsi.php';
include '../../includes/header.php';

$query = "SELECT * FROM transaksi ORDER BY id_transaksi ASC";
$result = mysqli_query($conn, $query);
?>

<h2>Daftar Transaksi</h2>

<table border="1">
    <tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Nama Pemesan</th>
        <th>Total</th>
        <th>Detail</th>
        <th>Aksi</th>
    </tr>

    <?php 
    $no = 1;
    while($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <td><?= $no++ ?></td>
        <td><?= $row['tgl_transaksi'] ?></td>
        <td><?= htmlspecialchars($row['nama_pemesan'] ?? '') ?></td>
        <td><?= rupiah($row['total']) ?></td>
        <td><a href="detail.php?id=<?= $row['id_transaksi'] ?>">Lihat</a></td>
        <td>
            <a href="hapus.php?id=<?= $row['id_transaksi'] ?>" onclick="return confirm('Yakin hapus transaksi ini?')">Hapus</a>
        </td>
    </tr>

    <?php endwhile; ?>
</table>

<?php include '../../includes/footer.php'; ?>

