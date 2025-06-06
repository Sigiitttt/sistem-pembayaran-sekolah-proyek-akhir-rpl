<?php
session_start();
require_once '../services/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_type = $_POST['login_type'] ?? 'siswa';
    
    if ($login_type === 'siswa') {
        // Student login
        $nama = $_POST['nama'] ?? '';
        $nisn = $_POST['nisn'] ?? '';
        
        // Validate student credentials
        $sql = "SELECT * FROM siswa WHERE nama = ? AND nisn = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nama, $nisn);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $siswa = $result->fetch_assoc();
            $_SESSION['siswa_id'] = $siswa['id'];
            $_SESSION['siswa_nama'] = $siswa['nama'];
            $_SESSION['siswa_nisn'] = $siswa['nisn'];
            $_SESSION['siswa_kelas'] = $siswa['kelas'];
            
            header("Location: ../siswa/index.php");
            exit();
        } else {
            header("Location: ../siswa/login.php?error=1");
            exit();
        }
    } else {
        // Admin login
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Validate admin credentials
        $sql = "SELECT * FROM admin WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            if ($password === $admin['password']) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_nama'] = $admin['nama'];
                $_SESSION['admin_level'] = $admin['level'];
                
                header("Location: ../admin/admin.php");
                exit();
            }
        }
        
        header("Location: ../login.php?error=1");
        exit();
    }
} else {
    header("Location: ../login.php");
    exit();
}
?>