<?php
include "../config/koneksi.php";

$id_pesanan = $_POST['id_pesanan'];
$tipe_bayar = $_POST['tipe_bayar'];
$catatan_cod = $_POST['catatan_cod'] ?? "";
$jumlah_cod = $_POST['jumlah_cod'] ?? "";

$file_bukti = "";

// Jika Transfer
if($tipe_bayar == "Transfer"){

    if(isset($_FILES['file_bukti']) && $_FILES['file_bukti']['error'] == 0){

        $namaFile = time() . "_" . $_FILES['file_bukti']['name'];

        move_uploaded_file(
    $_FILES['file_bukti']['tmp_name'],
    "../assets/upload/produk/" . $namaFile
);

        $file_bukti = $namaFile;
    }
}

$query = mysqli_query($conn,"
INSERT INTO pembayaran
(
    id_pesanan,
    tipe_bayar,
    file_bukti,
    catatan_cod,
    jumlah_cod,
    status_verifikasi,
    tanggal_upload
)
VALUES
(
    '$id_pesanan',
    '$tipe_bayar',
    '$file_bukti',
    '$catatan_cod',
    '$jumlah_cod',
    'Pending',
    NOW()
)
");

if($query){

    echo "<script>
    alert('Pembayaran berhasil dikirim');
    window.location='../customer/tracking.php?id=$id_pesanan';
    </script>";

}else{

    echo "<script>
    alert('Gagal mengirim pembayaran');
    history.back();
    </script>";
}
?>