<?php
// FILE: siswa/index.php

// Tampilkan semua error untuk debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start(); // Mulai sesi di paling atas
require_once '../services/koneksi.php'; // Hubungkan ke database

// ===================================================================
// === LOGIKA PROSES PEMBAYARAN SEKARANG ADA DI SINI ===
// ===================================================================
// Hanya proses jika ada permintaan POST dari form "Bayar"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_now'])) {
    
    // Pastikan user sudah login sebelum memproses
    if (!isset($_SESSION['siswa_id'])) {
        header("Location: login.php");
        exit();
    }

    $id_tagihan_siswa = $_POST['id_tagihan_siswa'];
    
    // Dapatkan detail tagihan yang akan dibayar
    $sql_tagihan = "SELECT ts.*, t.nama_tagihan, t.jumlah_tagihan, s.nama, s.nisn 
                    FROM tagihan_siswa ts
                    JOIN tagihan t ON ts.tagihan_id = t.id
                    JOIN siswa s ON ts.siswa_id = s.id
                    WHERE ts.id = ?";
    
    $stmt_detail = $conn->prepare($sql_tagihan);
    $stmt_detail->bind_param("i", $id_tagihan_siswa);
    $stmt_detail->execute();
    $tagihan_result = $stmt_detail->get_result();
    $tagihan = $tagihan_result->fetch_assoc();
    
    if ($tagihan) {
        $order_id = 'TAGIHAN-' . time() . '-' . $id_tagihan_siswa;
        
        $_SESSION['midtrans_transaction'] = [
            'order_id' => $order_id,
            'id_tagihan_siswa' => $id_tagihan_siswa,
            'amount' => $tagihan['jumlah_tagihan'],
            'nama_siswa' => $tagihan['nama'],
            'nisn' => $tagihan['nisn'],
            'nama_tagihan' => $tagihan['nama_tagihan']
        ];
        
        // Karena logika ada di atas sebelum HTML, redirect ini PASTI BERHASIL
        header("Location: ../proses/proses_pembayaran.php");
        exit();
    } else {
        die("ERROR: Gagal menemukan detail tagihan.");
    }
}
// === AKHIR DARI BLOK LOGIKA PEMBAYARAN ===


// Kode untuk menampilkan halaman (tidak berubah)
define('BASE_URL', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/');
$halaman = $_GET['halaman'] ?? 'home';
$judul_halaman = ucfirst($halaman);

require_once 'komponen/header.php'; // HTML mulai dikirim dari sini

$file_halaman = "halaman/{$halaman}.php";
if (file_exists($file_halaman)) {
    require_once $file_halaman;
} else {
    echo "<div style='text-align:center; padding: 50px;'><h1>404 - Halaman Tidak Ditemukan</h1></div>";
}

require_once 'komponen/footer.php';
$conn->close();
?>