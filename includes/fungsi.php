<?php
function rupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

function ambilSemuaBarang($conn) {
    $query = "SELECT * FROM barang";
    return mysqli_query($conn, $query);
}
// Tambahkan fungsi lain sesuai kebutuhan
?>