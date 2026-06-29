<?php
include "../config/koneksi.php";

if(!isset($_GET['id'])){
    header("Location: pesanan.php");
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

$query = mysqli_query($conn, "
SELECT
    p.*,
    d.*,
    k.nama_produk,
    k.harga,
    k.kategori,
    k.foto_produk
FROM pesanan p
LEFT JOIN detail_pesanan d
    ON p.id_pesanan = d.id_pesanan
LEFT JOIN katalog k
    ON d.id_produk = k.id_produk
WHERE p.id_pesanan = '$id'
LIMIT 1
");

$data=mysqli_fetch_assoc($query);
if(!$data){
    echo "<script>
            alert('Data pesanan tidak ditemukan');
            window.location='pesanan.php';
          </script>";
    exit;
}
?>
<?php include 'template/header.php'; ?>
<?php include 'template/sidebar.php'; ?>

<h1>Detail Pesanan</h1>

<p>Informasi lengkap pesanan pelanggan.</p>

<br>

<a href="pesanan.php" class="btn btn-primary">
    ← Kembali
</a>

<br><br>
<div class="card">

<h2>Data Pemesan</h2>

<table class="detail-table">

<tr>
    <td width="220"><b>ID Pesanan</b></td>
    <td><?= $data['id_pesanan']; ?></td>
</tr>

<tr>
    <td><b>Nama Pemesan</b></td>
    <td><?= $data['nama_pemesan']; ?></td>
</tr>

<tr>
    <td><b>No. Telepon</b></td>
    <td><?= $data['no_telepon']; ?></td>
</tr>

<tr>
    <td><b>Tanggal Pesan</b></td>
    <td><?= date('d-m-Y', strtotime($data['tanggal_pesan'])); ?></td>
</tr>

<tr>
    <td><b>Tanggal Ambil</b></td>
    <td><?= date('d-m-Y', strtotime($data['tanggal_ambil'])); ?></td>
</tr>

<tr>
    <td><b>Total Harga</b></td>
    <td>
        Rp <?= number_format($data['total_harga'],0,",","."); ?>
    </td>
</tr>

</table>

</div>
<div class="card">

<h2>Detail Buket</h2>

<table class="detail-table">

<tr>
<td width="220"><b>Produk</b></td>
<td><?= $data['nama_produk']; ?></td>
</tr>

<tr>
<td><b>Jumlah</b></td>
<td><?= $data['jumlah']; ?></td>
</tr>

<tr>
<td><b>Warna Kertas</b></td>
<td><?= $data['warna_kertas'] ?: '-'; ?></td>
</tr>

<tr>
<td><b>Jenis Isi</b></td>
<td><?= $data['jenis_isi']?: '-'; ?></td>
</tr>

<tr>
<td><b>Isi Custom</b></td>
<td><?= $data['isi_custom'] ?: '-'; ?></td>
</tr>

<tr>
<td><b>Ucapan</b></td>
<td><?= $data['ucapan'] ?: '-'; ?></td>
</tr>

<tr>
<td><b>Tambahan</b></td>
<td><?= $data['tambahan'] ?: '-'; ?></td>
</tr>


</table>

<?php
if(!empty($data['foto_produk'])){
?>

<br>

<center>

<img
src="../assets/upload/produk/<?= $data['foto_produk']; ?>"
width="220"
style="border-radius:10px;">

</center>

<?php
}
?>

</div>
<div class="card">

<h2>Status Pesanan</h2>

<p>

<b>Status Saat Ini :</b>

<?php

$status = strtolower($data['status_pesanan']);

if($status=="menunggu"){
    $warna="warning";
}elseif($status=="diproses"){
    $warna="info";
}elseif($status=="selesai"){
    $warna="success";
}else{
    $warna="danger";
}

?>

<span class="badge bg-<?= $warna; ?>">
<?= $data['status_pesanan']; ?>
</span>

</p>

<hr>

<form action="../proses/update_status_pesanan.php" method="POST">

<input
type="hidden"
name="id_pesanan"
value="<?= $data['id_pesanan']; ?>">

<label><b>Ubah Status</b></label>

<br><br>

<select
name="status_pesanan"
class="form-control">

<option value="Menunggu" <?= $data['status_pesanan']=="Menunggu"?"selected":""; ?>>
Menunggu
</option>

<option value="Diproses" <?= $data['status_pesanan']=="Diproses"?"selected":""; ?>>
Diproses
</option>

<option value="Selesai" <?= $data['status_pesanan']=="Selesai"?"selected":""; ?>>
Selesai
</option>

<option value="Dibatalkan" <?= $data['status_pesanan']=="Dibatalkan"?"selected":""; ?>>
Dibatalkan
</option>

</select>

<br><br>

<button
type="submit"
class="btn btn-success">

Simpan Perubahan

</button>

</form>

</div>
<?php include 'template/footer.php'; ?>