<?php include 'template/header.php'; ?>
<?php include 'template/sidebar.php'; ?>

<?php
session_start();

if (!isset($_SESSION['id_admin'])) {
    header("Location: ../login.php");
    exit;
}

include '../config/koneksi.php';

// ============================================================
// PROSES POST: TAMBAH / HAPUS PENGELUARAN
// ============================================================

// Tambah Pengeluaran
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi_keuangan']) && $_POST['aksi_keuangan'] === 'tambah_pengeluaran') {
    $keterangan = mysqli_real_escape_string($conn, trim($_POST['keterangan'] ?? ''));
    $jumlah_kel = (int)($_POST['jumlah'] ?? 0);
    $tanggal    = mysqli_real_escape_string($conn, trim($_POST['tanggal'] ?? date('Y-m-d')));
    $id_admin   = $_SESSION['id_admin'];

    if (!empty($keterangan) && $jumlah_kel > 0) {
        mysqli_query($conn, "
            INSERT INTO keuangan (id_admin, tipe, jumlah, tanggal, keterangan)
            VALUES ($id_admin, 'keluar', $jumlah_kel, '$tanggal', '$keterangan')
        ");
    }

    header('Location: keuangan.php?bulan=' . ($_POST['filter_bulan'] ?? date('m')) . '&tahun=' . ($_POST['filter_tahun'] ?? date('Y')));
    exit;
}

// Hapus Pengeluaran
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM keuangan WHERE id_keuangan=$id_hapus AND tipe='keluar'");
    header('Location: keuangan.php?bulan=' . ($_GET['bulan'] ?? date('m')) . '&tahun=' . ($_GET['tahun'] ?? date('Y')));
    exit;
}

// ============================================================
// AMBIL DATA KEUANGAN
// ============================================================

$bulan = (int)($_GET['bulan'] ?? date('m'));
$tahun = (int)($_GET['tahun'] ?? date('Y'));

// Total Pemasukan (dari keuangan tipe='masuk')
$qMasuk = mysqli_query($conn, "SELECT COALESCE(SUM(jumlah),0) AS total FROM keuangan WHERE tipe='masuk' AND MONTH(tanggal)=$bulan AND YEAR(tanggal)=$tahun");
$totalPemasukan = mysqli_fetch_assoc($qMasuk)['total'];

// Total Pengeluaran (dari keuangan tipe='keluar')
$qKeluar = mysqli_query($conn, "SELECT COALESCE(SUM(jumlah),0) AS total FROM keuangan WHERE tipe='keluar' AND MONTH(tanggal)=$bulan AND YEAR(tanggal)=$tahun");
$totalPengeluaran = mysqli_fetch_assoc($qKeluar)['total'];

$keuntungan = $totalPemasukan - $totalPengeluaran;

// Transaksi Masuk (JOIN dengan pesanan untuk nama pemesan)
$qTransaksiMasuk = mysqli_query($conn, "
    SELECT k.*, p.nama_pemesan
    FROM keuangan k
    LEFT JOIN pesanan p ON k.id_pesanan = p.id_pesanan
    WHERE k.tipe='masuk' AND MONTH(k.tanggal)=$bulan AND YEAR(k.tanggal)=$tahun
    ORDER BY k.tanggal DESC
");

// Transaksi Keluar
$qTransaksiKeluar = mysqli_query($conn, "
    SELECT * FROM keuangan
    WHERE tipe='keluar' AND MONTH(tanggal)=$bulan AND YEAR(tanggal)=$tahun
    ORDER BY tanggal DESC
");

$namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Keuangan - Toko Buket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-keuangan { border-radius: 12px; border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.07); }
    </style>
</head>
<body>

<h1>Rekap Keuangan</h1>

<!-- Filter Bulan/Tahun -->
<form method="GET" class="d-flex gap-2 mb-4" style="flex-wrap:wrap;">
    <select name="bulan" class="form-select" style="max-width:160px">
        <?php for ($m = 1; $m <= 12; $m++): ?>
            <option value="<?= $m ?>" <?= ($bulan == $m) ? 'selected' : '' ?>>
                <?= $namaBulan[$m] ?>
            </option>
        <?php endfor; ?>
    </select>

    <select name="tahun" class="form-select" style="max-width:120px">
        <?php for ($y = date('Y'); $y >= date('Y') - 3; $y--): ?>
            <option value="<?= $y ?>" <?= ($tahun == $y) ? 'selected' : '' ?>>
                <?= $y ?>
            </option>
        <?php endfor; ?>
    </select>

    <button type="submit" class="btn text-white" style="background:#3B4A1F">
        Filter
    </button>
</form>

<!-- Ringkasan -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card card-keuangan p-3" style="border-left:4px solid #198754">
            <div class="small text-muted">Total Pemasukan</div>
            <div class="fs-4 fw-bold text-success">
                Rp <?= number_format($totalPemasukan, 0, ',', '.') ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-keuangan p-3" style="border-left:4px solid #dc3545">
            <div class="small text-muted">Total Pengeluaran</div>
            <div class="fs-4 fw-bold text-danger">
                Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-keuangan p-3" style="border-left:4px solid #3B4A1F">
            <div class="small text-muted">Keuntungan Bersih</div>
            <div class="fs-4 fw-bold" style="color:#3B4A1F">
                Rp <?= number_format($keuntungan, 0, ',', '.') ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">

    <!-- PEMASUKAN -->
    <div class="col-md-6">
        <div class="card card-keuangan">
            <div class="card-header fw-bold text-success" style="background:transparent;">
                Pemasukan (Pesanan Selesai)
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>Total</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($qTransaksiMasuk) > 0): ?>
                            <?php while ($t = mysqli_fetch_assoc($qTransaksiMasuk)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($t['nama_pemesan'] ?? '-') ?></td>
                                    <td>Rp <?= number_format($t['jumlah'] ?? 0, 0, ',', '.') ?></td>
                                    <td><?= !empty($t['tanggal']) ? date('d/m/Y', strtotime($t['tanggal'])) : '-' ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted">Belum ada pemasukan</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- PENGELUARAN -->
    <div class="col-md-6">
        <div class="card card-keuangan">
            <div class="card-header fw-bold text-danger d-flex justify-content-between" style="background:transparent;">
                <span>Pengeluaran</span>
                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalPengeluaran">
                    + Catat
                </button>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Keterangan</th>
                            <th>Jumlah</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($qTransaksiKeluar) > 0): ?>
                            <?php while ($t = mysqli_fetch_assoc($qTransaksiKeluar)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($t['keterangan']) ?></td>
                                    <td>Rp <?= number_format($t['jumlah'], 0, ',', '.') ?></td>
                                    <td><?= date('d/m/Y', strtotime($t['tanggal'])) ?></td>
                                    <td>
                                        <a href="keuangan.php?hapus=<?= $t['id_keuangan'] ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>"
                                           class="text-danger small"
                                           onclick="return confirm('Hapus data ini?')">
                                            Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada pengeluaran</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- MODAL TAMBAH PENGELUARAN -->
<div class="modal fade" id="modalPengeluaran" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Catat Pengeluaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="keuangan.php">
                <input type="hidden" name="aksi_keuangan" value="tambah_pengeluaran">
                <input type="hidden" name="filter_bulan" value="<?= $bulan ?>">
                <input type="hidden" name="filter_tahun" value="<?= $tahun ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah (Rp)</label>
                        <input type="number" name="jumlah" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn text-white" style="background:#3B4A1F">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php include 'template/footer.php'; ?>