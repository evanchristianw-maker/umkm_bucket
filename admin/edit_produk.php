<?php

session_start();

if(!isset($_SESSION['id_admin'])){
    header("Location: ../login.php");
    exit;
}

include '../config/koneksi.php';

$id = $_GET['id'];

$query = mysqli_query(
    $conn,
    "SELECT * FROM katalog WHERE id_produk='$id'"
);

$data = mysqli_fetch_assoc($query);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Produk</title>
</head>
<body>

<h2>Edit Produk</h2>

<form action="../proses/edit_produk.php" method="POST">

<input type="hidden"
name="id_produk"
value="<?= $data['id_produk']; ?>">

Nama Produk<br>
<input type="text"
name="nama_produk"
value="<?= $data['nama_produk']; ?>">

<br><br>

Kategori<br>
<input type="text"
name="kategori"
value="<?= $data['kategori']; ?>">

<br><br>

Harga<br>
<input type="number"
name="harga"
value="<?= $data['harga']; ?>">

<br><br>

Deskripsi<br>
<textarea name="deskripsi"><?= $data['deskripsi']; ?></textarea>

<br><br>

Status<br>
<select name="status">
    <option value="Aktif">Aktif</option>
    <option value="Nonaktif">Nonaktif</option>
</select>

<br><br>

<button type="submit">
Update Produk
</button>

</form>

</body>
</html>