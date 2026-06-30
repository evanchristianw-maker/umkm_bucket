<?php
// setup_db.php
// Script ini digunakan untuk membuat database dan mengimpor tabel secara otomatis untuk XAMPP.

$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_buket";

echo "<h3>Mengonfigurasi Database Toko Buket...</h3>";

// 1. Koneksi ke MySQL tanpa memilih database terlebih dahulu
$conn = mysqli_connect($host, $user, $pass);
if (!$conn) {
    die("Koneksi ke MySQL Gagal: " . mysqli_connect_error());
}
echo "✓ Koneksi ke MySQL berhasil.<br>";

// 2. Buat database jika belum ada
$sql_create_db = "CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
if (mysqli_query($conn, $sql_create_db)) {
    echo "✓ Database `$db` berhasil dibuat atau sudah ada.<br>";
} else {
    die("❌ Gagal membuat database: " . mysqli_error($conn));
}

// 3. Pilih database
if (!mysqli_select_db($conn, $db)) {
    die("❌ Gagal memilih database `$db`: " . mysqli_error($conn));
}

// 4. Baca file SQL
$sql_file = __DIR__ . '/db_buket (2).sql';
if (!file_exists($sql_file)) {
    die("❌ File database SQL tidak ditemukan di: " . $sql_file);
}

$sql_content = file_get_contents($sql_file);

// Hapus komentar sql
$sql_content = preg_replace('/--.*$/m', '', $sql_content);
$sql_content = preg_replace('/^\/\*.*\*\/$/m', '', $sql_content);

// Pisahkan query berdasarkan semicolon (;)
$queries = explode(';', $sql_content);
$success_count = 0;
$error_count = 0;

foreach ($queries as $query) {
    $query = trim($query);
    if (!empty($query)) {
        if (mysqli_query($conn, $query)) {
            $success_count++;
        } else {
            $error_count++;
            // Tampilkan error jika bukan error duplikasi / warning biasa
            $err = mysqli_error($conn);
            if (strpos($err, 'already exists') === false) {
                echo "<small style='color:red;'>Peringatan query: $err</small><br>";
            }
        }
    }
}

echo "✓ Proses impor selesai. Berhasil: $success_count query, Error/Peringatan: $error_count query.<br><br>";
echo "<h4 style='color:green;'>✓ Setup Selesai! Anda sekarang dapat menggunakan website.</h4>";
echo "<p>Silakan akses website melalui: <a href='customer/katalog.php'>Katalog Pelanggan</a> atau <a href='login.php'>Login Admin</a></p>";
?>
