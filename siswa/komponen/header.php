<?php
// session_start();
if (!isset($_SESSION['siswa_id'])) {
    header("Location: ./login.php"); // SALAH JIKA login.php ADA DI DALAM 'siswa'
    exit();
}

// Get student name from session
$siswa_nama = $_SESSION['siswa_nama'] ?? '';

// Menentukan halaman aktif untuk memberikan style pada navigasi
if (isset($_GET['halaman'])) {
    $halaman_aktif = $_GET['halaman'];
} else {
    $halaman_aktif = 'home';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $judul_halaman; ?> - Sistem Tagihan Sekolah</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Merriweather&amp;family=Open+Sans&amp;display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="style.css">

</head>
<body>
    <div aria-label="Sistem Tagihan Sekolah" class="app-window" role="main">
        <div aria-hidden="true" class="top-bar">
            <div aria-label="Window controls" class="window-controls">
                <div class="window-control red"></div>
                <div class="window-control yellow"></div>
                <div class="window-control green"></div>
            </div>
        </div>
        
        <nav aria-label="Secondary navigation" class="top-nav">
            <div class="nav-links">
                <a href="index.php?halaman=home" class="<?php echo ($halaman_aktif == 'home') ? 'active' : ''; ?>">Home</a>
                <a href="index.php?halaman=tagihan" class="<?php echo ($halaman_aktif == 'tagihan') ? 'active' : ''; ?>">Tagihan</a>
                <a href="index.php?halaman=riwayat" class="<?php echo ($halaman_aktif == 'riwayat') ? 'active' : ''; ?>">Riwayat</a>
            </div>
            <div class="user-section">
                <span class="user-name"><?php echo htmlspecialchars($siswa_nama); ?></span>
                <button class="logout-btn" onclick="window.location.href='./logout.php'">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </div>
        </nav>
        
        <header>
            <a aria-label="Sekolah home link" class="logo" href="index.php?halaman=home">
                <img src="https://smkntrucukbjn.sch.id/wp-content/uploads/2021/03/cropped-TRUCUK-EVO-2-min.png" alt="Logo Sekolah">
            </a>
        </header>

        <main>
    