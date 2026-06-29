<?php

session_start();

if(!isset($_SESSION['id_admin'])){
    header("Location: ../login.php");
    exit;
}

include '../config/koneksi.php';

$data = mysqli_query($conn, "SELECT * FROM stok_bahan");

?>

<?php include 'template/header.php'; ?>
<?php include 'template/sidebar.php'; ?>

<h1>Manajemen Stok Bahan</h1>

<p>Kelola seluruh data stok bahan baku yang digunakan dalam pembuatan bucket.</p>

<br>

<a href="?tambah=1" class="btn btn-success">
    + Tambah Bahan
</a>
<a href="dashboard.php" class="btn btn-primary">
    Dashboard
</a>

<br><br>

<?php
if(isset($_GET['tambah'])){
?>

<div class="card">

<h2>Tambah Stok Bahan</h2>

<form action="../proses/tambah_stok.php" method="POST">

<label>Nama Bahan</label>

<input
type="text"
name="nama_bahan"
required>

<label>Jumlah</label>

<input
type="number"
name="jumlah"
required>

<label>Satuan</label>

<input
type="text"
name="satuan"
required>

<label>Kode Bahan</label>

<input
type="text"
name="kode_bahan"
required>

<button
type="submit"
class="btn btn-success">
Simpan
</button>

</form>

</div>

<br>

<?php } ?>

<div class="card">

<table>

<tr>

<th>Kode</th>
<th>Nama Bahan</th>
<th>Jumlah</th>
<th>Satuan</th>
<th>Status</th>
<th>Aksi</th>

</tr>

<?php while($row=mysqli_fetch_assoc($data)){ ?>

<tr>

<td><?= $row['kode_bahan']; ?></td>

<td><?= $row['nama_bahan']; ?></td>

<td>

<?php

if($row['jumlah']==0){

    echo "<span style='color:red;font-weight:bold;'>0</span>";

}else{

    echo $row['jumlah'];

}

?>

</td>

<td><?= $row['satuan']; ?></td>

<td>

<?php

if($row['jumlah'] > 5){

    echo "<span class='status aman'>Aman</span>";

}elseif($row['jumlah'] > 0){

    echo "<span class='status menipis'>Menipis</span>";

}else{

    echo "<span class='status habis'>Habis</span>";

}

?>

</td>

<td>

<a href="edit_stok.php?id=<?= $row['id_bahan']; ?>"
class="btn btn-primary">
Edit
</a>

<a href="../proses/hapus_stok.php?id=<?= $row['id_bahan']; ?>"
class="btn btn-danger"
onclick="return confirm('Yakin ingin menghapus bahan ini?')">
Hapus
</a>

</td>

</tr>

<?php } ?>

</table>

</div>

<?php include 'template/footer.php'; ?>