<?php
session_start();
require_once '../services/koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
$admin_nama = $_SESSION['admin_nama'] ?? 'Admin';

// --- LOGIKA CRUD ---
// Create/Update Tagihan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['simpan'])) {
    $id = $_POST['id'] ?? 0;
    $nama_tagihan = $_POST['nama_tagihan'];
    $jumlah_tagihan = $_POST['jumlah_tagihan'];
    $tanggal_tagihan = $_POST['tanggal_tagihan'];

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE tagihan SET nama_tagihan=?, jumlah_tagihan=?, tanggal_tagihan=? WHERE id=?");
        $stmt->bind_param("sdsi", $nama_tagihan, $jumlah_tagihan, $tanggal_tagihan, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO tagihan (nama_tagihan, jumlah_tagihan, tanggal_tagihan) VALUES (?, ?, ?)");
        $stmt->bind_param("sds", $nama_tagihan, $jumlah_tagihan, $tanggal_tagihan);
    }
    
    $stmt->execute();
    $stmt->close();
    header("Location: kelolatagihan.php");
    exit();
}

// Delete Tagihan
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM tagihan WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: kelolatagihan.php");
    exit();
}

// --- LOGIKA PENGAMBILAN DATA (DENGAN PENCARIAN) ---
$sql = "SELECT * FROM tagihan";
$search_term = '';
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_term = trim($_GET['search']);
    $sql .= " WHERE nama_tagihan LIKE ?";
    $search_like = "%" . $search_term . "%";
}
$sql .= " ORDER BY tanggal_tagihan DESC";

$stmt = $conn->prepare($sql);
if (!empty($search_term)) {
    $stmt->bind_param("s", $search_like);
}
$stmt->execute();
$result = $stmt->get_result();
$daftar_tagihan = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();


// Get tagihan data for edit form
$edit_tagihan = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt_edit = $conn->prepare("SELECT * FROM tagihan WHERE id=?");
    $stmt_edit->bind_param("i", $id);
    $stmt_edit->execute();
    $result_edit = $stmt_edit->get_result();
    $edit_tagihan = $result_edit->fetch_assoc();
    $stmt_edit->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Tagihan - Admin</title>
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
                <a href="kelolasiswa.php"><i class="icon fas fa-users"></i><span>Kelola Siswa</span></a>
                <a href="kelolatagihan.php" class="active"><i class="icon fas fa-file-invoice-dollar"></i><span>Kelola Tagihan</span></a>
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
                <h1><i class="fas fa-file-invoice-dollar" style="margin-right: 10px;"></i> Kelola Jenis Tagihan</h1>
            </header>

            <div id="form-container" class="content-box collapsible <?php if (!$edit_tagihan) echo 'collapsed'; ?>">
                <h2><?php echo $edit_tagihan ? 'Edit Data Tagihan' : 'Tambah Tagihan Baru'; ?></h2>
                <form method="POST" action="kelolatagihan.php" class="modern-form">
                    <input type="hidden" name="id" value="<?php echo $edit_tagihan['id'] ?? 0; ?>">
                    <div class="form-grid">
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="nama_tagihan">Nama Tagihan</label>
                            <input type="text" id="nama_tagihan" name="nama_tagihan" required value="<?php echo htmlspecialchars($edit_tagihan['nama_tagihan'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="jumlah_tagihan">Jumlah Tagihan (Rp)</label>
                            <input type="number" id="jumlah_tagihan" name="jumlah_tagihan" min="0" step="1000" required value="<?php echo htmlspecialchars($edit_tagihan['jumlah_tagihan'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="tanggal_tagihan">Tanggal Tagihan</label>
                            <input type="date" id="tanggal_tagihan" name="tanggal_tagihan" required value="<?php echo $edit_tagihan['tanggal_tagihan'] ?? ''; ?>">
                        </div>
                    </div>
                    <div class="btn-group">
                        <button type="submit" name="simpan" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                        <?php if ($edit_tagihan): ?>
                            <a href="kelolatagihan.php" class="btn btn-secondary">Batal Edit</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="content-box">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2>Daftar Jenis Tagihan</h2>
                    <button id="tombol-tambah-siswa" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Tagihan Baru</button>
                </div>
                 
                 <form action="kelolatagihan.php" method="GET" class="modern-form" style="margin-bottom: 20px;">
                    <div class="form-grid" style="align-items: flex-end;">
                        <div class="form-group">
                            <label for="search">Cari Nama Tagihan</label>
                            <input type="text" id="search" name="search" placeholder="Masukkan nama tagihan..." value="<?php echo htmlspecialchars($search_term); ?>">
                        </div>
                        <div class="form-group">
                            <div class="btn-group" style="margin-top: 0;">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
                                <?php if (!empty($search_term)): ?>
                                    <a href="kelolatagihan.php" class="btn btn-secondary">Reset</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                 </form>

                <table class="data-table">
                     <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Tagihan</th>
                            <th>Jumlah</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($daftar_tagihan)): ?>
                            <?php foreach ($daftar_tagihan as $index => $t): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($t['nama_tagihan']); ?></td>
                                <td>Rp <?php echo number_format($t['jumlah_tagihan'], 0, ',', '.'); ?></td>
                                <td><?php echo date('d M Y', strtotime($t['tanggal_tagihan'])); ?></td>
                                <td>
                                    <a href="kelolatagihan.php?edit=<?php echo $t['id']; ?>" class="btn btn-edit"><i class="fas fa-edit"></i></a>
                                    <a href="kelolatagihan.php?delete=<?php echo $t['id']; ?>" class="btn btn-delete" onclick="return confirm('Yakin ingin menghapus tagihan ini?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center;">
                                    <?php if (!empty($search_term)): ?>
                                        Tagihan dengan nama "<?php echo htmlspecialchars($search_term); ?>" tidak ditemukan.
                                    <?php else: ?>
                                        Belum ada data tagihan.
                                    <?php endif; ?>
                                </td>
                            </tr>
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
            const toggleButton = document.getElementById('tombol-tambah-siswa'); // ID ini tetap bisa dipakai

            toggleButton.addEventListener('click', function() {
                formContainer.classList.toggle('collapsed');
            });
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>