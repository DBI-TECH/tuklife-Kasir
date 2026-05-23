<?php
include '../../config/database.php';
include '../../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama_barang'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $query = "INSERT INTO barang (nama_barang, harga, stok) VALUES ('$nama', $harga, $stok)";
    mysqli_query($conn, $query);
    header("Location: index.php");
    exit;
}
?>
<h2>Tambah Barang</h2>
<form method="POST">
    Nama Barang: <input type="text" name="nama_barang" required><br>
    Harga: <input type="number" name="harga" required><br>
    Stok: <input type="number" name="stok" required><br>
    <button type="submit">Simpan</button>
</form>
<?php include '../../includes/footer.php'; ?>