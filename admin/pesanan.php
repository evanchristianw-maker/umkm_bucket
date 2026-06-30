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
<span class="status-badge <?= $warnaStatus; ?>">
<?= $data['status_pesanan']; ?>
</span>
</td>

<td>

<a href="detail_pesanan.php?id=<?= $data['id_pesanan']; ?>" class="btn btn-primary">
Detail
</a>



</td>

</tr>

<?php
}
?>

</table>

</div>

</div>

<?php include 'template/footer.php'; ?>

