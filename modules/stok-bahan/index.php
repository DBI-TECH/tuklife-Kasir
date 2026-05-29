<?php
require_once '../../config/database.php';
require_once '../../includes/fungsi.php';
require_once '../../includes/header.php';

$query = "SELECT * FROM stok ORDER BY id_stok ASC";
$result = mysqli_query($conn, $query);
?>
<<<<<<< HEAD

<h2><i class="fas fa-boxes"></i> Data Stok Bahan</h2>

<div class="action-buttons" style="margin-bottom: 20px;">
    <a href="tambah.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Stok
    </a>
</div>

<div class="table-wrapper">
    <table class="table-striped">
        <thead>
            <tr>
                <th width="50">No</th>
                <th>Nama Barang</th>
                <th>Stok</th>
                <th width="120">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            if(mysqli_num_rows($result) > 0):
                while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['nama_stok'] ?? '') ?></td>
                    <td>
                        <span class="badge <?= ($row['stok'] <= 10) ? 'badge-danger' : 'badge-success' ?>">
                            <?= $row['stok'] ?? 0 ?> unit
                        </span>
                    </td>
                    <td>
                        <a href="edit.php?id=<?= $row['id_stok'] ?>" class="action-link">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="hapus.php?id=<?= $row['id_stok'] ?>" class="action-link danger" onclick="return confirm('Yakin ingin menghapus stok ini?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; 
            else: ?>
                <tr>
                    <td colspan="4" style="text-align: center;">Belum ada data stok</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

=======
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
>>>>>>> f9fdb3fd35fab447ae2718eaa32767cdbe800f7d
<?php include '../../includes/footer.php'; ?>