<?php

include '../config/koneksi.php';

$id = $_GET['id'];

mysqli_query($conn,"
DELETE FROM katalog
WHERE id_produk='$id'
");

header("Location: ../admin/produk.php");