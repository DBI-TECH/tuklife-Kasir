<?php
require_once '../../config/database.php';
require_once '../../includes/fungsi.php';
require_once '../../includes/header.php';

// Get barang grouped by category (filtered to 4 categories)
$itemsByTipe = ambilBarangGroupedByTipe($conn);

// Urutkan kategori sesuai urutan yang diinginkan
$categoryOrder = ['mocktail', 'milk base', 'coffe', 'snack'];
$orderedItems = [];
foreach ($categoryOrder as $cat) {
    if (isset($itemsByTipe[$cat])) {
        $orderedItems[$cat] = $itemsByTipe[$cat];
    }
}
$itemsByTipe = $orderedItems;
?>
<h2>Daftar Menu</h2>
<a href="tambah.php">+ Tambah Menu</a>
<table border="1" cellpadding="8">
    <tr>
        <th>No</th><th>Kategori</th><th>Nama Menu</th><th>Harga</th><th>Aksi</th>
    </tr>
    <?php 
    $no = 1;
    foreach ($itemsByTipe as $kategori => $items): ?>
        <tr style="background:#eef;">
            <td colspan="5"><strong>Kategori: <?= htmlspecialchars($kategori) ?></strong></td>
        </tr>
        <?php foreach ($items as $barang): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($kategori) ?></td>
            <td><?= htmlspecialchars($barang['nama_barang'] ?? '') ?></td>
            <td><?= rupiah($barang['harga'] ?? 0) ?></td>
            <td>
                <a href="edit.php?id=<?= $barang['id_barang'] ?>">Edit</a>
                <a href="hapus.php?id=<?= $barang['id_barang'] ?>" onclick="return confirm('"'"'Yakin?'"'"')">Hapus</a>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
</table>
<?php include '../../includes/footer.php'; ?>
