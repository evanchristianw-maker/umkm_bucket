<?php

include '../config/koneksi.php';

$id = $_GET['id'];

mysqli_query(
    $conn,
    "UPDATE pesanan
     SET status_pesanan='Selesai'
     WHERE id_pesanan='$id'"
);

header("Location: ../admin/pesanan.php");