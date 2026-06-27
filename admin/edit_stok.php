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
"SELECT * FROM stok_bahan
WHERE id_bahan='$id'"
);

$data = mysqli_fetch_assoc($query);

?>

<?php include 'template/header.php'; ?>
<?php include 'template/sidebar.php'; ?>

<h1>Edit Stok Bahan</h1>

<p>Perbarui informasi stok bahan.</p>

<br>

<div class="card">

<form action="../proses/update_stok.php" method="POST">

<input
type="hidden"
name="id_bahan"
value="<?= $data['id_bahan']; ?>">

<label>Kode Bahan</label>

<input
type="text"
name="kode_bahan"
value="<?= $data['kode_bahan']; ?>"
required>

<label>Nama Bahan</label>

<input
type="text"
name="nama_bahan"
value="<?= $data['nama_bahan']; ?>"
required>

<label>Jumlah</label>

<input
type="number"
name="jumlah"
value="<?= $data['jumlah']; ?>"
required>

<label>Satuan</label>

<input
type="text"
name="satuan"
value="<?= $data['satuan']; ?>"
required>

<button
type="submit"
class="btn btn-success">
Update Stok
</button>

<a
href="stok.php"
class="btn btn-primary">
Kembali
</a>

</form>

</div>

<?php include 'template/footer.php'; ?>