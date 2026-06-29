<?php
session_start();

require_once __DIR__ . '/../config/koneksi.php';

$username = $_POST['username'];
$password = $_POST['password'];

$query = mysqli_query($conn,
    "SELECT * FROM admin
     WHERE username='$username'
     AND password='$password'");

$data = mysqli_fetch_assoc($query);

if ($data) {
    $_SESSION['id_admin'] = $data['id_admin'];
    $_SESSION['nama'] = $data['nama'];

    header("Location: ../admin/dashboard.php");
    exit;
}

$_SESSION['error'] = "Username atau Password Salah";
header("Location: ../login.php");
exit;