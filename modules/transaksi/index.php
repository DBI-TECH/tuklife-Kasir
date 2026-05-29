<?php
include '../../config/database.php';
include '../../includes/fungsi.php';
include '../../includes/header.php';

<<<<<<< HEAD
$query = "SELECT * FROM transaksi ORDER BY id_transaksi DESC";
$result = mysqli_query($conn, $query);
?>

<h2><i class="fas fa-receipt"></i> Daftar Transaksi</h2>

<div class="table-wrapper">
    <table class="table-striped">
        <thead>
            <tr>
                <th width="50">No</th>
                <th>Tanggal</th>
                <th>Nama Pemesan</th>
                <th>Total</th>
                <th width="100">Detail</th>
                <th width="80">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            if(mysqli_num_rows($result) > 0):
                while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($row['tgl_transaksi'])) ?></td>
                    <td><?= htmlspecialchars($row['nama_pemesan'] ?? '-') ?></td>
                    <td><strong><?= rupiah($row['total']) ?></strong></td>
                    <td>
                        <a href="detail.php?id=<?= $row['id_transaksi'] ?>" class="action-link">
                            <i class="fas fa-eye"></i> Lihat
                        </a>
                    </td>
                    <td>
                        <a href="hapus.php?id=<?= $row['id_transaksi'] ?>" class="action-link danger" onclick="return confirm('Yakin hapus transaksi ini?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; 
            else: ?>
                <tr>
                    <td colspan="6" style="text-align: center;">Belum ada transaksi</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../../includes/footer.php'; ?>
=======
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

>>>>>>> f9fdb3fd35fab447ae2718eaa32767cdbe800f7d
