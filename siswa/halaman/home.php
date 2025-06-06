<?php
// FILE: halaman/tagihan.php

// Pastikan variabel sesi ada, jika tidak redirect (pengaman tambahan)
if (!isset($_SESSION['siswa_id'])) {
    exit('Sesi tidak valid.');
}
$siswa_id = $_SESSION['siswa_id'];

?>
<link rel="stylesheet" href="./style/style.css">
<section aria-label="Hero section with headline and image" class="hero">
    <div class="hero-image-container">
        <img alt="Sekolah background" src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1200&q=80">
    </div>
    
    <div class="hero-content">
        <h1>SISTEM PEMBAYARAN DIGITAL SEKOLAH</h1>
        <p class="description">
            Platform terintegrasi untuk memudahkan siswa dan orang tua dalam memantau dan membayar tagihan sekolah secara online. 
            Akses tagihan kapan saja, di mana saja, dengan fitur notifikasi dan riwayat pembayaran yang lengkap.
        </p>
    </div>
</section>

<section class="features">
    <div class="feature-card">
        <div class="feature-icon">
            <i class="fas fa-file-invoice-dollar"></i>
        </div>
        <h3 class="feature-title">Cek Tagihan</h3>
        <p class="feature-desc">Lihat detail tagihan sekolah Anda secara real-time dengan tampilan yang jelas dan mudah dipahami.</p>
    </div>
    
    <div class="feature-card">
        <div class="feature-icon">
            <i class="fas fa-history"></i>
        </div>
        <h3 class="feature-title">Riwayat Pembayaran</h3>
        <p class="feature-desc">Akses lengkap riwayat pembayaran Anda sejak awal tahun ajaran hingga saat ini.</p>
    </div>
    
    <div class="feature-card">
        <div class="feature-icon">
            <i class="fas fa-comments"></i>
        </div>
        <h3 class="feature-title">Chat Admin</h3>
        <p class="feature-desc">Tanyakan langsung ke admin sekolah jika Anda memiliki pertanyaan seputar tagihan.</p>
    </div>
</section>