<?php
require_once '../services/koneksi.php';
session_start();

$errors = [];

// Logika PHP Anda tidak berubah, hanya dipindahkan ke sini
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $level = $_POST['level'];

    if (empty($nama) || empty($username) || empty($password) || empty($level)) { $errors[] = "Semua kolom wajib diisi."; }
    if ($password !== $confirm_password) { $errors[] = "Password dan konfirmasi password tidak cocok."; }
    if (strlen($password) < 3) { $errors[] = "Password minimal harus 3 karakter."; }

    $stmt_check = $conn->prepare("SELECT id FROM admin WHERE username = ?");
    $stmt_check->bind_param("s", $username);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    if ($result_check->num_rows > 0) { $errors[] = "Username sudah digunakan, silakan pilih yang lain."; }
    $stmt_check->close();

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $stmt_insert = $conn->prepare("INSERT INTO admin (nama, username, password, level) VALUES (?, ?, ?, ?)");
        $stmt_insert->bind_param("ssss", $nama, $username, $hashed_password, $level);
        if ($stmt_insert->execute()) {
            header("Location: ../siswa/login.php?status=reg_success");
            exit();
        } else {
            $errors[] = "Terjadi kesalahan saat mendaftarkan akun.";
        }
        $stmt_insert->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Admin Baru</title>
    <link rel="stylesheet" href="style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="background-container">
        <div class="aurora aurora-1"></div>
        <div class="aurora aurora-2"></div>
    </div>
    <div class="auth-container">
        <div class="auth-form">
            <div class="content-box">
                <i class="icon-header fas fa-user-plus"></i>
                <h2>Buat Akun Admin</h2>

                <?php if (!empty($errors)): ?>
                    <ul class="error-box">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <form method="POST" action="register.php" class="modern-form">
                    <div class="form-group">
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" required value="<?php echo htmlspecialchars($_POST['nama'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                     <div class="form-group">
                        <label for="confirm_password">Konfirmasi Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                     <div class="form-group">
                        <label for="level">Level Akses</label>
                        <select id="level" name="level" required>
                            <option value="admin" <?php echo (($_POST['level'] ?? 'admin') == 'admin') ? 'selected' : ''; ?>>Admin</option>
                            <option value="superadmin" <?php echo (($_POST['level'] ?? '') == 'superadmin') ? 'selected' : ''; ?>>Superadmin</option>
                        </select>
                    </div>
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Daftar Akun</button>
                    </div>
                </form>
            </div>
            <div class="auth-link">
                <p>Sudah punya akun? <a href="../siswa/login.php">Login di sini</a></p>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'light') {
                document.body.classList.add('light-mode');
            }
        });
    </script>
</body>
</html>