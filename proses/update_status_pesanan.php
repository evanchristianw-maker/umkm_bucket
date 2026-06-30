<?php

include "../config/koneksi.php";

$id = $_POST['id_pesanan'];
$status = $_POST['status_pesanan'];

mysqli_query($conn,"
UPDATE pesanan
SET status_pesanan='$status'
WHERE id_pesanan='$id'
");

echo "<script>
alert('Status berhasil diperbarui');
window.location='../admin/detail_pesanan.php?id=$id';
</script>";

?>
