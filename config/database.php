<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'kasir_db';

define('BASE_URL', '/client-dbi/tuklifeKasir/');

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>