<?php
session_start();

if(!isset($_SESSION['id_admin'])){
    header("Location: ../login.php");
    exit;
}

include '../config/koneksi.php';

$data = mysqli_query($conn,"SELECT * FROM katalog");
?>

<?php include 'template/header.php'; ?>
<?php include 'template/sidebar.php'; ?>    

<h1>Manajemen Produk</h1>

<p>Kelola seluruh katalog produk toko Buket.</p>

<br>
<a href="?tambah=1" class="btn btn-success">
    + Tambah Produk
</a>
<a href="dashboard.php" class="btn btn-primary">
    Dashboard
</a>

<?php
if(isset($_GET['tambah'])){
?>

<div class="card">

<form action="../proses/tambah_produk.php"
method="POST"
enctype="multipart/form-data">

Nama Produk <br>
<input type="text" name="nama_produk" required>
<br><br>

Kategori <br>
<input type="text" name="kategori" required>
<br><br>

Harga <br>
<input type="number" name="harga" required>
<br><br>

Deskripsi <br>
<textarea name="deskripsi"></textarea>
<br><br>

Foto Produk <br>
<input type="file" name="foto_produk">
<br><br>

Status <br>
<select name="status">
    <option value="Aktif">Aktif</option>
    <option value="Nonaktif">Nonaktif</option>
</select>

<br><br>

<button type="submit" class="btn btn-success">
    Simpan
</button>

</form>
</div>

<hr>

<?php } ?>

<div class="card">
<table>

<tr>
    <th>ID</th>
    <th>Foto</th>
    <th>Nama Produk</th>
    <th>Kategori</th>
    <th>Harga</th>
    <th>Status</th>
    <th>Aksi</th>
</tr>

<?php while($row=mysqli_fetch_assoc($data)){ ?>

<tr>

<td><?= $row['id_produk']; ?></td>

<td>
<img
src="../assets/upload/produk/<?= $row['foto_produk']; ?>"
width="80">
</td>

<td><?= $row['nama_produk']; ?></td>

<td><?= $row['kategori']; ?></td>

<td>Rp <?= number_format($row['harga']); ?></td>

<td><?= $row['status']; ?></td>

<td>

<a href="edit_produk.php?id=<?= $row['id_produk']; ?>"
class="btn btn-primary">
    Edit
</a>


<a href="../proses/hapus_produk.php?id=<?= $row['id_produk']; ?>"
class="btn btn-danger"
onclick="return confirm('Yakin hapus produk?')">
    Hapus
</a>

</td>

</tr>

<?php } ?>

</table>
</div>

<?php include 'template/footer.php'; ?>