<?php

include '../config/koneksi.php';

$id = $_POST['id_produk'];
$nama = $_POST['nama_produk'];
$kategori = $_POST['kategori'];
$harga = $_POST['harga'];
$deskripsi = $_POST['deskripsi'];
$status = $_POST['status'];

$foto = $_FILES['foto_produk']['name'];

if($foto != ""){

    $tmp = $_FILES['foto_produk']['tmp_name'];

    $namaBaru = time()."_".$foto;

    move_uploaded_file(
        $tmp,
        "../assets/upload/produk/".$namaBaru
    );

    $lama = mysqli_fetch_assoc(
        mysqli_query(
            $conn,
            "SELECT foto_produk FROM katalog
            WHERE id_produk='$id'"
        )
    );

    if(
        $lama['foto_produk']!=""
        &&
        file_exists(
        "../assets/upload/produk/".$lama['foto_produk'])
    ){
        unlink(
        "../assets/upload/produk/".$lama['foto_produk']);
    }

    mysqli_query(
        $conn,
        "UPDATE katalog SET

        nama_produk='$nama',

        kategori='$kategori',

        harga='$harga',

        deskripsi='$deskripsi',

        status='$status',

        foto_produk='$namaBaru'

        WHERE id_produk='$id'"
    );

}else{

    mysqli_query(
        $conn,
        "UPDATE katalog SET

        nama_produk='$nama',

        kategori='$kategori',

        harga='$harga',

        deskripsi='$deskripsi',

        status='$status'

        WHERE id_produk='$id'"
    );

}

header("Location: ../admin/produk.php");