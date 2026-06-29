
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

<?php include 'template/header.php'; ?>
<?php include 'template/sidebar.php'; ?>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<h1>Manajemen Pembayaran</h1>

<p>Kelola seluruh pembayaran pelanggan.</p>

<br>    


<div class="card">

<div style="margin-bottom:20px;">
    <a href="dashboard.php" class="btn btn-primary">
        Dashboard
    </a>
</div>

<div style="overflow-x:auto;">

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
class="btn btn-info"
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
class="btn btn-success"
onclick="verifikasiPembayaran('<?= $data['id_pembayaran']; ?>')">
Verifikasi
</button>


<button
class="btn btn-danger"
onclick="tolakPembayaran('<?= $data['id_pembayaran']; ?>')">
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

</div>

</div>

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
class="btn btn-info"
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

document.getElementById("gambarBukti").src="../assets/upload/pembayaran/"+file;

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


<?php include 'template/footer.php'; ?>