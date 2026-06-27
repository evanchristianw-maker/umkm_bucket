<<<<<<< HEAD
<?php

include '../config/koneksi.php';

$nama = $_POST['nama_produk'];
$kategori = $_POST['kategori'];
$harga = $_POST['harga'];
$deskripsi = $_POST['deskripsi'];
$status = $_POST['status'];

$foto = $_FILES['foto_produk']['name'];
$tmp = $_FILES['foto_produk']['tmp_name'];

move_uploaded_file(
    $tmp,
    "../assets/upload/produk/".$foto
);

mysqli_query($conn,"
INSERT INTO katalog
(
id_admin,
nama_produk,
kategori,
deskripsi,
harga,
foto_produk,
status
)
VALUES
(
1,
'$nama',
'$kategori',
'$deskripsi',
'$harga',
'$foto',
'$status'
)
");

=======
<?php

include '../config/koneksi.php';

$nama = $_POST['nama_produk'];
$kategori = $_POST['kategori'];
$harga = $_POST['harga'];
$deskripsi = $_POST['deskripsi'];
$status = $_POST['status'];

$foto = $_FILES['foto_produk']['name'];
$tmp = $_FILES['foto_produk']['tmp_name'];

move_uploaded_file(
    $tmp,
    "../assets/upload/produk/".$foto
);

mysqli_query($conn,"
INSERT INTO katalog
(
id_admin,
nama_produk,
kategori,
deskripsi,
harga,
foto_produk,
status
)
VALUES
(
1,
'$nama',
'$kategori',
'$deskripsi',
'$harga',
'$foto',
'$status'
)
");

>>>>>>> main
header("Location: ../admin/produk.php");