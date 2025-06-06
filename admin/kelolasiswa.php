<?php
session_start();
require_once '../services/koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$admin_nama = $_SESSION['admin_nama'] ?? 'Admin';

// Logika PHP Anda tidak ada yang berubah sama sekali
// ... (semua logika CRUD dan pencarian Anda tetap sama) ...
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['simpan'])) {
    $id = $_POST['id'] ?? 0;
    $nama = $_POST['nama'];
    $nisn = $_POST['nisn'];
    $kelas = $_POST['kelas'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $alamat = $_POST['alamat'];

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE siswa SET nama=?, nisn=?, kelas=?, tanggal_lahir=?, alamat=? WHERE id=?");
        $stmt->bind_param("sssssi", $nama, $nisn, $kelas, $tanggal_lahir, $alamat, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO siswa (nama, nisn, kelas, tanggal_lahir, alamat) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nama, $nisn, $kelas, $tanggal_lahir, $alamat);
    }
    
    $stmt->execute();
    $stmt->close();
    header("Location: kelolasiswa.php");
    exit();
}
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM siswa WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: kelolasiswa.php");
    exit();
}

$sql = "SELECT s.*, CONCAT(j.nama_jurusan, ' (', a.tahun_masuk, ')') as nama_kelas FROM siswa s LEFT JOIN kelas k ON s.kelas = k.id LEFT JOIN jurusan j ON k.nama_jurusan = j.id LEFT JOIN angkatan a ON k.angkatan = a.id";
$search_term = '';
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_term = trim($_GET['search']);
    $sql .= " WHERE s.nama LIKE ? OR s.nisn LIKE ?";
    $search_like = "%" . $search_term . "%";
}
$sql .= " ORDER BY s.nama ASC";

