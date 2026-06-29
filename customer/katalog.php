<?php
include '../config/koneksi.php';

// Ambil semua produk aktif dari katalog
$query = mysqli_query($conn, "SELECT * FROM katalog WHERE status='Aktif' ORDER BY id_produk DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog - Toko Buket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar { background: #3B4A1F !important; }
        .btn-pesan { background: #3B4A1F; color: #fff; border: none; }
        .btn-pesan:hover { background: #2a3516; color: #fff; }
        .card { border-radius: 12px; border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    </style>
</head>
<body class="bg-light">

<nav class="navbar py-3 mb-4">
    <div class="container">
        <span class="navbar-brand text-white fw-bold">Toko Buket</span>
        <span class="text-white small">Pesan buket custom sesuai keinginanmu!</span>
    </div>
</nav>

<div class="container pb-5">
    <h5 class="fw-bold mb-4">Pilih Produk Buket</h5>

    <div class="row g-3">
    <?php
    if (mysqli_num_rows($query) > 0) {
        while ($k = mysqli_fetch_assoc($query)) {
    ?>
        <div class="col-md-4">
            <div class="card h-100">
                <?php if (!empty($k['foto_produk'])): ?>
                    <img src="../assets/upload/produk/<?= $k['foto_produk'] ?>" class="card-img-top" style="height:220px; object-fit:cover; border-radius:12px 12px 0 0">
                <?php else: ?>
                    <div class="d-flex align-items-center justify-content-center bg-light" style="height:220px; border-radius:12px 12px 0 0; font-size:4rem;">🌸</div>
                <?php endif; ?>
                <div class="card-body d-flex flex-column">
                    <h6 class="fw-bold"><?= htmlspecialchars($k['nama_produk']) ?></h6>
                    <p class="text-muted small flex-grow-1"><?= htmlspecialchars($k['deskripsi']) ?></p>
                    <div class="fw-bold fs-5 mb-3" style="color:#3B4A1F">Rp <?= number_format($k['harga'], 0, ',', '.') ?></div>
                    <a href="pesan.php?id=<?= $k['id_produk'] ?>" class="btn btn-pesan w-100">Pesan Sekarang</a>
                </div>
            </div>
        </div>
    <?php
        }
    } else {
    ?>
        <div class="col-12 text-center py-5 text-muted">Belum ada produk tersedia</div>
    <?php } ?>
    </div>

    <!-- Link Tracking -->
    <div class="text-center mt-4">
        <a href="tracking.php" class="text-decoration-none" style="color:#3B4A1F">
            📦 Lacak Pesananmu di sini
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>