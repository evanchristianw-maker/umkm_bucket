
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
'<?= $data['file_bukti']; ?>'
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

<!-- MODAL LIHAT BUKTI -->

<div id="modalBukti" class="modal">

    <div class="modal-content">

        <span class="close" onclick="tutupModal()">&times;</span>

        <h2 style="text-align:center;">Bukti Pembayaran</h2>

        <hr><br>

        <div style="text-align:center;">

            <img
                id="gambarBukti"
                src=""
                alt="Bukti Pembayaran"
                style="
                    max-width:100%;
                    max-height:500px;
                    border-radius:10px;
                    border:1px solid #ddd;
                    box-shadow:0 3px 8px rgba(0,0,0,.15);
                ">

        </div>

        <br>

        <div style="text-align:center;">

            <button
                class="btn btn-primary"
                onclick="tutupModal()">

                Tutup

            </button>

        </div>

    </div>

</div>

<!-- FORM VERIFIKASI -->

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

function lihatBukti(id,file){

    document.getElementById("modalBukti").style.display="block";

    document.getElementById("gambarBukti").src =
    "../assets/upload/produk/" + file;

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