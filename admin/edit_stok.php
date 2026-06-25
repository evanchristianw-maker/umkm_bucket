<?php include 'template/header.php'; ?>
<?php include 'template/sidebar.php'; ?>

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

<form action="../proses/update_stok.php"
method="POST">

<input type="hidden"
name="id_bahan"
value="<?= $data['id_bahan']; ?>">

Nama Bahan<br>
<input type="text"
name="nama_bahan"
value="<?= $data['nama_bahan']; ?>">

<br><br>

Jumlah<br>
<input type="number"
name="jumlah"
value="<?= $data['jumlah']; ?>">

<br><br>

Satuan<br>
<input type="text"
name="satuan"
value="<?= $data['satuan']; ?>">

<br><br>

<button>
Update Stok
</button>

</form>

<?php include 'template/footer.php'; ?>