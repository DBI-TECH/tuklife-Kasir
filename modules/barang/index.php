<?php
require_once '../../config/database.php';
require_once '../../includes/fungsi.php';
require_once '../../includes/header.php';

$query = "SELECT * FROM barang ORDER BY id_barang DESC";
$result = mysqli_query($conn, $query);
?>
<h2>Data Barang</h2>
<a href="tambah.php">+ Tambah Barang</a>
<table border="1" cellpadding="8">
    <tr>
        <th>ID</th><th>Nama Barang</th><th>Harga</th><th>Stok</th><th>Aksi</th>
    </tr>
    <?php while($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <td><?= $row['id_barang'] ?></td>
        <td><?= $row['nama_barang'] ?></td>
        <td><?= rupiah($row['harga']) ?></td>
        <td><?= $row['stok'] ?></td>
        <td>
            <a href="edit.php?id=<?= $row['id_barang'] ?>">Edit</a>
            <a href="hapus.php?id=<?= $row['id_barang'] ?>" onclick="return confirm('Yakin?')">Hapus</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
<?php include '../../includes/footer.php'; ?>