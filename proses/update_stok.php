<?php

include '../config/koneksi.php';

$id = $_POST['id_bahan'];

$nama = $_POST['nama_bahan'];
$jumlah = $_POST['jumlah'];
$satuan = $_POST['satuan'];

mysqli_query($conn,"
UPDATE stok_bahan
SET
nama_bahan='$nama',
jumlah='$jumlah',
satuan='$satuan'
WHERE id_bahan='$id'
");

header("Location: ../admin/stok.php");