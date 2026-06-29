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


<h1>Manajemen Pesanan</h1>

<p>Kelola seluruh pesanan pelanggan.</p>

<br>

<div style="margin-bottom:20px;">
    <a href="dashboard.php" class="btn btn-primary">
        Dashboard
    </a>
</div>

<div class="card">

<div style="overflow-x:auto;">

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

  $status = strtolower($data['status_pesanan']);

if($status=="menunggu"){
    $warnaStatus="menunggu";
}
elseif($status=="diproses"){
    $warnaStatus="diproses";
}
elseif($status=="selesai"){
    $warnaStatus="selesai";
}
else{
    $warnaStatus="dibatalkan";
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

<form action="../proses/update_status_pesanan.php" method="POST">

<input
type="hidden"
name="id_pesanan"
value="<?= $data['id_pesanan']; ?>">

<select
name="status_pesanan"
class="status-select"
onchange="this.form.submit()">

<option value="Menunggu"
<?= $data['status_pesanan']=="Menunggu"?"selected":"";?>>
Menunggu
</option>

<option value="Diproses"
<?= $data['status_pesanan']=="Diproses"?"selected":"";?>>
Diproses
</option>

<option value="Selesai"
<?= $data['status_pesanan']=="Selesai"?"selected":"";?>>
Selesai
</option>

<option value="Dibatalkan"
<?= $data['status_pesanan']=="Dibatalkan"?"selected":"";?>>
Dibatalkan
</option>

</select>

</form>

</td>

<td>

<button
type="button"
class="btn btn-primary"
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

<?php
}
?>

</table>
</div>

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

</div>
</div>


<center>

<button
class="btn btn-primary"
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


<?php include 'template/footer.php'; ?>