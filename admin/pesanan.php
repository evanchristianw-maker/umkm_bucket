<?php include 'template/header.php'; ?>
<?php include 'template/sidebar.php'; ?>

<?php
include "../config/koneksi.php";

$query = mysqli_query($conn, "
SELECT
    p.id_pesanan,
    p.nama_pemesan,
    p.no_telepon,
    p.tanggal_pesan,
    p.tanggal_ambil,
    p.total_harga,
    p.status_pesanan,
    d.warna_kertas,
    d.jenis_isi,
    d.isi_custom,
    d.ucapan,
    d.tambahan,
    d.jumlah
FROM pesanan p
LEFT JOIN detail_pesanan d
ON p.id_pesanan = d.id_pesanan
ORDER BY p.tanggal_pesan DESC
");
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manajemen Pesanan</title>

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

    padding:12px;
    border:1px solid #ddd;
    text-align:center;

}

tr:nth-child(even){

    background:#f9f9f9;

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

.diproses{

    background:#17a2b8;

}

.selesai{

    background:#28a745;

}

.dibatalkan{

    background:#dc3545;

}

.btn-detail{

    background:#0d6efd;
    color:white;
    border:none;
    padding:8px 15px;
    border-radius:5px;
    cursor:pointer;

}

.btn-detail:hover{

    background:#084298;

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
width:500px;
margin:60px auto;
padding:20px;
border-radius:10px;

}

.close{

float:right;
font-size:25px;
cursor:pointer;

}

</style>

</head>

<body>

<div class="container">

<h2>Manajemen Pesanan</h2>

<table>

<tr>

<th>ID</th>

<th>Nama</th>

<th>No HP</th>

<th>Tanggal Pesan</th>

<th>Tanggal Ambil</th>

<th>Total</th>

<th>Status</th>

<th>Detail</th>

</tr>

<?php
while($data = mysqli_fetch_assoc($query)){

    $status = strtolower(trim($data['status_pesanan']));

    if($status=="pending"){
        $warnaStatus="pending";
    }elseif($status=="diproses"){
        $warnaStatus="diproses";
    }elseif($status=="selesai"){
        $warnaStatus="selesai";
    }elseif($status=="dibatalkan"){
        $warnaStatus="dibatalkan";
    }else{
        $warnaStatus="pending";
    }
?>

<tr>

<td><?= $data['id_pesanan']; ?></td>

<td><?= $data['nama_pemesan']; ?></td>

<td><?= $data['no_telepon']; ?></td>

<td><?= date('d-m-Y',strtotime($data['tanggal_pesan'])); ?></td>

<td><?= date('d-m-Y',strtotime($data['tanggal_ambil'])); ?></td>

<td>
Rp <?= number_format($data['total_harga'],0,",","."); ?>
</td>

<td>

<span class="status <?= $warnaStatus; ?>">

<?= $data['status_pesanan']; ?>

</span>

</td>

<td>

<button
class="btn-detail"

onclick="detailPesanan(
'<?= $data['id_pesanan']; ?>',
'<?= htmlspecialchars($data['warna_kertas']); ?>',
'<?= htmlspecialchars($data['jenis_isi']); ?>',
'<?= htmlspecialchars($data['isi_custom']); ?>',
'<?= htmlspecialchars($data['ucapan']); ?>',
'<?= htmlspecialchars($data['tambahan']); ?>',
'<?= $data['jumlah']; ?>'
)">

Detail

</button>

</td>

</tr>

<?php
}
?>

</table>

<! MODAL DETAIL PEMESANAN >

<div id="modalDetail" class="modal">

<div class="modal-content">

<span class="close" onclick="tutupModal()">&times;</span>

<h2>Detail Pesanan</h2>

<hr><br>

<table style="width:100%;">

<tr>
<td width="35%"><b>ID Pesanan</b></td>
<td id="idPesanan"></td>
</tr>

<tr>
<td><b>Warna Kertas</b></td>
<td id="warnaKertas"></td>
</tr>

<tr>
<td><b>Jenis Isi</b></td>
<td id="jenisIsi"></td>
</tr>

<tr>
<td><b>Isi Custom</b></td>
<td id="isiCustom"></td>
</tr>

<tr>
<td><b>Ucapan</b></td>
<td id="ucapan"></td>
</tr>

<tr>
<td><b>Tambahan</b></td>
<td id="tambahan"></td>
</tr>

<tr>
<td><b>Jumlah</b></td>
<td id="jumlah"></td>
</tr>

</table>

<br>

<center>

<button
class="btn-detail"
onclick="tutupModal()">

Tutup

</button>

</center>

</div>

</div>

<script>

function detailPesanan(

id,
warna,
jenis,
custom,
ucapan,
tambahan,
jumlah

){

document.getElementById("modalDetail").style.display="block";

document.getElementById("idPesanan").innerHTML=id;

document.getElementById("warnaKertas").innerHTML=warna;

document.getElementById("jenisIsi").innerHTML=jenis;

document.getElementById("isiCustom").innerHTML=custom;

document.getElementById("ucapan").innerHTML=ucapan;

document.getElementById("tambahan").innerHTML=tambahan;

document.getElementById("jumlah").innerHTML=jumlah;

}

function tutupModal(){

document.getElementById("modalDetail").style.display="none";

}

window.onclick=function(event){

var modal=document.getElementById("modalDetail");

if(event.target==modal){

modal.style.display="none";

}

}

</script>

</div>

</body>

</html>

<?php include 'template/footer.php'; ?>