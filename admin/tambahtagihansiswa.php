<?php
session_start();
require_once '../services/koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}
$admin_nama = $_SESSION['admin_nama'] ?? 'Admin';

// --- LOGIKA CRUD ---
// Assign tagihan to siswa
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assign'])) {
    $siswa_id = $_POST['siswa_id'];
    $tagihan_id = $_POST['tagihan_id'];
    
    // Gunakan prepared statement untuk keamanan
    $stmt_check = $conn->prepare("SELECT id FROM tagihan_siswa WHERE siswa_id=? AND tagihan_id=?");
    $stmt_check->bind_param("ii", $siswa_id, $tagihan_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows == 0) {
        $stmt_insert = $conn->prepare("INSERT INTO tagihan_siswa (tagihan_id, siswa_id, status) VALUES (?, ?, 'BELUM LUNAS')");
        $stmt_insert->bind_param("ii", $tagihan_id, $siswa_id);
        $stmt_insert->execute();
        $stmt_insert->close();
    }
    $stmt_check->close();
    header("Location: tambahtagihansiswa.php");
    exit();
}

// Remove tagihan from siswa
if (isset($_GET['remove'])) {
    $id = $_GET['remove'];
    $stmt = $conn->prepare("DELETE FROM tagihan_siswa WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: tambahtagihansiswa.php");
    exit();
}

// --- LOGIKA PENGAMBILAN DATA (DENGAN PENCARIAN & PAGINATION) ---

// 1. Pengaturan Pagination
$limit = 15; // Jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// 2. Logika Pencarian
$base_sql_count = "SELECT COUNT(ts.id) as total FROM tagihan_siswa ts JOIN siswa s ON ts.siswa_id = s.id JOIN tagihan t ON ts.tagihan_id = t.id";
$base_sql_data = "SELECT ts.id, ts.status, ts.tanggal_bayar, s.nama as siswa_nama, s.nisn, t.nama_tagihan, t.jumlah_tagihan FROM tagihan_siswa ts JOIN siswa s ON ts.siswa_id = s.id JOIN tagihan t ON ts.tagihan_id = t.id";
$where_clauses = [];
$params = [];
$types = "";

$search_term = '';
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_term = trim($_GET['search']);
    $where_clauses[] = "(s.nama LIKE ? OR s.nisn LIKE ? OR t.nama_tagihan LIKE ?)";
    $search_like = "%" . $search_term . "%";
    $params[] = $search_like;
    $params[] = $search_like;
    $params[] = $search_like;
    $types .= "sss";
}

if (!empty($where_clauses)) {
    $sql_where = " WHERE " . implode(" AND ", $where_clauses);
    $base_sql_count .= $sql_where;
    $base_sql_data .= $sql_where;
}

// 3. Hitung total data untuk pagination
$stmt_count = $conn->prepare($base_sql_count);
if (!empty($params)) {
    $stmt_count->bind_param($types, ...$params);
}
$stmt_count->execute();
$total_records = $stmt_count->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);
$stmt_count->close();

// 4. Ambil data untuk halaman saat ini
$base_sql_data .= " ORDER BY ts.id DESC LIMIT ?, ?";
$params[] = $offset;
$params[] = $limit;
$types .= "ii";

$stmt_data = $conn->prepare($base_sql_data);
if (!empty($params)) {
    $stmt_data->bind_param($types, ...$params);
}
$stmt_data->execute();
$result_data = $stmt_data->get_result();
$tagihan_siswa = $result_data->fetch_all(MYSQLI_ASSOC);
$stmt_data->close();


