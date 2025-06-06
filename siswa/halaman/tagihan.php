<?php
// FILE: halaman/tagihan.php

// Pastikan variabel sesi ada, jika tidak redirect (pengaman tambahan)
if (!isset($_SESSION['siswa_id'])) {
    exit('Sesi tidak valid.');
}
$siswa_id = $_SESSION['siswa_id'];

// Query HANYA untuk menampilkan daftar tagihan
$sql_list = "SELECT ts.id as id_tagihan_siswa, t.nama_tagihan, t.jumlah_tagihan, t.tanggal_tagihan, 
             ts.status, ts.tanggal_bayar
             FROM tagihan_siswa ts
             JOIN tagihan t ON ts.tagihan_id = t.id
             WHERE ts.siswa_id = ?";

$stmt_list = $conn->prepare($sql_list);
$stmt_list->bind_param("i", $siswa_id);
$stmt_list->execute();
$result = $stmt_list->get_result();
?>

<div class="main-content">
    <div class="tagihan-container">
        <h2 class="tagihan-title">Daftar Tagihan</h2>
            <?php if ($result && $result->num_rows > 0): ?>
                <div style="overflow-x: auto; position: relative;">
                    <table class="tagihan-table">
                        <thead>
                            <tr>
                                <th>Nama Tagihan</th>
                                <th>Jumlah</th>
                                <th>Tanggal Tagihan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $total_belum_lunas = 0;
                            while ($row = $result->fetch_assoc()): 
                                if ($row['status'] == 'BELUM LUNAS') {
                                    $total_belum_lunas += $row['jumlah_tagihan'];
                                }
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['nama_tagihan']); ?></td>
                                <td>Rp <?php echo number_format($row['jumlah_tagihan'], 0, ',', '.'); ?></td>
                                <td><?php echo date('d M Y', strtotime($row['tanggal_tagihan'])); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $row['status'] == 'LUNAS' ? 'status-lunas' : 'status-belum'; ?>">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($row['status'] == 'BELUM LUNAS'): ?>
                                        <form method="POST" action="index.php?halaman=tagihan" style="margin: 0;">
                                            <input type="hidden" name="id_tagihan_siswa" value="<?php echo $row['id_tagihan_siswa']; ?>">
                                            <button type="submit" name="pay_now" class="btn-bayar">Bayar</button>
                                        </form>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                        <tfoot>
                            <tr style="background-color: rgba(220, 53, 69, 0.2); font-weight: bold; color: #ff5252;">
                                <td colspan="4" style="text-align: right;">Total Tagihan Belum Lunas</td>
                                <td>Rp <?php echo number_format($total_belum_lunas, 0, ',', '.'); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-data">
                    <p>Tidak ada tagihan untuk saat ini.</p>
                </div>
            <?php endif; ?>
    </div>
</div>