$stmt = $conn->prepare($sql);
if (!empty($search_term)) {
    $stmt->bind_param("ss", $search_like, $search_like);
}
$stmt->execute();
$result = $stmt->get_result();
$daftar_siswa = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$daftar_kelas = $conn->query("SELECT k.id, CONCAT(j.nama_jurusan, ' (', a.tahun_masuk, ')') as nama_kelas FROM kelas k JOIN jurusan j ON k.nama_jurusan = j.id JOIN angkatan a ON k.angkatan = a.id")->fetch_all(MYSQLI_ASSOC);
$edit_siswa = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt_edit = $conn->prepare("SELECT * FROM siswa WHERE id=?");
    $stmt_edit->bind_param("i", $id);
    $stmt_edit->execute();
    $result_edit = $stmt_edit->get_result();
    $edit_siswa = $result_edit->fetch_assoc();
    $stmt_edit->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Siswa - Admin</title>
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
                <a href="admin.php"><i class="icon fas fa-tachometer-alt"></i><span>Dashboard</span></a>
                <a href="kelolasiswa.php" class="active"><i class="icon fas fa-users"></i><span>Kelola Siswa</span></a>
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
                <h1><i class="fas fa-users" style="margin-right: 10px;"></i> Kelola Data Siswa</h1>
            </header>

            <div id="form-container" class="content-box collapsible <?php if (!$edit_siswa) echo 'collapsed'; ?>">
                <h2><?php echo $edit_siswa ? 'Edit Data Siswa' : 'Tambah Siswa Baru'; ?></h2>
                <form method="POST" action="kelolasiswa.php" class="modern-form">
                    <input type="hidden" name="id" value="<?php echo $edit_siswa['id'] ?? 0; ?>">
                    <div class="form-grid">
                        <div class="form-group"><label for="nama">Nama Siswa</label><input type="text" id="nama" name="nama" required value="<?php echo htmlspecialchars($edit_siswa['nama'] ?? ''); ?>"></div>
                        <div class="form-group"><label for="nisn">NISN</label><input type="text" id="nisn" name="nisn" required value="<?php echo htmlspecialchars($edit_siswa['nisn'] ?? ''); ?>"></div>
                        <div class="form-group"><label for="kelas">Kelas</label><select id="kelas" name="kelas" required><option value="">Pilih Kelas</option><?php foreach ($daftar_kelas as $k): ?><option value="<?php echo $k['id']; ?>" <?php echo (isset($edit_siswa) && $edit_siswa['kelas'] == $k['id']) ? 'selected' : ''; ?>><?php echo $k['nama_kelas']; ?></option><?php endforeach; ?></select></div>
                        <div class="form-group"><label for="tanggal_lahir">Tanggal Lahir</label><input type="date" id="tanggal_lahir" name="tanggal_lahir" required value="<?php echo $edit_siswa['tanggal_lahir'] ?? ''; ?>"></div>
                        <div class="form-group" style="grid-column: 1 / -1;"><label for="alamat">Alamat</label><input type="text" id="alamat" name="alamat" required value="<?php echo htmlspecialchars($edit_siswa['alamat'] ?? ''); ?>"></div>
                    </div>
                    <div class="btn-group">
                        <button type="submit" name="simpan" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                        <?php if ($edit_siswa): ?>
                            <a href="kelolasiswa.php" class="btn btn-secondary">Batal Edit</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="content-box">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2>Daftar Siswa Terdaftar</h2>
                    <button id="tombol-tambah-siswa" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Siswa Baru</button>
                </div>
                 
                 <form action="kelolasiswa.php" method="GET" class="modern-form" style="margin-bottom: 20px;">
                    <div class="form-grid" style="align-items: flex-end;">
                        <div class="form-group"><label for="search">Cari Siswa (Nama atau NISN)</label><input type="text" id="search" name="search" placeholder="Masukkan nama atau NISN..." value="<?php echo htmlspecialchars($search_term); ?>"></div>
                        <div class="form-group"><div class="btn-group" style="margin-top: 0;"><button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button><?php if (!empty($search_term)): ?><a href="kelolasiswa.php" class="btn btn-secondary">Reset</a><?php endif; ?></div></div>
                    </div>
                 </form>

                <table class="data-table">
                     <thead><tr><th>No</th><th>Nama</th><th>NISN</th><th>Kelas</th><th>Tgl Lahir</th><th>Alamat</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php if (!empty($daftar_siswa)): ?>
                            <?php foreach ($daftar_siswa as $index => $s): ?>
                            <tr><td><?php echo $index + 1; ?></td><td><?php echo htmlspecialchars($s['nama']); ?></td><td><?php echo htmlspecialchars($s['nisn']); ?></td><td><?php echo htmlspecialchars($s['nama_kelas']); ?></td><td><?php echo date('d M Y', strtotime($s['tanggal_lahir'])); ?></td><td><?php echo htmlspecialchars($s['alamat']); ?></td><td><a href="kelolasiswa.php?edit=<?php echo $s['id']; ?>" class="btn btn-edit"><i class="fas fa-edit"></i></a><a href="kelolasiswa.php?delete=<?php echo $s['id']; ?>" class="btn btn-delete" onclick="return confirm('Yakin ingin menghapus data siswa ini?')"><i class="fas fa-trash"></i></a></td></tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" style="text-align: center;"><?php if (!empty($search_term)): ?>Siswa dengan nama atau NISN "<?php echo htmlspecialchars($search_term); ?>" tidak ditemukan.<?php else: ?>Belum ada data siswa.<?php endif; ?></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
<script src="script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formContainer = document.getElementById('form-container');
            const toggleButton = document.getElementById('tombol-tambah-siswa');

            toggleButton.addEventListener('click', function() {
                // Toggle class 'collapsed' untuk memicu animasi CSS
                formContainer.classList.toggle('collapsed');
            });

            // Jika URL berisi parameter 'edit', pastikan form tidak collapsed
            // Ini sudah ditangani oleh logika PHP di atas, tapi ini adalah
            // pengaman tambahan jika diperlukan di masa depan.
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('edit')) {
                formContainer.classList.remove('collapsed');
            }
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>