<<<<<<< HEAD
<?php
include '../config/koneksi.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tracking Pesanan</title>
</head>
<body>

<h2>Tracking Pesanan</h2>

<form method="GET">

    ID Pesanan :

    <input
    type="text"
    name="id_pesanan"
    required>

    <button type="submit">
        Cari
    </button>

</form>

<hr>

<?php

if(isset($_GET['id_pesanan']))
{
    $id_pesanan = $_GET['id_pesanan'];

    $query = mysqli_query(
        $conn,
        "SELECT * FROM pesanan
        WHERE id_pesanan='$id_pesanan'"
    );

    if(mysqli_num_rows($query) > 0)
    {
        $data = mysqli_fetch_assoc($query);
?>

        <h3>Data Pesanan</h3>

        <table border="1" cellpadding="10">

            <tr>
                <td>ID Pesanan</td>
                <td><?= $data['id_pesanan']; ?></td>
            </tr>

            <tr>
                <td>Nama Pemesan</td>
                <td><?= $data['nama_pemesan']; ?></td>
            </tr>

            <tr>
                <td>No Telepon</td>
                <td><?= $data['no_telepon']; ?></td>
            </tr>

            <tr>
                <td>Total Harga</td>
                <td>
                    Rp <?= number_format($data['total_harga']); ?>
                </td>
            </tr>

            <tr>
                <td>Status Pesanan</td>
                <td>
                    <b><?= $data['status_pesanan']; ?></b>
                </td>
            </tr>

        </table>

<?php
    }
    else
    {
        echo "Pesanan tidak ditemukan";
    }
}
?>

</body>
=======
<?php
include '../config/koneksi.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tracking Pesanan</title>
    <link rel="stylesheet"
href="../assets/css/style.css">
</head>
<body>

<h2>Tracking Pesanan</h2>

<form method="GET">

    ID Pesanan :

    <input
    type="text"
    name="id_pesanan"
    required>

    <button type="submit">
        Cari
    </button>

</form>

<hr>

<?php

if(isset($_GET['id_pesanan']))
{
    $id_pesanan = $_GET['id_pesanan'];

    $query = mysqli_query(
        $conn,
        "SELECT * FROM pesanan
        WHERE id_pesanan='$id_pesanan'"
    );

    if(mysqli_num_rows($query) > 0)
    {
        $data = mysqli_fetch_assoc($query);
?>

        <h3>Data Pesanan</h3>

        <table border="1" cellpadding="10">

            <tr>
                <td>ID Pesanan</td>
                <td><?= $data['id_pesanan']; ?></td>
            </tr>

            <tr>
                <td>Nama Pemesan</td>
                <td><?= $data['nama_pemesan']; ?></td>
            </tr>

            <tr>
                <td>No Telepon</td>
                <td><?= $data['no_telepon']; ?></td>
            </tr>

            <tr>
                <td>Total Harga</td>
                <td>
                    Rp <?= number_format($data['total_harga']); ?>
                </td>
            </tr>

            <tr>
                <td>Status Pesanan</td>
                <td>
                    <b><?= $data['status_pesanan']; ?></b>
                </td>
            </tr>

        </table>

<?php
    }
    else
    {
        echo "Pesanan tidak ditemukan";
    }
}
?>

</body>
>>>>>>> main
</html>