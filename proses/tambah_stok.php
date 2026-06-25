<?php

include '../config/koneksi.php';

$nama = $_POST['nama_bahan'];
$jumlah = $_POST['jumlah'];
$satuan = $_POST['satuan'];
$kode = $_POST['kode_bahan'];

mysqli_query($conn,"
INSERT INTO stok_bahan
(
id_admin,
nama_bahan,
jumlah,
satuan,
kode_bahan
)
VALUES
(
1,
'$nama',
'$jumlah',
'$satuan',
'$kode'
)
");

header("Location: ../admin/stok.php");