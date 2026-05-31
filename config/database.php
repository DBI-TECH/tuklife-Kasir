<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'kasir_db';

// Untuk struktur client-dbi/tuklife-Kasir
define('BASE_URL', '/client-dbi/tuklife-Kasir/');

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>