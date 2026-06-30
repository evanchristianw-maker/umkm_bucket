<?php
include "../config/koneksi.php";

if(isset($_POST['aksi']) && isset($_POST['id_pembayaran'])){

    $aksi = $_POST['aksi'];
    $id_pembayaran = mysqli_real_escape_string($conn,$_POST['id_pembayaran']);

    // Ambil id pesanan
    $get = mysqli_query($conn,"SELECT id_pesanan FROM pembayaran WHERE id_pembayaran='$id_pembayaran'");
    $data = mysqli_fetch_assoc($get);
    $id_pesanan = $data['id_pesanan'];

    if($aksi=="verifikasi"){

        mysqli_query($conn,"
        UPDATE pembayaran
        SET
        status_verifikasi='Terverifikasi',
        tanggal_verifikasi=NOW(),
        alasan_tolak='',
        update_at=NOW()
        WHERE id_pembayaran='$id_pembayaran'
        ");

        mysqli_query($conn,"
        UPDATE pesanan
        SET
        status_pesanan='Diproses',
        update_at=NOW()
        WHERE id_pesanan='$id_pesanan'
        ");

        echo "<script>
        alert('Pembayaran berhasil diverifikasi');
        window.location='../admin/Pembayaran.php';
        </script>";

    }elseif($aksi=="tolak"){

        $alasan = mysqli_real_escape_string($conn,$_POST['alasan_tolak']);

        mysqli_query($conn,"
        UPDATE pembayaran
        SET
        status_verifikasi='Ditolak',
        alasan_tolak='$alasan',
        tanggal_verifikasi=NOW(),
        update_at=NOW()
        WHERE id_pembayaran='$id_pembayaran'
        ");

        mysqli_query($conn,"
        UPDATE pesanan
        SET
        status_pesanan='Pending',
        update_at=NOW()
        WHERE id_pesanan='$id_pesanan'
        ");

        echo "<script>
        alert('Pembayaran ditolak');
        window.location='../admin/Pembayaran.php';
        </script>";

    }

}else{

    header("Location: ../admin/Pembayaran.php");

}
?>