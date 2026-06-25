<?php

session_start();

if(!isset($_SESSION['id_admin'])){
    header("Location: ../login.php");
    exit;
}

include '../config/koneksi.php';

$qProduk = mysqli_query($conn, "SELECT COUNT(*) as total FROM katalog");
$produk = mysqli_fetch_assoc($qProduk);

$qPesanan = mysqli_query($conn, "SELECT COUNT(*) as total FROM pesanan");
$pesanan = mysqli_fetch_assoc($qPesanan);

$qPembayaran = mysqli_query($conn, "SELECT COUNT(*) as total FROM pembayaran");
$pembayaran = mysqli_fetch_assoc($qPembayaran);

$qStok = mysqli_query($conn, "SELECT COUNT(*) as total FROM stok_bahan");
$stok = mysqli_fetch_assoc($qStok);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<h1>Dashboard Admin</h1>

<h3>
    Selamat Datang,
    <?php echo $_SESSION['nama']; ?>
</h3>

<div class="dashboard">

    <div class="card">
        <h2><?php echo $produk['total']; ?></h2>
        <p>Total Produk</p>
    </div>

    <div class="card">
        <h2><?php echo $pesanan['total']; ?></h2>
        <p>Total Pesanan</p>
    </div>

    <div class="card">
        <h2><?php echo $pembayaran['total']; ?></h2>
        <p>Total Pembayaran</p>
    </div>

    <div class="card">
        <h2><?php echo $stok['total']; ?></h2>
        <p>Total Bahan</p>
    </div>

</div>

<br>

<a href="produk.php">Kelola Produk</a> |
<a href="pesanan.php">Kelola Pesanan</a> |
<a href="stok.php">Kelola Stok</a> |
<a href="keuangan.php">Keuangan</a> |
<a href="logout.php">Logout</a>

</body>
</html>