<?php
include '../config/koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Pesanan - Toko Buket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar { background: #3B4A1F !important; }
        .btn-main { background: #3B4A1F; color: #fff; border: none; }
        .btn-main:hover { background: #2a3516; color: #fff; }
        .card { border-radius: 12px; border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .badge-pending { background: #ffc107; color: #000; }
        .badge-diproses { background: #0dcaf0; color: #000; }
        .badge-selesai { background: #198754; color: #fff; }
        .badge-dibatalkan { background: #dc3545; color: #fff; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar py-3 mb-4">
    <div class="container">
        <a href="katalog.php" class="text-white text-decoration-none">← Kembali ke Katalog</a>
        <span class="text-white fw-bold">Toko Buket</span>
    </div>
</nav>

<div class="container pb-5" style="max-width:600px">
    <h5 class="fw-bold mb-4">📦 Tracking Pesanan</h5>

    <!-- Form Cari -->
    <div class="card p-4 mb-4">
        <form method="GET">
            <label class="form-label fw-semibold">Masukkan ID Pesanan</label>
            <div class="d-flex gap-2">
                <input type="text" name="id_pesanan" class="form-control" placeholder="cth: PES-20260629-0001" value="<?= htmlspecialchars($_GET['id_pesanan'] ?? '') ?>" required>
                <button type="submit" class="btn btn-main px-4">Cari</button>
            </div>
        </form>
    </div>

    <?php
    if (isset($_GET['id_pesanan']) && !empty($_GET['id_pesanan'])) {
        $id_pesanan = mysqli_real_escape_string($conn, $_GET['id_pesanan']);

        $query = mysqli_query($conn,
            "SELECT p.*, d.warna_kertas, d.jenis_isi, d.isi_custom, d.ucapan, d.tambahan, d.jumlah
             FROM pesanan p
             LEFT JOIN detail_pesanan d ON p.id_pesanan = d.id_pesanan
             WHERE p.id_pesanan='$id_pesanan'"
        );

        if (mysqli_num_rows($query) > 0) {
            $data = mysqli_fetch_assoc($query);

            // Tentukan badge status
            $status = strtolower(trim($data['status_pesanan']));
            $badgeClass = 'badge-pending';
            if ($status === 'diproses') $badgeClass = 'badge-diproses';
            elseif ($status === 'selesai') $badgeClass = 'badge-selesai';
            elseif ($status === 'dibatalkan') $badgeClass = 'badge-dibatalkan';
    ?>

    <!-- Hasil Tracking -->
    <div class="card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0">Data Pesanan</h6>
            <span class="badge <?= $badgeClass ?> px-3 py-2"><?= htmlspecialchars($data['status_pesanan']) ?></span>
        </div>

        <table class="table table-borderless mb-0">
            <tr>
                <td class="text-muted" width="40%">ID Pesanan</td>
                <td class="fw-semibold"><?= htmlspecialchars($data['id_pesanan']) ?></td>
            </tr>
            <tr>
                <td class="text-muted">Nama Pemesan</td>
                <td class="fw-semibold"><?= htmlspecialchars($data['nama_pemesan']) ?></td>
            </tr>
            <tr>
                <td class="text-muted">No Telepon</td>
                <td class="fw-semibold"><?= htmlspecialchars($data['no_telepon']) ?></td>
            </tr>
            <tr>
                <td class="text-muted">Tanggal Pesan</td>
                <td class="fw-semibold"><?= date('d-m-Y H:i', strtotime($data['tanggal_pesan'])) ?></td>
            </tr>
            <tr>
                <td class="text-muted">Tanggal Ambil</td>
                <td class="fw-semibold"><?= date('d-m-Y', strtotime($data['tanggal_ambil'])) ?></td>
            </tr>
            <tr>
                <td class="text-muted">Pengambilan</td>
                <td class="fw-semibold"><?= $data['tipe_pengambilan'] === 'kirim' ? 'Dikirim' : 'Ambil Sendiri' ?></td>
            </tr>
            <tr>
                <td class="text-muted">Total Harga</td>
                <td class="fw-bold fs-5" style="color:#3B4A1F">Rp <?= number_format($data['total_harga'], 0, ',', '.') ?></td>
            </tr>
        </table>

        <?php if (!empty($data['jumlah'])): ?>
        <hr>
        <h6 class="fw-bold mb-3">Detail Pesanan</h6>
        <table class="table table-borderless mb-0">
            <tr>
                <td class="text-muted" width="40%">Jumlah</td>
                <td class="fw-semibold"><?= $data['jumlah'] ?></td>
            </tr>
            <?php if (!empty($data['warna_kertas'])): ?>
            <tr>
                <td class="text-muted">Warna Kertas</td>
                <td class="fw-semibold"><?= htmlspecialchars($data['warna_kertas']) ?></td>
            </tr>
            <?php endif; ?>
            <?php if (!empty($data['jenis_isi'])): ?>
            <tr>
                <td class="text-muted">Jenis Isi</td>
                <td class="fw-semibold"><?= htmlspecialchars($data['jenis_isi']) ?></td>
            </tr>
            <?php endif; ?>
            <?php if (!empty($data['isi_custom'])): ?>
            <tr>
                <td class="text-muted">Catatan Custom</td>
                <td class="fw-semibold"><?= htmlspecialchars($data['isi_custom']) ?></td>
            </tr>
            <?php endif; ?>
            <?php if (!empty($data['ucapan'])): ?>
            <tr>
                <td class="text-muted">Ucapan</td>
                <td class="fw-semibold"><?= htmlspecialchars($data['ucapan']) ?></td>
            </tr>
            <?php endif; ?>
            <?php if (!empty($data['tambahan'])): ?>
            <tr>
                <td class="text-muted">Tambahan</td>
                <td class="fw-semibold"><?= htmlspecialchars($data['tambahan']) ?></td>
            </tr>
            <?php endif; ?>
        </table>
        <?php endif; ?>
    </div>

    <?php
        } else {
            echo '<div class="alert alert-danger text-center">❌ Pesanan dengan ID <strong>' . htmlspecialchars($id_pesanan) . '</strong> tidak ditemukan.</div>';
        }
    }
    ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>