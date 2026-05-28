<?php
require_once '../../config/database.php';
require_once '../../includes/fungsi.php';
require_once '../../includes/header.php';

$query = "SELECT * FROM stok ORDER BY id_stok ASC";
$result = mysqli_query($conn, $query);
?>
<h2>Data Stok Bahan</h2>
<a href="tambah.php">+ Tambah Stok</a>
<table border="1" cellpadding="8">
    <tr>
        <th>No</th><th>Nama Barang</th><th>Stok</th><th>Aksi</th>
    </tr>
    <?php 
    $no = 1;
    while($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <td><?= $no++ ?></td>
        <td><?= htmlspecialchars($row['nama_stok'] ?? '') ?></td>
        <td><?= $row['stok'] ?? 0 ?></td>
        <td>
            <a href="edit.php?id=<?= $row['id_stok'] ?>">Edit</a>
            <a href="hapus.php?id=<?= $row['id_stok'] ?>" onclick="return confirm('Yakin?')">Hapus</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
<?php include '../../includes/footer.php'; ?>