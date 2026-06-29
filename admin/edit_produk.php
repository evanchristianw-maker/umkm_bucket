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

<?php include 'template/header.php'; ?>
<?php include 'template/sidebar.php'; ?>

<h1>Edit Produk</h1>

<p>Silakan ubah data produk berikut.</p>

<br>

<div class="card">

<form action="../proses/update_produk.php"
      method="POST"
      enctype="multipart/form-data">

<input
type="hidden"
name="id_produk"
value="<?= $data['id_produk']; ?>">

<label>Nama Produk</label>

<input
type="text"
name="nama_produk"
value="<?= $data['nama_produk']; ?>"
required>

<label>Kategori</label>

<input
type="text"
name="kategori"
value="<?= $data['kategori']; ?>"
required>

<label>Harga</label>

<input
type="number"
name="harga"
value="<?= $data['harga']; ?>"
required>

<label>Deskripsi</label>

<textarea
name="deskripsi"
rows="5"><?= $data['deskripsi']; ?></textarea>

<label>Foto Produk Saat Ini</label>

<br>

<?php
if($data['foto_produk']!=""){
?>

<img
src="../assets/upload/produk/<?= $data['foto_produk']; ?>"
style="
width:180px;
height:180px;
object-fit:cover;
border-radius:10px;
border:1px solid #ddd;
margin-bottom:15px;
">

<?php
}else{
    echo "<p>Tidak ada foto.</p>";
}
?>

<label>Ganti Foto Produk</label>

<input
type="file"
name="foto_produk"
accept="image/*">

<small>
Kosongkan jika tidak ingin mengganti gambar.
</small>

<br><br>

<label>Status</label>

<select name="status">

<option
value="Aktif"
<?= ($data['status']=="Aktif") ? "selected" : ""; ?>>
Aktif
</option>

<option
value="Nonaktif"
<?= ($data['status']=="Nonaktif") ? "selected" : ""; ?>>
Nonaktif
</option>

</select>

<br>

<button
type="submit"
class="btn btn-success">
Update Produk
</button>

<a
href="produk.php"
class="btn btn-primary">
Kembali
</a>

</form>

</div>

<?php include 'template/footer.php'; ?>