<?php

include '../config/koneksi.php';

$id = $_POST['id_produk'];

$nama = $_POST['nama_produk'];
$kategori = $_POST['kategori'];
$harga = $_POST['harga'];
$deskripsi = $_POST['deskripsi'];
$status = $_POST['status'];

mysqli_query($conn,"
UPDATE katalog
SET
nama_produk='$nama',
kategori='$kategori',
harga='$harga',
deskripsi='$deskripsi',
status='$status'
WHERE id_produk='$id'
");

header("Location: ../admin/produk.php");