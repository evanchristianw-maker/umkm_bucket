<?php include 'template/header.php'; ?>
<?php include 'template/sidebar.php'; ?>

<?php
session_start();

if (!isset($_SESSION['id_admin'])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . '/../config/koneksi.php';

// Pesanan hari ini
$qHariIni = mysqli_query($conn, "SELECT COUNT(*) as total FROM pesanan WHERE DATE(tanggal_pesan) = CURDATE()");
$pesananHariIni = mysqli_fetch_assoc($qHariIni)['total'];

// Belum lunas
$qBelumLunas = mysqli_query($conn, "SELECT COUNT(*) as total FROM pesanan WHERE status_pesanan IN ('Menunggu', 'Diproses')");
$belumLunas = mysqli_fetch_assoc($qBelumLunas)['total'];

// Segera dikirim
$qDikirim = mysqli_query($conn, "SELECT COUNT(*) as total FROM pesanan WHERE status_pesanan = 'Siap Ambil'");
$segeraDikirim = mysqli_fetch_assoc($qDikirim)['total'];

// Keuntungan bulan ini
$qKeuntungan = mysqli_query($conn, "
    SELECT COALESCE(SUM(total_harga), 0) as total
    FROM pesanan
    WHERE status_pesanan = 'Selesai'
    AND MONTH(tanggal_pesan) = MONTH(CURDATE())
    AND YEAR(tanggal_pesan) = YEAR(CURDATE())
");
$keuntungan = mysqli_fetch_assoc($qKeuntungan)['total'];

// Daftar semua pesanan urut dari pertama masuk
$qDaftarPesanan = mysqli_query($conn, "
    SELECT id_pesanan, nama_pemesan, no_telepon, tanggal_pesan, tanggal_ambil, total_harga, status_pesanan
    FROM pesanan
    ORDER BY tanggal_pesan ASC
");
?>

<!-- MAIN -->
    <h1>Dashboard Admin</h1>

    <p>Selamat datang di halaman Dashboard Admin Bucket Bloom.</p>

    <br>

    <!-- Kartu Statistik -->
    <div class="stat-grid">
        <div class="stat-card dark-green">
            <div class="stat-lbl">Pesanan Hari Ini</div>
            <div class="stat-num"><?php echo $pesananHariIni; ?></div>
        </div>
        <div class="stat-card yellow">
            <div class="stat-lbl">Belum Lunas</div>
            <div class="stat-num"><?php echo $belumLunas; ?></div>
        </div>
        <div class="stat-card red">
            <div class="stat-lbl">Segera Dikirim</div>
            <div class="stat-num"><?php echo $segeraDikirim; ?></div>
        </div>
        <div class="stat-card green">
            <div class="stat-lbl">Keuntungan Bulan Ini</div>
            <div class="stat-num">Rp <?php echo number_format($keuntungan, 0, ',', '.'); ?></div>
        </div>
    </div>

    <!-- Tabel Pesanan -->
    <div class="table-panel">
        <div class="table-panel-head">Daftar Pesanan (Urut dari Pertama Masuk)</div>
        <?php if (mysqli_num_rows($qDaftarPesanan) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Pemesan</th>
                    <th>Produk</th>
                    <th>Tgl Pesan</th>
                    <th>Tgl Ambil</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Konfirmasi WA</th>
                </tr>
            </thead>
            <tbody>
            <?php $no = 1; while ($row = mysqli_fetch_assoc($qDaftarPesanan)): ?>
                <?php
                    $status = $row['status_pesanan'];
                    $statusClass = 'menunggu';
                    if (stripos($status, 'selesai') !== false)                                          $statusClass = 'selesai';
                    elseif (stripos($status, 'diproses') !== false || stripos($status, 'proses') !== false) $statusClass = 'diproses';
                    elseif (stripos($status, 'siap') !== false)                                         $statusClass = 'siap';
                    elseif (stripos($status, 'batal') !== false)                                        $statusClass = 'batal';

                    $tglPesan = $row['tanggal_pesan'] ? date('d/m/Y', strtotime($row['tanggal_pesan'])) : '-';
                    $tglAmbil = $row['tanggal_ambil'] ? date('d/m/Y', strtotime($row['tanggal_ambil'])) : '-';

                    $noHp    = ltrim($row['no_telepon'], '0');
                    $pesan   = urlencode('Halo ' . $row['nama_pemesan'] . ', pesanan Anda dengan ID ' . $row['id_pesanan'] . ' sedang kami proses. Terima kasih!');
                    $waLink  = 'https://wa.me/62' . $noHp . '?text=' . $pesan;
                ?>
                <tr>
                    <td class="no-cell"><?php echo $no++; ?></td>
                    <td>
                        <div><?php echo htmlspecialchars($row['nama_pemesan']); ?></div>
                        <a href="<?php echo $waLink; ?>" target="_blank" class="wa-badge">
                            <i class="ti ti-brand-whatsapp"></i>
                            <?php echo htmlspecialchars($row['no_telepon']); ?>
                        </a>
                    </td>
                    <td>ADA</td>
                    <td><?php echo $tglPesan; ?></td>
                    <td><?php echo $tglAmbil; ?></td>
                    <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                    <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($status); ?></span></td>
                    <td>
                        <a href="<?php echo $waLink; ?>" target="_blank" class="btn btn-success">Konfirmasi</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="empty">
                <i class="ti ti-shopping-cart-off"></i>
                Belum ada pesanan
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'template/footer.php'; ?>