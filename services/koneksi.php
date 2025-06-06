<?php
$servername = "localhost";
$username = "root";
$password = "1";
$dbname = "rpl2";

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);
// --- TAMBAHKAN KODE DEBUG DI SINI ---
// $result_db_name = $conn->query("SELECT DATABASE()");
// $current_db_name = $result_db_name->fetch_row()[0];
// die("DEBUG: Saat ini skrip terhubung ke database bernama: " . $current_db_name);
// --- AKHIR KODE DEBUG ---
// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>