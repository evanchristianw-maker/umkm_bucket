<?php
session_start();
include '../config/koneksi.php';

$aksi = $_GET['aksi'] ?? '';

// ============================================================
// AKSI 1: SIMPAN PESANAN
// ============================================================
if ($aksi === 'simpan_pesanan') {


    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ../customer/katalog.php');
        exit;
    }

    $nama_pemesan     = mysqli_real_escape_string($conn, trim($_POST['nama_pemesan'] ?? ''));
    $no_telepon       = mysqli_real_escape_string($conn, trim($_POST['no_telepon'] ?? ''));
    $id_produk        = (int)($_POST['id_produk'] ?? 0);
    $jumlah           = (int)($_POST['jumlah'] ?? 1);
    $tanggal_ambil    = mysqli_real_escape_string($conn, trim($_POST['tanggal_ambil'] ?? ''));
    $ucapan           = mysqli_real_escape_string($conn, trim($_POST['ucapan'] ?? ''));
    $is_custom        = isset($_POST['is_custom']) ? 1 : 0;
    $warna_kertas     = mysqli_real_escape_string($conn, trim($_POST['warna_kertas'] ?? ''));
    $jenis_isi        = mysqli_real_escape_string($conn, trim($_POST['jenis_isi'] ?? ''));
    $isi_custom       = mysqli_real_escape_string($conn, trim($_POST['isi_custom'] ?? ''));
    $tipe_pengambilan = mysqli_real_escape_string($conn, trim($_POST['tipe_pengambilan'] ?? 'ambil'));
    $total_harga      = (int)($_POST['total_harga'] ?? 0);

    // Tambahan hiasan (array → string)
    $tambahan = $_POST['tambahan'] ?? [];
    if (is_array($tambahan)) {
        $tambahan = implode(', ', $tambahan);
    }
    $tambahan = mysqli_real_escape_string($conn, $tambahan);

    // Validasi
    if (empty($nama_pemesan) || empty($no_telepon) || !$id_produk || empty($tanggal_ambil)) {
        $_SESSION['error'] = 'Data pemesanan tidak lengkap. Mohon isi semua field yang wajib.';
        header('Location: ../customer/pesan.php?id=' . $id_produk);
        exit;
    }

    // Generate ID Pesanan unik: PES-YYYYMMDD-XXXX
    $tanggalHariIni = date('Ymd');

