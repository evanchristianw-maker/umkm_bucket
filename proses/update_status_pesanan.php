<?php
include "../config/koneksi.php";

if(isset($_POST['id_pesanan']) && isset($_POST['status_pesanan'])){

    $id_pesanan = mysqli_real_escape_string($conn, $_POST['id_pesanan']);
    $status = mysqli_real_escape_string($conn, $_POST['status_pesanan']);

    $query = mysqli_query($conn,"
        UPDATE pesanan
        SET
            status_pesanan='$status',
            update_at=NOW()
        WHERE id_pesanan='$id_pesanan'
    ");

    if($query){

        echo "<script>
                alert('Status pesanan berhasil diperbarui');
                window.location='../admin/Pesanan.php';
              </script>";

    }else{

        echo "<script>
                alert('Gagal memperbarui status');
                window.location='../admin/Pesanan.php';
              </script>";

    }

}else{

    header("Location: ../admin/Pesanan.php");

}
?>