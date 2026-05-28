<?php
include '../../config/database.php';
include '../../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama = mysqli_real_escape_string($conn, $_POST['nama_stok']);
    $stok = (int)$_POST['stok'];

    $query = "INSERT INTO stok (nama_stok, stok)
              VALUES ('$nama', $stok)";

    mysqli_query($conn, $query);

    header("Location: index.php");
    exit;
}
?>

<h2>Tambah Stok Bahan</h2>
<a href="index.php">Kembali ke Data Stok</a>

<form method="POST">
    Nama Stok:
    <input type="text" name="nama_stok" required><br>

    Jumlah Stok:
    <input type="number" name="stok" required><br>

    <button type="submit">Simpan</button>
</form>

<?php include '../../includes/footer.php'; ?>