<?php

session_start();

include '../config/koneksi.php';

if(!isset($_SESSION['id_admin'])){
    header("Location: ../login.php");
    exit;
}

$id_pembayaran = $_GET['id'];
$status = $_GET['status'];

$id_admin = $_SESSION['id_admin'];

/*
Ambil data pembayaran
*/
$qBayar = mysqli_query(
    $conn,
    "SELECT * FROM pembayaran
     WHERE id_pembayaran='$id_pembayaran'"
);

$dataBayar = mysqli_fetch_assoc($qBayar);

$id_pesanan = $dataBayar['id_pesanan'];

/*
Update pembayaran
*/
mysqli_query(
    $conn,
    "UPDATE pembayaran
     SET
     status_verifikasi='$status',
     tanggal_verifikasi=NOW(),
     id_admin='$id_admin'
     WHERE id_pembayaran='$id_pembayaran'"
);

/*
Jika valid
*/
if($status == "Valid"){

    mysqli_query(
        $conn,
        "UPDATE pesanan
         SET status_pesanan='Diproses'
         WHERE id_pesanan='$id_pesanan'"
    );

}

/*
Jika ditolak
*/
if($status == "Ditolak"){

    mysqli_query(
        $conn,
        "UPDATE pesanan
         SET status_pesanan='Pending'
         WHERE id_pesanan='$id_pesanan'"
    );

}

header("Location: ../admin/pembayaran.php");
exit;