<?php
require_once '../../config/database.php';
require_once '../../includes/fungsi.php';
require_once '../../includes/header.php';

session_start();

$id_transaksi = (int)($_GET['id'] ?? 0);
if ($id_transaksi <= 0) {
    header('Location: index.php');
    exit;
}

mysqli_begin_transaction($conn);
try {
    // Hapus detail dulu (FK)
    mysqli_query($conn, "DELETE FROM detail_transaksi WHERE id_transaksi=$id_transaksi");

    // Hapus transaksi
    mysqli_query($conn, "DELETE FROM transaksi WHERE id_transaksi=$id_transaksi");

    mysqli_commit($conn);
} catch (Throwable $e) {
    mysqli_rollback($conn);
}

header('Location: index.php');
exit;

