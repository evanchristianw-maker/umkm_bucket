<?php

session_start();

if(!isset($_SESSION['id_admin'])){
    header("Location: ../login.php");
    exit;
}

include '../config/koneksi.php';

$data = mysqli_query($conn,"
SELECT *
FROM stok_bahan
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Stok Bahan</title>
</head>
<body>

<h2>Data Stok Bahan</h2>

<a href="?tambah=1">
Tambah Bahan
</a>

<br><br>

<?php
if(isset($_GET['tambah'])){
?>

<form action="../proses/tambah_stok.php"
method="POST">

Nama Bahan <br>
<input type="text"
name="nama_bahan"
required>

<br><br>

Jumlah <br>
<input type="number"
name="jumlah"
required>

<br><br>

Satuan <br>
<input type="text"
name="satuan"
required>

<br><br>

Kode Bahan <br>
<input type="text"
name="kode_bahan"
required>

<br><br>

<button>
Simpan
</button>

</form>

<hr>

<?php } ?>

<table border="1"
cellpadding="10">

<tr>
<th>Kode</th>
<th>Nama</th>
<th>Jumlah</th>
<th>Satuan</th>
<th>Aksi</th>
</tr>

<?php
while($row=mysqli_fetch_assoc($data)){
?>

<tr>

<td><?= $row['kode_bahan']; ?></td>

<td><?= $row['nama_bahan']; ?></td>

<td><?= $row['jumlah']; ?></td>

<td><?= $row['satuan']; ?></td>

<td>

<a href="edit_stok.php?id=<?= $row['id_bahan']; ?>">
Edit
</a>

|

<a href="../proses/hapus_stok.php?id=<?= $row['id_bahan']; ?>"
onclick="return confirm('Yakin hapus bahan?')">
Hapus
</a>

</td>

</tr>

<?php } ?>

</table>

</body>
</html>