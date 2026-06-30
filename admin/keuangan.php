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

// Total Pemasukan: dari pesanan yang statusnya 'Selesai' di bulan/tahun terpilih
$qMasuk = mysqli_query($conn, "
    SELECT COALESCE(SUM(p.total_harga), 0) AS total
    FROM pesanan p
    WHERE p.status_pesanan = 'Selesai'
      AND MONTH(p.tanggal_pesan) = $bulan
      AND YEAR(p.tanggal_pesan) = $tahun
");
$totalPemasukan = mysqli_fetch_assoc($qMasuk)['total'];

// Total Pengeluaran (dari keuangan tipe='keluar')
$qKeluar = mysqli_query($conn, "SELECT COALESCE(SUM(jumlah),0) AS total FROM keuangan WHERE tipe='keluar' AND MONTH(tanggal)=$bulan AND YEAR(tanggal)=$tahun");
$totalPengeluaran = mysqli_fetch_assoc($qKeluar)['total'];

$keuntungan = $totalPemasukan - $totalPengeluaran;

// Transaksi Masuk: pesanan yang sudah selesai di bulan/tahun terpilih
$qTransaksiMasuk = mysqli_query($conn, "
    SELECT p.id_pesanan, p.nama_pemesan, p.total_harga, p.tanggal_pesan
    FROM pesanan p
    WHERE p.status_pesanan = 'Selesai'
      AND MONTH(p.tanggal_pesan) = $bulan
      AND YEAR(p.tanggal_pesan) = $tahun
    ORDER BY p.tanggal_pesan DESC
");

// Transaksi Keluar
$qTransaksiKeluar = mysqli_query($conn, "
    SELECT * FROM keuangan
    WHERE tipe='keluar' AND MONTH(tanggal)=$bulan AND YEAR(tanggal)=$tahun
    ORDER BY tanggal DESC
");

$namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
?>

<h1>Rekap Keuangan</h1>

<p>Laporan pemasukan dan pengeluaran bulan <?= $namaBulan[$bulan] ?> <?= $tahun ?>.</p>

<br>

<!-- Filter Bulan/Tahun -->
<form method="GET" style="display:flex; gap:10px; margin-bottom:25px; flex-wrap:wrap; align-items:center;">
    <select name="bulan" style="max-width:160px; margin-bottom:0;">
        <?php for ($m = 1; $m <= 12; $m++): ?>
            <option value="<?= $m ?>" <?= ($bulan == $m) ? 'selected' : '' ?>>
                <?= $namaBulan[$m] ?>
            </option>
        <?php endfor; ?>
    </select>

    <select name="tahun" style="max-width:120px; margin-bottom:0;">
        <?php for ($y = date('Y'); $y >= date('Y') - 3; $y--): ?>
            <option value="<?= $y ?>" <?= ($tahun == $y) ? 'selected' : '' ?>>
                <?= $y ?>
            </option>
        <?php endfor; ?>
    </select>

    <button type="submit" class="btn btn-primary">
        Filter
    </button>
</form>

<!-- Ringkasan -->
<div class="stat-grid">
    <div class="stat-card green">
        <div class="stat-lbl">Total Pemasukan</div>
        <div class="stat-num">Rp <?= number_format($totalPemasukan, 0, ',', '.') ?></div>
    </div>
    <div class="stat-card red">
        <div class="stat-lbl">Total Pengeluaran</div>
        <div class="stat-num">Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?></div>
    </div>
    <div class="stat-card dark-green">
        <div class="stat-lbl">Keuntungan Bersih</div>
        <div class="stat-num">Rp <?= number_format($keuntungan, 0, ',', '.') ?></div>
    </div>
</div>

<!-- PEMASUKAN -->
<div class="card" style="margin-bottom:20px;">
    <h2 style="color:var(--success);">Pemasukan (Pesanan Selesai)</h2>

    <div style="overflow-x:auto;">
    <table>
        <thead>
            <tr>
                <th>ID Pesanan</th>
                <th>Nama Pemesan</th>
                <th>Total Bayar</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($qTransaksiMasuk) > 0): ?>
                <?php while ($t = mysqli_fetch_assoc($qTransaksiMasuk)): ?>
                    <tr>
                        <td><?= htmlspecialchars($t['id_pesanan']) ?></td>
                        <td><?= htmlspecialchars($t['nama_pemesan'] ?? '-') ?></td>
                        <td>Rp <?= number_format($t['total_harga'] ?? 0, 0, ',', '.') ?></td>
                        <td><?= !empty($t['tanggal_pesan']) ? date('d/m/Y', strtotime($t['tanggal_pesan'])) : '-' ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align:center; color:#999; padding:20px;">Belum ada pemasukan</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>

<!-- PENGELUARAN -->
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
        <h2 style="color:var(--danger); margin-bottom:0;">Pengeluaran</h2>
        <button class="btn btn-danger" onclick="document.getElementById('modalPengeluaran').style.display='block'">
            + Catat
        </button>
    </div>

    <div style="overflow-x:auto;">
    <table>
        <thead>
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
                               class="btn btn-danger"
                               style="padding:6px 12px; font-size:13px;"
                               onclick="return confirm('Hapus data ini?')">
                                Hapus
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align:center; color:#999; padding:20px;">Belum ada pengeluaran</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>

<!-- MODAL TAMBAH PENGELUARAN -->
<div id="modalPengeluaran" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('modalPengeluaran').style.display='none'">&times;</span>
        <h2>Catat Pengeluaran</h2>
        <form method="POST" action="keuangan.php">
            <input type="hidden" name="aksi_keuangan" value="tambah_pengeluaran">
            <input type="hidden" name="filter_bulan" value="<?= $bulan ?>">
            <input type="hidden" name="filter_tahun" value="<?= $tahun ?>">

            <label>Keterangan</label>
            <input type="text" name="keterangan" placeholder="Contoh: Beli pita, kertas, dll" required>

            <label>Jumlah (Rp)</label>
            <input type="number" name="jumlah" min="1" placeholder="Contoh: 50000" required>

            <label>Tanggal</label>
            <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>">

            <br>
            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" class="btn btn-warning" onclick="document.getElementById('modalPengeluaran').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php include 'template/footer.php'; ?>