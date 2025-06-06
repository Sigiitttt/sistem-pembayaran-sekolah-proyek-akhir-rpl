<?php
session_start();
require_once '../services/koneksi.php';

// Fungsi untuk redirect dengan pesan error yang jelas
function redirect_with_error($message) {
    // PERBAIKAN: Mengarahkan kembali ke halaman login yang benar
    header("Location: ../siswa/login.php?error=" . urlencode($message));
    exit();
}

// Pastikan ini adalah request POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../siswa/login.php');
    exit();
}

$login_type = $_POST['login_type'] ?? '';

if ($login_type === 'admin') {
    // --- PROSES LOGIN ADMIN ---
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        redirect_with_error("Username dan password admin wajib diisi.");
    }

    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        
        // =================================================================
        // === PERBAIKAN PALING PENTING: Gunakan password_verify() ===
        // =================================================================
        // Ini akan berhasil untuk password yang di-hash (dari registrasi)
        // dan juga untuk password teks biasa yang lama (meskipun tidak aman)
        if (password_verify($password, $admin['password']) || $password === $admin['password']) {
            // Jika password benar, buat session
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_nama'] = $admin['nama'];
            $_SESSION['admin_level'] = $admin['level'];
            
            // Arahkan ke dashboard admin
            header("Location: ../admin/admin.php");
            exit();
        }
    }
    
    // Jika username tidak ditemukan atau password salah
    redirect_with_error("Username atau password admin salah.");

} elseif ($login_type === 'siswa') {
    // --- PROSES LOGIN SISWA ---
    $nama = $_POST['nama'] ?? '';
    $nisn = $_POST['nisn'] ?? '';

    if (empty($nama) || empty($nisn)) {
        redirect_with_error("Nama dan NISN siswa wajib diisi.");
    }

    $sql = "SELECT * FROM siswa WHERE nama = ? AND nisn = ?";
    $stmt = $conn->prepare($sql);
    
    // PENYEMPURNAAN: Menganggap NISN sebagai string ('s') agar lebih aman dan fleksibel
    $stmt->bind_param("ss", $nama, $nisn); 
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $siswa = $result->fetch_assoc();
        $_SESSION['siswa_id'] = $siswa['id'];
        $_SESSION['siswa_nama'] = $siswa['nama'];
        // Anda bisa menambahkan session lain jika perlu
        
        header("Location: ../siswa/index.php");
        exit();
    } else {
        redirect_with_error("Nama atau NISN siswa salah.");
    }

} else {
    // Jika tipe login tidak valid
    redirect_with_error("Tipe login tidak valid.");
}

$stmt->close();
$conn->close();
?>