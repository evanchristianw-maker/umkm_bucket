<?php

include '../config/koneksi.php';

$id = $_GET['id'];

mysqli_query($conn,"
DELETE FROM stok_bahan
WHERE id_bahan='$id'
");

header("Location: ../admin/stok.php");