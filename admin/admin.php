<?php
session_start();
require_once '../services/koneksi.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../siswa/login.php?error=1");
    exit();
}
$admin_nama = $_SESSION['admin_nama'] ?? 'Admin';
$admin_level = $_SESSION['admin_level'] ?? 'N/A';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <div class="background-container">
        <div class="aurora aurora-1"></div>
        <div class="aurora aurora-2"></div>
    </div>

    <div class="dashboard-layout">
        <nav class="sidebar">
            <div class="sidebar-header">
                <img src="https://smkntrucukbjn.sch.id/wp-content/uploads/2021/03/cropped-TRUCUK-EVO-2-min.png" alt="Logo Sekolah">
                <h2>Admin Panel</h2>
            </div>
            <div class="sidebar-nav">
                <a href="#" class="active"><i class="icon fas fa-tachometer-alt"></i><span>Dashboard</span></a>
                <a href="kelolasiswa.php"><i class="icon fas fa-users"></i><span>Kelola Siswa</span></a>
                <a href="kelolatagihan.php"><i class="icon fas fa-file-invoice-dollar"></i><span>Kelola Tagihan</span></a>
                <a href="tambahtagihansiswa.php"><i class="icon fas fa-plus-circle"></i><span>Tagihan Siswa</span></a>

            </div>
          
            <div class="user-profile">
                <div class="theme-switcher">
                    <i class="fas fa-moon"></i>
                    <label class="toggle-switch">
                        <input type="checkbox" id="theme-toggle">
                        <span class="slider"></span>
                    </label>
                    <i class="fas fa-sun"></i>
                </div>
                <a href="../siswa/logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>


        <main class="main-content">
            <header class="main-header">
                <div>
                    <h1>Selamat Datang, <?php echo htmlspecialchars($admin_nama); ?>!</h1>
                    <p>Anda login sebagai: <?php echo htmlspecialchars($admin_level); ?></p>
                </div>
            </header>

            <section class="dashboard-cards">
                <div class="card" onclick="window.location.href='kelolasiswa.php'">
                    <div class="card-content">
                        <i class="card-icon fas fa-users"></i>
                        <h3>Total Siswa</h3>
                        <?php
                        $sql = "SELECT COUNT(*) as total FROM siswa";
                        $result = $conn->query($sql);
                        $row = $result->fetch_assoc();
                        ?>
                        <div class="value"><?php echo $row['total']; ?></div>
                        <span class="action-link">Lihat Detail &rarr;</span>
                    </div>
                </div>
                
                <div class="card" onclick="window.location.href='kelolatagihan.php'">
                    <div class="card-content">
                        <i class="card-icon fas fa-file-invoice-dollar"></i>
                        <h3>Jenis Tagihan</h3>
                         <?php
                        $sql = "SELECT COUNT(*) as total FROM tagihan";
                        $result = $conn->query($sql);
                        $row = $result->fetch_assoc();
                        ?>
                        <div class="value"><?php echo $row['total']; ?></div>
                        <span class="action-link">Lihat Detail &rarr;</span>
                    </div>
                </div>
                
                <div class="card" onclick="window.location.href='tambahtagihansiswa.php'">
                     <div class="card-content">
                        <i class="card-icon fas fa-plus-circle"></i>
                        <h3>Penetapan Tagihan</h3>
                        <?php
                        $sql = "SELECT COUNT(DISTINCT siswa_id) as total FROM tagihan_siswa";
                        $result = $conn->query($sql);
                        $row = $result->fetch_assoc();
                        ?>
                        <div class="value"><?php echo $row['total']; ?> Siswa</div>
                        <span class="action-link">Lihat Detail &rarr;</span>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const cards = document.querySelectorAll('.card');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry, index) => {
                    if (entry.isIntersecting) {
                        // Tambahkan delay berdasarkan urutan kartu
                        entry.target.style.animationDelay = `${index * 100}ms`;
                        entry.target.classList.add('fade-in-visible'); // Ganti nama class agar tidak sama dengan keyframe
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });

            cards.forEach(card => {
                observer.observe(card);
            });
        });

          
    </script>
<script src="script.js"></script>


</body>
</html>
<?php $conn->close(); ?>