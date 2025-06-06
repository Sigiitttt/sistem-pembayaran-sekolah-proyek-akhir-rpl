<?php
session_start();
require_once '../services/koneksi.php';

// ==========================================================
// === PERBAIKAN PATH ADA DI BARIS INI ===
// ==========================================================
// Menggunakan path sesuai struktur folder Anda
require_once '../midtrans-php-master/Midtrans.php'; 

// 1. Ambil data transaksi dari session
if (!isset($_SESSION['midtrans_transaction'])) {
    header('Location: ../siswa/index.php?halaman=tagihan');
    exit();
}
$transaction_data = $_SESSION['midtrans_transaction'];

// 2. Ambil pengaturan Midtrans dari database
$pengaturan_db = $conn->query("SELECT * FROM pengaturan WHERE setting_name LIKE 'midtrans_%'")->fetch_all(MYSQLI_ASSOC);
$settings = [];
foreach ($pengaturan_db as $p) {
    $settings[$p['setting_name']] = $p['setting_value'];
}

$is_production = ($settings['midtrans_environment'] ?? 'sandbox') == 'production';
$server_key = $settings['midtrans_server_key'] ?? '';
$client_key = $settings['midtrans_client_key'] ?? '';

// Pengecekan terakhir jika kunci tetap kosong
if (empty($server_key) || empty($client_key)) {
    die("Kunci Midtrans belum diatur dengan benar di database Anda.");
}

// 3. Konfigurasi Midtrans
\Midtrans\Config::$serverKey = $server_key;
\Midtrans\Config::$isProduction = $is_production;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// 4. Siapkan parameter untuk Midtrans
$params = [
    'transaction_details' => [
        'order_id' => $transaction_data['order_id'],
        'gross_amount' => $transaction_data['amount'],
    ],
    'item_details' => [[
        'id' => $transaction_data['id_tagihan_siswa'],
        'price' => $transaction_data['amount'],
        'quantity' => 1,
        'name' => $transaction_data['nama_tagihan']
    ]],
    'customer_details' => [
        'first_name' => $transaction_data['nama_siswa'],
        'last_name' => '(NISN: ' . $transaction_data['nisn'] . ')',
    ],
];

// 5. Dapatkan Snap Token
$snapToken = null;
try {
    $snapToken = \Midtrans\Snap::getSnapToken($params);
} catch (Exception $e) {
    echo 'Error saat membuat Snap Token: ' . $e->getMessage();
    unset($_SESSION['midtrans_transaction']);
    echo '<br><a href="../siswa/index.php?halaman=tagihan">Kembali ke Tagihan</a>';
    exit();
}

// Hapus sesi setelah token didapat
unset($_SESSION['midtrans_transaction']);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proses Pembayaran</title>
    <script type="text/javascript"
            src="<?php echo $is_production ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js'; ?>"
            data-client-key="<?php echo $client_key; ?>"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #1a1a2e; color: white; flex-direction: column; }
        .container { text-align: center; }
        #pay-button { background: #00BFFF; color: white; border: none; padding: 15px 30px; font-size: 16px; border-radius: 8px; cursor: pointer; transition: all 0.3s; }
        #pay-button:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0, 191, 255, 0.4); }
    </style>
</head>
<body>
    <div class="container">
        <h1>Mempersiapkan Pembayaran...</h1>
        <p>Anda akan diarahkan ke halaman pembayaran Midtrans.</p>
        <br>
        <button id="pay-button">Klik di sini jika tidak otomatis</button>
    </div>

    <script type="text/javascript">
      var payButton = document.getElementById('pay-button');
      function pay() {
        if (!'<?php echo $snapToken; ?>') {
            alert('Gagal membuat token pembayaran. Silakan coba lagi.');
            return;
        }
        window.snap.pay('<?php echo $snapToken; ?>', {
          onSuccess: function(result){
            alert("Pembayaran sukses!"); 
            window.location.href = '../siswa/index.php?halaman=riwayat';
          },
          onPending: function(result){
            alert("Pembayaran Anda sedang diproses."); 
            window.location.href = '../siswa/index.php?halaman=riwayat';
          },
          onError: function(result){
            alert("Pembayaran gagal!"); 
            window.location.href = '../siswa/index.php?halaman=tagihan';
          },
          onClose: function(){
            alert('Anda menutup popup pembayaran.');
            window.location.href = '../siswa/index.php?halaman=tagihan';
          }
        })
      }
      // Panggil fungsi pay secara otomatis
      pay();
      // Jadikan tombol sebagai cadangan
      payButton.addEventListener('click', pay);
    </script>
</body>
</html>