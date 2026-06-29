<?php include 'template/header.php'; ?>
<?php include 'template/sidebar.php'; ?>

<?php
include "../config/koneksi.php";

$query = mysqli_query($conn, "
SELECT
    pembayaran.*,
    pesanan.nama_pemesan,
    pesanan.total_harga,
    pesanan.status_pesanan
FROM pembayaran
LEFT JOIN pesanan
ON pembayaran.id_pesanan = pesanan.id_pesanan
ORDER BY pembayaran.tanggal_upload DESC
");
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manajemen Pembayaran</title>

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Arial, Helvetica, sans-serif;
}

body{
background:#f5f5f5;
}

.container{
width:95%;
margin:30px auto;
}

h2{
margin-bottom:20px;
color:#333;
}

table{

width:100%;
border-collapse:collapse;
background:white;

}

table th{

background:#4e73df;
color:white;
padding:12px;

}

table td{

padding:10px;
border:1px solid #ddd;
text-align:center;

}

tr:nth-child(even){

background:#f8f8f8;

}

.status{

padding:6px 12px;
border-radius:20px;
color:white;
font-size:13px;

}

.pending{

background:#ffc107;
color:black;

}

.verifikasi{

background:#28a745;

}

.tolak{

background:#dc3545;

}

.btn{

padding:8px 15px;
border:none;
border-radius:5px;
cursor:pointer;
color:white;

}

.btn-bukti{

background:#17a2b8;

}

.btn-verifikasi{

background:#28a745;

}

.btn-tolak{

background:#dc3545;

}

.modal{

display:none;
position:fixed;
left:0;
top:0;
width:100%;
height:100%;
background:rgba(0,0,0,.6);

}

.modal-content{

background:white;
width:550px;
margin:60px auto;
padding:20px;
border-radius:10px;

}

.close{

float:right;
font-size:25px;
cursor:pointer;

}

img{

max-width:100%;
border-radius:10px;

}

</style>

</head>

<body>

<div class="container">

<h2>Manajemen Pembayaran</h2>

<table>

<tr>

<th>ID Pembayaran</th>

<th>Nama Pemesan</th>

<th>ID Pesanan</th>

<th>Total</th>

<th>Tipe Bayar</th>

<th>Status</th>

<th>Bukti</th>

<th>Aksi</th>

</tr>

<?php
while($data = mysqli_fetch_assoc($query)){

$status = strtolower(trim($data['status_verifikasi']));

if($status=="pending"){

    $warnaStatus="pending";

}elseif($status=="terverifikasi"){

    $warnaStatus="verifikasi";

}elseif($status=="ditolak"){

    $warnaStatus="tolak";

}else{

    $warnaStatus="pending";

}
?>

<tr>

<td><?= $data['id_pembayaran']; ?></td>

<td><?= $data['nama_pemesan']; ?></td>

<td><?= $data['id_pesanan']; ?></td>

<td>

Rp <?= number_format($data['total_harga'],0,",","."); ?>

</td>

<td><?= $data['tipe_bayar']; ?></td>

<td>

<span class="status <?= $warnaStatus; ?>">

<?= $data['status_verifikasi']; ?>

</span>

</td>

<td>

<button
class="btn btn-bukti"

onclick="lihatBukti(
'<?= $data['id_pembayaran']; ?>',
'<?= $data['file_bukti']; ?>',
'<?= htmlspecialchars($data['catatan_cod']); ?>',
'<?= $data['jumlah_cod']; ?>',
'<?= $data['tipe_bayar']; ?>'
)">

Lihat

</button>

</td>

<td>

<?php

if($data['status_verifikasi']=="Pending" || $data['status_verifikasi']==""){

?>

<button

class="btn btn-verifikasi"

onclick="verifikasiPembayaran(
'<?= $data['id_pembayaran']; ?>'
)">

Verifikasi

</button>

<button

class="btn btn-tolak"

onclick="tolakPembayaran(
'<?= $data['id_pembayaran']; ?>'
)">

Tolak

</button>

<?php

}else{

echo "-";

}

?>

</td>

</tr>

<?php

}

?>

</table>

<! MODAL LIHAT BUKTI >

<div id="modalBukti" class="modal">

<div class="modal-content">

<span class="close" onclick="tutupModal()">&times;</span>

<h2>Detail Pembayaran</h2>

<hr><br>

<p><b>ID Pembayaran :</b></p>
<p id="idPembayaran"></p>

<br>

<p><b>Tipe Pembayaran :</b></p>
<p id="tipeBayar"></p>

<br>

<p><b>Catatan COD :</b></p>
<p id="catatanCod"></p>

<br>

<p><b>Jumlah COD :</b></p>
<p id="jumlahCod"></p>

<br>

<p><b>Bukti Pembayaran :</b></p>

<img
id="gambarBukti"
src=""
alt="Bukti Pembayaran">

<br><br>

<center>

<button
class="btn btn-bukti"
onclick="tutupModal()">

Tutup

</button>

</center>

</div>

</div>

<! FORM VERIFIKASI >

<form
id="formVerifikasi"
action="../proses/Verifikasi.php"
method="POST"
style="display:none;">

<input
type="hidden"
name="id_pembayaran"
id="verifikasi_id">

<input
type="hidden"
name="aksi"
value="verifikasi">

</form>

<! FORM TOLAK >

<form
id="formTolak"
action="../proses/Verifikasi.php"
method="POST"
style="display:none;">

<input
type="hidden"
name="id_pembayaran"
id="tolak_id">

<input
type="hidden"
name="aksi"
value="tolak">

<input
type="hidden"
name="alasan_tolak"
id="alasanTolak">

</form>

<script>

function lihatBukti(id,file,catatan,jumlah,tipe){

document.getElementById("modalBukti").style.display="block";

document.getElementById("idPembayaran").innerHTML=id;

document.getElementById("tipeBayar").innerHTML=tipe;

document.getElementById("catatanCod").innerHTML=catatan;

document.getElementById("jumlahCod").innerHTML=jumlah;

document.getElementById("gambarBukti").src="../assets/upload/produk/"+file;

}

function tutupModal(){

document.getElementById("modalBukti").style.display="none";

}

window.onclick=function(event){

var modal=document.getElementById("modalBukti");

if(event.target==modal){

modal.style.display="none";

}

}

function verifikasiPembayaran(id){

if(confirm("Verifikasi pembayaran ini?")){

document.getElementById("verifikasi_id").value=id;

document.getElementById("formVerifikasi").submit();

}

}

function tolakPembayaran(id){

let alasan=prompt("Masukkan alasan penolakan:");

if(alasan!=null && alasan!=""){

document.getElementById("tolak_id").value=id;

document.getElementById("alasanTolak").value=alasan;

document.getElementById("formTolak").submit();

}

}

</script>

</div>

</body>

</html> 

<?php include 'template/footer.php'; ?>