$q = mysqli_query($conn,"
SELECT id_pesanan
FROM pesanan
WHERE id_pesanan LIKE 'PES-$tanggalHariIni-%'
ORDER BY id_pesanan DESC
LIMIT 1
");

if(mysqli_num_rows($q) > 0){

    $last = mysqli_fetch_assoc($q);

    // Ambil angka terakhir, contoh: PES-20260630-0002 -> 0002
    $nomor = (int)substr($last['id_pesanan'], -4);

    $nomor++;

}else{

    $nomor = 1;

}

$id_pesanan = "PES-$tanggalHariIni-".str_pad($nomor,4,"0",STR_PAD_LEFT);

    $tanggal_pesan = date('Y-m-d H:i:s');

    // Insert ke tabel pesanan
    $queryPesanan = mysqli_query($conn, "
        INSERT INTO pesanan (id_pesanan, nama_pemesan, no_telepon, tipe_pengambilan, tanggal_pesan, tanggal_ambil, total_harga, status_pesanan)
        VALUES ('$id_pesanan', '$nama_pemesan', '$no_telepon', '$tipe_pengambilan', '$tanggal_pesan', '$tanggal_ambil', $total_harga, 'Pending')
    ");

  if ($queryPesanan) {

    mysqli_query($conn, "
        INSERT INTO detail_pesanan
        (id_pesanan,id_produk,jumlah,warna_kertas,jenis_isi,isi_custom,ucapan,tambahan)
        VALUES
        ('$id_pesanan',$id_produk,$jumlah,'$warna_kertas','$jenis_isi','$isi_custom','$ucapan','$tambahan')
    ");

    // ============================
    // KURANGI STOK BAHAN
    // ============================

    if ($is_custom) {

        // Kertas
if ($warna_kertas != "") {
    $sql = "UPDATE stok_bahan
            SET jumlah = GREATEST(jumlah - $jumlah, 0)
            WHERE nama_bahan='$warna_kertas'";
    mysqli_query($conn, $sql);
}

// Jenis Isi
if ($jenis_isi != "") {
    $sql = "UPDATE stok_bahan
            SET jumlah = GREATEST(jumlah - $jumlah, 0)
            WHERE kode_bahan='$jenis_isi'";
    mysqli_query($conn, $sql);
}

// Tambahan
if (!empty($_POST['tambahan'])) {
    foreach ($_POST['tambahan'] as $item) {
        $sql = "UPDATE stok_bahan
                SET jumlah = GREATEST(jumlah - $jumlah, 0)
                WHERE kode_bahan='$item'";
        mysqli_query($conn, $sql);
    }
}

        header("Location: ../customer/pesan.php?step=bayar&id=".$id_pesanan);
        exit;
    }

    header("Location: ../customer/pesan.php?step=bayar&id=".$id_pesanan);
    exit;

  } // <-- INI YANG HILANG: penutup if ($queryPesanan)

}

// ============================================================
// AKSI 2: UPLOAD BUKTI PEMBAYARAN
// ============================================================
elseif ($aksi === 'upload_bukti') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ../customer/katalog.php');
        exit;
    }

    $id_pesanan = mysqli_real_escape_string($conn, trim($_POST['id_pesanan'] ?? ''));
    $tipe_bayar = mysqli_real_escape_string($conn, trim($_POST['tipe_bayar'] ?? 'dp'));

    // Validasi pesanan ada
    if (empty($id_pesanan)) {
        $_SESSION['error'] = 'ID Pesanan tidak valid.';
        header('Location: ../customer/katalog.php');
        exit;
    }

    // Validasi file upload
    if (!isset($_FILES['bukti_bayar']) || $_FILES['bukti_bayar']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = 'Gagal mengupload file. Pastikan file valid dan ukuran tidak melebihi 2MB.';
        header('Location: ../customer/pesan.php?step=upload&id=' . $id_pesanan . '&mode=' . $tipe_bayar);
        exit;
    }

    $file = $_FILES['bukti_bayar'];

    // Validasi ukuran (maks 2MB)
    if ($file['size'] > 2 * 1024 * 1024) {
        $_SESSION['error'] = 'Ukuran file terlalu besar. Maksimal 2MB.';
        header('Location: ../customer/pesan.php?step=upload&id=' . $id_pesanan . '&mode=' . $tipe_bayar);
        exit;
    }

    // Validasi tipe file
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
    if (!in_array($file['type'], $allowedTypes)) {
        $_SESSION['error'] = 'Tipe file tidak didukung. Gunakan JPG, PNG, atau PDF.';
        header('Location: ../customer/pesan.php?step=upload&id=' . $id_pesanan . '&mode=' . $tipe_bayar);
        exit;
    }

    // Generate nama file unik
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $namaFile = 'bukti_' . $id_pesanan . '_' . time() . '.' . $ext;

    // Pastikan folder upload ada
    $uploadDir = '../assets/upload/produk/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Pindahkan file
    if (move_uploaded_file($file['tmp_name'], $uploadDir . $namaFile)) {

        // Generate ID Pembayaran unik: PAY-YYYYMMDD-XXXX
        $tanggalHariIni = date('Ymd');

$q = mysqli_query($conn,"
SELECT id_pembayaran
FROM pembayaran
WHERE id_pembayaran LIKE 'PAY-$tanggalHariIni-%'
ORDER BY id_pembayaran DESC
LIMIT 1
");

if(mysqli_num_rows($q) > 0){

    $last = mysqli_fetch_assoc($q);

    $nomor = (int)substr($last['id_pembayaran'], -4);

    $nomor++;

}else{

    $nomor = 1;

}

$id_pembayaran = "PAY-$tanggalHariIni-".str_pad($nomor,4,"0",STR_PAD_LEFT);

        $tanggal_upload = date('Y-m-d H:i:s');

        // Insert ke tabel pembayaran
        $queryBayar = mysqli_query($conn, "
            INSERT INTO pembayaran (id_pembayaran, id_pesanan, file_bukti, tipe_bayar, tanggal_upload, status_verifikasi)
            VALUES ('$id_pembayaran', '$id_pesanan', '$namaFile', '$tipe_bayar', '$tanggal_upload', 'Pending')
        ");

        if ($queryBayar) {
            header('Location: ../customer/pesan.php?step=sukses');
            exit;
        } else {
            $_SESSION['error'] = 'Gagal menyimpan data pembayaran.';
            header('Location: ../customer/pesan.php?step=upload&id=' . $id_pesanan . '&mode=' . $tipe_bayar);
            exit;
        }
    } else {
        $_SESSION['error'] = 'Gagal menyimpan file bukti pembayaran.';
        header('Location: ../customer/pesan.php?step=upload&id=' . $id_pesanan . '&mode=' . $tipe_bayar);
        exit;
    }
}

// ============================================================
// DEFAULT: REDIRECT
// ============================================================
else {
    header('Location: ../customer/katalog.php');
    exit;
}
?>
