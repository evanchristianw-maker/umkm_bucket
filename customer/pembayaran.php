<?php
include "../config/koneksi.php";

$id = $_GET['id'];

$pesanan = mysqli_query($conn,"
SELECT *
FROM pesanan
WHERE id_pesanan='$id'
");

$data = mysqli_fetch_assoc($pesanan);

if(!$data){
    die("Pesanan tidak ditemukan");
}
?>

<?php include '../template/header_customer.php'; ?>

<h1>Pembayaran</h1>

<p>Silakan lakukan pembayaran sesuai metode yang dipilih.</p>

<div class="card">

<h2>Informasi Pesanan</h2>

<table>

<tr>
<td width="200"><b>ID Pesanan</b></td>
<td><?= $data['id_pesanan']; ?></td>
</tr>

<tr>
<td><b>Nama</b></td>
<td><?= $data['nama_pemesan']; ?></td>
</tr>

<tr>
<td><b>Total Pembayaran</b></td>
<td>
Rp <?= number_format($data['total_harga'],0,",","."); ?>
</td>
</tr>

</table>

</div>

<br>

<div class="card">

<h2>Upload Bukti Pembayaran</h2>

<form
action="../proses/upload_pembayaran.php"
method="POST"
enctype="multipart/form-data">

<input
type="hidden"
name="id_pesanan"
value="<?= $data['id_pesanan']; ?>">

<label>Metode Pembayaran</label>

<select
name="tipe_bayar"
id="tipe_bayar"
onchange="gantiMetode()"
required>

<option value="">Pilih</option>

<option value="Transfer">
Transfer
</option>

<option value="COD">
COD
</option>

</select>

<div id="transfer">

<label>Upload Bukti Transfer</label>

<input
type="file"
name="file_bukti">

</div>

<div
id="cod"
style="display:none;">

<label>Catatan COD</label>

<textarea
name="catatan_cod"></textarea>

<label>Jumlah Uang yang Disiapkan</label>

<input
type="number"
name="jumlah_cod">

</div>

<br>

<button
class="btn btn-success">

Kirim Pembayaran

</button>

</form>

</div>

<script>

function gantiMetode(){

let metode=document.getElementById("tipe_bayar").value;

if(metode=="Transfer"){

document.getElementById("transfer").style.display="block";
document.getElementById("cod").style.display="none";

}else if(metode=="COD"){

document.getElementById("transfer").style.display="none";
document.getElementById("cod").style.display="block";

}else{

document.getElementById("transfer").style.display="none";
document.getElementById("cod").style.display="none";

}

}

</script>

<?php include '../template/footer_customer.php'; ?>