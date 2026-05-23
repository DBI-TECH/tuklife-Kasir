<?php
require_once '../../config/database.php';
require_once '../../includes/header.php';
$hasil = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $angka1 = $_POST['angka1'];
    $angka2 = $_POST['angka2'];
    $op = $_POST['operator'];
    switch($op) {
        case '+': $hasil = $angka1 + $angka2; break;
        case '-': $hasil = $angka1 - $angka2; break;
        case '*': $hasil = $angka1 * $angka2; break;
        case '/': $hasil = $angka1 / $angka2; break;
        default: $hasil = "Error";
    }
}
?>
<h2>Kalkulator Menu Kasir</h2>
<form method="POST">
    <input type="number" name="angka1" required>
    <select name="operator">
        <option>+</option><option>-</option><option>*</option><option>/</option>
    </select>
    <input type="number" name="angka2" required>
    <button type="submit">=</button>
    <strong>Hasil: <?= $hasil ?></strong>
</form>
<p><i>Fungsi tambahan: hitung diskon, total bayar, kembalian bisa ditambahkan di sini.</i></p>
<?php include '../../includes/footer.php'; ?>