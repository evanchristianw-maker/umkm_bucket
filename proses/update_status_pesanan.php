<?php
session_start();

if (!isset($_SESSION['id_admin'])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . '/../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../admin/pesanan.php");
    exit;
}

$id_pesanan     = $_POST['id_pesanan'] ?? '';
$status_pesanan = $_POST['status_pesanan'] ?? '';

$statusValid = ['Menunggu', 'Diproses', 'Siap Ambil', 'Selesai', 'Batal'];

if ($id_pesanan === '' || !in_array($status_pesanan, $statusValid, true)) {
    header("Location: ../admin/pesanan.php?error=invalid");
    exit;
}

$id_pesanan_esc = mysqli_real_escape_string($conn, $id_pesanan);
$status_esc     = mysqli_real_escape_string($conn, $status_pesanan);

mysqli_query($conn, "
    UPDATE pesanan
    SET status_pesanan = '$status_esc'
    WHERE id_pesanan = '$id_pesanan_esc'
");

header("Location: ../admin/dashboard.php?success=1");
exit;