// Get all siswa and tagihan for dropdowns
$siswa = $conn->query("SELECT s.id, s.nama, s.nisn FROM siswa s ORDER BY s.nama")->fetch_all(MYSQLI_ASSOC);
$tagihan = $conn->query("SELECT t.id, t.nama_tagihan, t.jumlah_tagihan FROM tagihan t ORDER BY t.nama_tagihan")->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penetapan Tagihan Siswa - Admin</title>
    <link rel="stylesheet" href="style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* CSS tambahan khusus untuk pagination */
        .pagination { display: flex; justify-content: center; align-items: center; gap: 5px; margin-top: 25px; }
        .pagination a, .pagination span {
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.1);
            color: #e0e0e0;
            transition: all 0.3s ease;
        }
        .pagination a:hover { background: rgba(255, 255, 255, 0.2); }
        .pagination span.current { background: #00BFFF; color: white; font-weight: 600; }
    </style>
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
                <a href="kelolatagihan.php"><i class="icon fas fa-file-invoice-dollar"></i><span>Kelola Tagihan</span></a>
                <a href="tambahtagihansiswa.php" class="active"><i class="icon fas fa-plus-circle"></i><span>Tagihan Siswa</span></a>

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
                <h1><i class="fas fa-plus-circle" style="margin-right: 10px;"></i> Penetapan Tagihan ke Siswa</h1>
            </header>

            <div id="form-container" class="content-box collapsible collapsed">
                <h2>Tetapkan Tagihan Baru</h2>
                <form method="POST" action="tambahtagihansiswa.php" class="modern-form">
                    <input type="hidden" name="assign" value="1">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="siswa_id">Pilih Siswa</label>
                            <select id="siswa_id" name="siswa_id" required>
                                <option value="">--- Pilih Siswa ---</option>
                                <?php foreach ($siswa as $s): ?>
                                    <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['nama']) . ' (' . $s['nisn'] . ')'; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tagihan_id">Pilih Jenis Tagihan</label>
                            <select id="tagihan_id" name="tagihan_id" required>
                                <option value="">--- Pilih Tagihan ---</option>
                                <?php foreach ($tagihan as $t): ?>
                                    <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['nama_tagihan']) . ' (Rp ' . number_format($t['jumlah_tagihan']) . ')'; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="btn-group">
                        <button type="submit" name="simpan" class="btn btn-primary"><i class="fas fa-plus"></i> Tetapkan</button>
                    </div>
                </form>
            </div>

            <div class="content-box">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2>Data Tagihan Siswa</h2>
                    <button id="tombol-tambah-siswa" class="btn btn-primary"><i class="fas fa-plus"></i> Tetapkan Tagihan</button>
                </div>
                 
                 <form action="tambahtagihansiswa.php" method="GET" class="modern-form" style="margin-bottom: 20px;">
                    <div class="form-grid" style="align-items: flex-end;">
                        <div class="form-group">
                            <label for="search">Cari (Nama Siswa, NISN, atau Nama Tagihan)</label>
                            <input type="text" id="search" name="search" placeholder="Masukkan kata kunci..." value="<?php echo htmlspecialchars($search_term); ?>">
                        </div>
                        <div class="form-group">
                            <div class="btn-group" style="margin-top: 0;"><button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button><?php if (!empty($search_term)): ?><a href="tambahtagihansiswa.php" class="btn btn-secondary">Reset</a><?php endif; ?></div>
                        </div>
                    </div>
                 </form>

                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead><tr><th>No</th><th>Nama Siswa (NISN)</th><th>Tagihan</th><th>Jumlah</th><th>Status</th><th>Tgl Bayar</th><th>Aksi</th></tr></thead>
                        <tbody>
                            <?php if (!empty($tagihan_siswa)): ?>
                                <?php foreach ($tagihan_siswa as $index => $ts): ?>
                                <tr>
                                    <td><?php echo $offset + $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($ts['siswa_nama']); ?> (<?php echo htmlspecialchars($ts['nisn']); ?>)</td>
                                    <td><?php echo htmlspecialchars($ts['nama_tagihan']); ?></td>
                                    <td>Rp <?php echo number_format($ts['jumlah_tagihan'], 0, ',', '.'); ?></td>
                                    <td><span class="status-badge <?php echo $ts['status'] == 'LUNAS' ? 'status-lunas' : 'status-belum'; ?>" style="font-size: 0.8rem;"><?php echo $ts['status']; ?></span></td>
                                    <td><?php echo $ts['tanggal_bayar'] ? date('d M Y', strtotime($ts['tanggal_bayar'])) : '-'; ?></td>
                                    <td>
                                        <a href="tambahtagihansiswa.php?remove=<?php echo $ts['id']; ?>" class="btn btn-delete" onclick="return confirm('Yakin ingin membatalkan tagihan ini untuk siswa tersebut?')"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="7" style="text-align: center;"><?php if (!empty($search_term)): ?>Data tidak ditemukan untuk "<?php echo htmlspecialchars($search_term); ?>".<?php else: ?>Belum ada data.<?php endif; ?></td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <nav class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?search=<?php echo $search_term; ?>&page=<?php echo $page - 1; ?>">&laquo;</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?search=<?php echo $search_term; ?>&page=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'current' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?search=<?php echo $search_term; ?>&page=<?php echo $page + 1; ?>">&raquo;</a>
                    <?php endif; ?>
                </nav>
            </div>
        </main>
    </div>
<script src="script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formContainer = document.getElementById('form-container');
            const toggleButton = document.getElementById('tombol-tambah-siswa');
            toggleButton.addEventListener('click', function() { formContainer.classList.toggle('collapsed'); });
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>