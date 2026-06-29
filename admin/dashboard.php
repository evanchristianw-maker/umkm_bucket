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
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Toko Buket</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #f0f0f0;
            color: #1a1a1a;
            display: flex;
            min-height: 100vh;
        }

        /*  SIDEBAR */
        .sidebar {
            width: 200px;
            background: #1e3d1e;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0;
            height: 100vh;
            z-index: 100;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 18px 16px 16px;
        }

        .brand i { font-size: 20px; color: #fff; }
        .brand-title { font-size: 15px; font-weight: 700; color: #fff; }

        .nav { padding: 6px 0; flex: 1; }

        .nav a {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 9px 16px;
            font-size: 13px;
            color: rgba(255,255,255,0.80);
            text-decoration: none;
        }

        .nav a:hover { color: #fff; }
        .nav a i { font-size: 17px; width: 20px; flex-shrink: 0; }

        .nav-divider { height: 1px; background: rgba(255,255,255,0.10); margin: 6px 0; }

        .nav a.logout { color: #ff7070; }
        .nav a.logout:hover { color: #ff4444; }

        /*  MAIN  */
        .main {
            margin-left: 200px;
            flex: 1;
            padding: 28px 32px;
        }

        .page-title {
            font-size: 22px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 22px;
        }

        /* STAT CARDS  */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 26px;
        }

        .stat-card {
            padding: 20px 22px 18px;
            border-radius: 6px;
        }

        .stat-card.dark-green { background: #3a5e1f; }
        .stat-card.yellow     { background: #e6a817; }
        .stat-card.red        { background: #cc2e2e; }
        .stat-card.green      { background: #1a7a32; }

        .stat-lbl {
            font-size: 13px;
            font-weight: 500;
            color: #fff;
            margin-bottom: 10px;
        }

        .stat-card.yellow .stat-lbl { color: #7a4e00; }

        .stat-num {
            font-size: 26px;
            font-weight: 700;
            color: #fff;
            line-height: 1;
        }

        .stat-card.yellow .stat-num { color: #7a4e00; }

        /*  TABLE PANEL  */
        .table-panel {
            background: #fff;
            border-radius: 6px;
            border: 1px solid #ddd;
            overflow: hidden;
        }

        .table-panel-head {
            padding: 12px 18px;
            font-size: 14px;
            font-weight: 600;
            color: #222;
            border-bottom: 1px solid #e8e8e8;
        }

        table { width: 100%; border-collapse: collapse; }

        thead th {
            font-size: 13px;
            font-weight: 700;
            color: #222;
            padding: 10px 14px;
            text-align: left;
            border-bottom: 2px solid #e0e0e0;
            background: #fff;
        }

        tbody tr { border-bottom: 1px solid #f0f0f0; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #fafafa; }

        tbody td {
            padding: 10px 14px;
            font-size: 13px;
            color: #333;
            vertical-align: middle;
        }

        .no-cell { color: #666; }

        .wa-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #25D366;
            color: #fff;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 4px;
            margin-top: 4px;
            text-decoration: none;
        }

        .wa-badge i { font-size: 13px; }

        .status-badge {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 4px;
            color: #fff;
        }

        .status-badge.selesai  { background: #1a7a32; }
        .status-badge.diproses { background: #0d8cc4; }
        .status-badge.menunggu { background: #e6a817; color: #7a4e00; }
        .status-badge.siap     { background: #7b3fa0; }
        .status-badge.batal    { background: #cc2e2e; }

        .btn-konfirmasi {
            display: inline-block;
            background: #1a7a32;
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 4px;
            text-decoration: none;
        }

        .btn-konfirmasi:hover { background: #155c26; }

        .btn-detail {
            display: inline-block;
            background: #fff;
            color: #555;
            font-size: 12px;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 4px;
            border: 1px solid #bbb;
            text-decoration: none;
        }

        .btn-detail:hover { background: #f5f5f5; }

        .empty {
            text-align: center;
            padding: 40px;
            color: #bbb;
            font-size: 14px;
        }

        .empty i { font-size: 36px; display: block; margin-bottom: 10px; color: #ccc; }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="brand">
        <i class="ti ti-settings"></i>
        <span class="brand-title">Toko Buket</span>
    </div>
    <nav class="nav">
        <a href="dashboard.php">
            <i class="ti ti-layout-dashboard"></i> Dashboard
        </a>
        <a href="pesanan.php">
            <i class="ti ti-shopping-bag"></i> Manajemen Pesanan
        </a>
        <a href="pembayaran.php">
            <i class="ti ti-credit-card"></i> Manajemen Pembayaran
        </a>
        <a href="produk.php">
            <i class="ti ti-grid-dots"></i> Katalog Produk
        </a>
        <a href="stok.php">
            <i class="ti ti-box"></i> Stok Bahan
        </a>
        <a href="keuangan.php">
            <i class="ti ti-report-money"></i> Rekap Keuangan
        </a>
        <a href="riwayat.php">
            <i class="ti ti-history"></i> Riwayat Pesanan
        </a>
        <div class="nav-divider"></div>
        <a href="logout.php" class="logout">
            <i class="ti ti-logout"></i> Logout
        </a>
    </nav>
</aside>

<!-- MAIN -->
<div class="main">
    <div class="page-title">Dashboard Admin</div>

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
                        <a href="<?php echo $waLink; ?>" target="_blank" class="btn-konfirmasi">Konfirmasi</a>
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

</body>
</html>