<?php
include '../../config/database.php';
include '../../includes/header.php';
session_start();
if (!isset($_SESSION['keranjang'])) $_SESSION['keranjang'] = [];

// Proses tambah item ke keranjang
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_barang'])) {
    $id_barang = $_POST['id_barang'];
    $jumlah = $_POST['jumlah'];
    $_SESSION['keranjang'][] = ['id_barang' => $id_barang, 'jumlah' => $jumlah];
}

// Proses simpan transaksi
if (isset($_GET['simpan'])) {
    $total = 0;
    foreach($_SESSION['keranjang'] as $item) {
        $q = mysqli_query($conn, "SELECT harga FROM barang WHERE id_barang=".$item['id_barang']);
        $harga = mysqli_fetch_assoc($q)['harga'];
        $total += $harga * $item['jumlah'];
    }
    $query = "INSERT INTO transaksi (total) VALUES ($total)";
    mysqli_query($conn, $query);
    $id_transaksi = mysqli_insert_id($conn);
    foreach($_SESSION['keranjang'] as $item) {
        $q = "INSERT INTO detail_transaksi (id_transaksi, id_barang, jumlah) VALUES ($id_transaksi, ".$item['id_barang'].", ".$item['jumlah'].")";
        mysqli_query($conn, $q);
    }
    unset($_SESSION['keranjang']);
    header("Location: index.php");
}
?>
<h2>Transaksi Baru</h2>
<form method="POST">
    Pilih Barang: 
    <select name="id_barang">
        <?php $barang = mysqli_query($conn, "SELECT * FROM barang"); 
        while($b = mysqli_fetch_assoc($barang)): ?>
        <option value="<?= $b['id_barang'] ?>"><?= $b['nama_barang'] ?> - <?= rupiah($b['harga']) ?></option>
        <?php endwhile; ?>
    </select>
    Jumlah: <input type="number" name="jumlah" value="1">
    <button type="submit">Tambah ke Keranjang</button>
</form>

<h3>Keranjang</h3>
<ul>
<?php foreach($_SESSION['keranjang'] as $item): ?>
    <li>Barang ID <?= $item['id_barang'] ?> x <?= $item['jumlah'] ?></li>
<?php endforeach; ?>
</ul>
<a href="?simpan=1">Selesaikan Transaksi</a>
<?php include '../../includes/footer.php'; ?>