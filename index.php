<?php
include 'config/database.php';
include 'includes/header.php';
include 'includes/fungsi.php';

// Get statistics
$totalBarang = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM barang"));
$totalTransaksi = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transaksi"));
$totalStok = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM stok"));

// Get today's transactions
$today = date('Y-m-d');
$queryHariIni = "SELECT COUNT(*) as total, COALESCE(SUM(total), 0) as pendapatan FROM transaksi WHERE DATE(tgl_transaksi) = '$today'";
$result = mysqli_query($conn, $queryHariIni);
$dataHariIni = mysqli_fetch_assoc($result);

// Get recent transactions
$recentTrans = mysqli_query($conn, "SELECT * FROM transaksi ORDER BY id_transaksi DESC LIMIT 5");
?>

<h1><i class="fas fa-chalkboard-user"></i> Selamat Datang di Sistem Kasir Tuklife</h1>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?= $totalBarang ?></div>
        <div class="stat-label"><i class="fas fa-utensils"></i> Total Menu</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $totalTransaksi ?></div>
        <div class="stat-label"><i class="fas fa-receipt"></i> Total Transaksi</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $totalStok ?></div>
        <div class="stat-label"><i class="fas fa-boxes"></i> Jenis Bahan Stok</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $dataHariIni['total'] ?></div>
        <div class="stat-label"><i class="fas fa-calendar-day"></i> Transaksi Hari Ini</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= rupiah($dataHariIni['pendapatan']) ?></div>
        <div class="stat-label"><i class="fas fa-money-bill-wave"></i> Pendapatan Hari Ini</div>
    </div>
</div>

<div class="action-buttons">
    <a href="<?= BASE_URL ?>modules/kalkulator/index.php" class="btn btn-primary">
        <i class="fas fa-calculator"></i> Mulai Transaksi Baru
    </a>
    <a href="<?= BASE_URL ?>modules/menu/index.php" class="btn btn-success">
        <i class="fas fa-utensils"></i> Kelola Menu
    </a>
    <a href="<?= BASE_URL ?>modules/stok-bahan/index.php" class="btn btn-secondary">
        <i class="fas fa-boxes"></i> Kelola Stok
    </a>
</div>

<?php include 'includes/footer.php'; ?>