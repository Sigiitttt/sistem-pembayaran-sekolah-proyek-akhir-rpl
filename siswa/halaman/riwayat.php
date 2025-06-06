<?php
// halaman/riwayat.php

// Ambil ID siswa dari session yang sudah ada
$siswa_id = $_SESSION['siswa_id'];

// Query untuk mendapatkan riwayat pembayaran yang sudah lunas
// Variabel $conn sudah tersedia dari file index.php utama
$sql = "SELECT t.nama_tagihan, t.jumlah_tagihan, t.tanggal_tagihan, 
               ts.tanggal_bayar
        FROM tagihan_siswa ts
        JOIN tagihan t ON ts.tagihan_id = t.id
        WHERE ts.siswa_id = $siswa_id AND ts.status = 'LUNAS'
        ORDER BY ts.tanggal_bayar DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<link rel="stylesheet" href="../style/style.css">
<div class="main-content">
    <div class="history-container">
        <h2 class="history-title">Riwayat Pembayaran</h2>
        
        <?php if ($result && $result->num_rows > 0): ?>
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Nama Tagihan</th>
                        <th>Tanggal Tagihan</th>
                        <th>Jumlah</th>
                        <th>Tanggal Pembayaran</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_pembayaran = 0;
                    $jumlah_transaksi = 0;
                    $tanggal_terakhir = '';
                    
                    while ($row = $result->fetch_assoc()): 
                        $total_pembayaran += $row['jumlah_tagihan'];
                        $jumlah_transaksi++;
                        if (empty($tanggal_terakhir)) { // Simpan tanggal terbaru
                            $tanggal_terakhir = $row['tanggal_bayar'];
                        }
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nama_tagihan']); ?></td>
                        <td><?php echo date('d M Y', strtotime($row['tanggal_tagihan'])); ?></td>
                        <td>Rp <?php echo number_format($row['jumlah_tagihan'], 0, ',', '.'); ?></td>
                        <td><?php echo date('d M Y', strtotime($row['tanggal_bayar'])); ?></td>
                        <td><span class="status-badge status-lunas">LUNAS</span></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
            <div class="summary-card">
                <h3 class="summary-title">Ringkasan Pembayaran</h3>
                <div class="summary-stats">
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $jumlah_transaksi; ?></div>
                        <div class="stat-label">Total Transaksi</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">Rp <?php echo number_format($total_pembayaran, 0, ',', '.'); ?></div>
                        <div class="stat-label">Total Pembayaran</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo date('d M Y', strtotime($tanggal_terakhir)); ?></div>
                        <div class="stat-label">Pembayaran Terakhir</div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="no-data">
                <p>Belum ada riwayat pembayaran</p>
                <p>Tagihan yang sudah Anda lunasi akan muncul di sini</p>
            </div>
        <?php endif; ?>
    </div>
</div>
</html>