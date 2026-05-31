<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Aplikasi Kasir - Tuklife</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>
    <nav>
        <div class="nav-container">
            <div class="logo">
                <img src="<?= BASE_URL ?>assets/img/logop2.png" style="height: 90px; width: auto; object-fit: contain;" alt="logo tuklife">
            </div>
            <div class="nav-links">
                <a href="<?= BASE_URL ?>index.php" class="<?= (basename($_SERVER['REQUEST_URI']) == 'index.php' && strpos($_SERVER['REQUEST_URI'], 'modules') === false) ? 'active' : '' ?>">
                    Beranda
                </a>
                <a href="<?= BASE_URL ?>modules/stok-bahan/index.php" class="<?= strpos($_SERVER['REQUEST_URI'], 'stok-bahan') !== false ? 'active' : '' ?>">
                    Stok Bahan
                </a>
                <a href="<?= BASE_URL ?>modules/menu/index.php" class="<?= (strpos($_SERVER['REQUEST_URI'], 'modules/menu') !== false && strpos($_SERVER['REQUEST_URI'], 'kalkulator') === false) ? 'active' : '' ?>">
                    Data Menu
                </a>
                <a href="<?= BASE_URL ?>modules/transaksi/index.php" class="<?= (strpos($_SERVER['REQUEST_URI'], 'modules/transaksi') !== false && strpos($_SERVER['REQUEST_URI'], 'kalkulator') === false) ? 'active' : '' ?>">
                    Transaksi
                </a>
                <a href="<?= BASE_URL ?>modules/kalkulator/index.php" class="<?= strpos($_SERVER['REQUEST_URI'], 'kalkulator') !== false ? 'active' : '' ?>">
                    Kalkulator Menu
                </a>
            </div>
        </div>
    </nav>
    <main>