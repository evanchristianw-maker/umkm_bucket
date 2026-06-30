<?php
session_start();
include '../config/koneksi.php';

$step = $_GET['step'] ?? 'form';
$id   = $_GET['id'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemesanan - Toko Buket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar { background: #3B4A1F !important; }
        .btn-main { background: #3B4A1F; color: #fff; border: none; }
        .btn-main:hover { background: #2a3516; color: #fff; }
        #harga-info { background: #f0f4e8; border-radius: 10px; }
        .mode-card { border: 2px solid #dee2e6; border-radius: 12px; cursor: pointer; transition: .2s; }
        .mode-card.selected { border-color: #3B4A1F; background: #f0f4e8; }
        .upload-area { border: 2px dashed #3B4A1F; border-radius: 12px; padding: 2rem; text-align: center; cursor: pointer; transition: .2s; }
        .upload-area:hover { background: #f0f4e8; }
        .badge-dp { background: #ffc107; color: #000; }
        .badge-lunas { background: #198754; color: #fff; }
        .qris-img { max-width: 260px; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.12); }
        .card { border-radius: 12px; border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    </style>
</head>
<body class="bg-light">

<?php
// ============================================================
// STEP 1: FORM PEMESANAN
// ============================================================
if ($step === 'form'):

    // Ambil data produk
    $id_produk = (int) $id;
    $qProduk = mysqli_query($conn, "SELECT * FROM katalog WHERE id_produk=$id_produk");
    $produk = mysqli_fetch_assoc($qProduk);

    if (!$produk) {
        header('Location: katalog.php');
        exit;
    }

    // Ambil stok bahan untuk kustomisasi
    $qBahan = mysqli_query($conn, "SELECT * FROM stok_bahan WHERE jumlah > 0");
    $warnaWrap = [];
    $jenisIsi  = [];
    $tambahan  = [];

    while ($bahan = mysqli_fetch_assoc($qBahan)) {

    if (strpos($bahan['nama_bahan'], 'Kertas') !== false) {

        $warnaWrap[] = $bahan;

    } elseif ($bahan['nama_bahan'] == 'Bunga Mawar') {

        $jenisIsi[] = $bahan;

    } else {

        $tambahan[] = $bahan;

    }

}
?>

<nav class="navbar py-3 mb-4">
    <div class="container">
        <a href="katalog.php" class="text-white text-decoration-none">← Kembali ke Katalog</a>
        <span class="text-white fw-bold">Toko Buket</span>
    </div>
</nav>

<div class="container pb-5" style="max-width:700px">
    <h5 class="fw-bold mb-1">Form Pemesanan</h5>
    <p class="text-muted mb-4">Produk: <strong><?= htmlspecialchars($produk['nama_produk']) ?></strong></p>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <form method="POST" action="../proses/tambah_pesanan.php?aksi=simpan_pesanan" id="formPesan">
        <input type="hidden" name="id_produk" value="<?= $produk['id_produk'] ?>">
        <input type="hidden" name="total_harga" id="total_harga_input" value="<?= $produk['harga'] ?>">

        <!-- Data Diri -->
        <div class="card p-4 mb-3">
            <h6 class="fw-bold mb-3">Data Diri</h6>
            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Pemesan</label>
                <input type="text" name="nama_pemesan" class="form-control" placeholder="Masukkan nama lengkap" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Nomor Telepon / WhatsApp</label>
                <input type="text" name="no_telepon" class="form-control" placeholder="cth: 08123456789" required>
            </div>
        </div>

        <!-- Detail Pesanan -->
        <div class="card p-4 mb-3">
            <h6 class="fw-bold mb-3">Detail Pesanan</h6>
            <div class="mb-3">
                <label class="form-label fw-semibold">Jumlah</label>
                <input type="number" name="jumlah" id="jumlah" class="form-control" value="1" min="1" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Tanggal Pengambilan</label>
                <input type="date" name="tanggal_ambil" class="form-control" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Jenis Pengambilan</label>
                <select name="tipe_pengambilan" id="tipe_pengambilan" class="form-select">
                    <option value="ambil">Ambil Sendiri</option>
                    <option value="kirim">Dikirim (+Rp 20.000)</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Teks Ucapan (opsional)</label>
                <textarea name="ucapan" class="form-control" rows="2" placeholder="Selamat wisuda, semoga sukses!"></textarea>
            </div>
        </div>

        <!-- Kustomisasi -->
        <div class="card p-4 mb-3">
            <h6 class="fw-bold mb-3">Kustomisasi</h6>
            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is_custom" name="is_custom" value="1">
                    <label class="form-check-label fw-semibold" for="is_custom">Saya ingin custom buket (+Rp 15.000)</label>
                </div>
            </div>
            <div id="form-custom" style="display:none">
                <div class="mb-3">
                    <label class="form-label">Warna Kertas Wrap</label>
                    <select name="warna_kertas" class="form-select">
                        <?php foreach ($warnaWrap as $warna): ?>
                            <option value="<?= htmlspecialchars($warna['nama_bahan']) ?>">
                                <?= htmlspecialchars($warna['nama_bahan']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jenis Isi</label>
                    <select name="jenis_isi" class="form-select">
                        <option value="">-- Pilih Isi --</option>
                        <?php foreach ($jenisIsi as $isi): ?>
                            <option value="<?= htmlspecialchars($isi['kode_bahan']) ?>">
                                <?= htmlspecialchars($isi['nama_bahan']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tambahan Hiasan</label>
                    <?php foreach ($tambahan as $item): ?>
                        <div class="form-check">
                            <input class="form-check-input tambahan" type="checkbox" name="tambahan[]" value="<?= htmlspecialchars($item['kode_bahan']) ?>">
                            <label class="form-check-label"><?= htmlspecialchars($item['nama_bahan']) ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan Custom Tambahan (opsional)</label>
                    <textarea name="isi_custom" class="form-control" rows="2" placeholder="Tulis detail custom tambahan..."></textarea>
                </div>
            </div>
        </div>

        <!-- Kalkulasi Harga -->
        <div id="harga-info" class="p-3 mb-4">
            <div class="d-flex justify-content-between"><span>Harga Dasar</span><span id="txt-dasar">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></span></div>
            <div class="d-flex justify-content-between"><span>Biaya Kustomisasi</span><span id="txt-custom">Rp 0</span></div>
            <div class="d-flex justify-content-between" id="row-ongkir" style="display:none"><span>Biaya Pengiriman</span><span id="txt-ongkir">Rp 20.000</span></div>
            <hr>
            <div class="d-flex justify-content-between fw-bold fs-5"><span>Total</span><span id="txt-total" style="color:#3B4A1F">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></span></div>
            <div class="text-muted small mt-1">DP Minimal (50%): <strong id="txt-dp">Rp <?= number_format($produk['harga'] / 2, 0, ',', '.') ?></strong></div>
        </div>

        <button type="submit" class="btn btn-main w-100 py-2 fw-bold fs-5">Lanjut ke Pembayaran →</button>
    </form>
</div>

<script>
    const hargaDasar = <?= $produk['harga'] ?>;
    const BIAYA_KIRIM = 20000;

    function fmt(n) {
        return 'Rp ' + n.toLocaleString('id-ID');
    }

    function hitung() {
        const jumlah = parseInt(document.getElementById('jumlah').value) || 1;
        const isCustom = document.getElementById('is_custom').checked;
        const tipeAmbil = document.getElementById('tipe_pengambilan').value;
        let custom = isCustom ? 15000 : 0;
        if (isCustom) {
            const jenis = document.querySelector('[name="jenis_isi"]').value;
            if (jenis === 'bunga_segar') custom += 20000;
            document.querySelectorAll('.tambahan:checked').forEach(cb => {
                if (cb.value === 'pita_premium') custom += 5000;
                if (cb.value === 'coklat') custom += 10000;
                if (cb.value === 'boneka') custom += 25000;
            });
        }
        const ongkir = (tipeAmbil === 'kirim') ? BIAYA_KIRIM : 0;
        document.getElementById('row-ongkir').style.display = (ongkir > 0) ? 'flex' : 'none';

        const dasar = hargaDasar * jumlah;
        const total = dasar + custom + ongkir;
        document.getElementById('txt-dasar').textContent = fmt(dasar);
        document.getElementById('txt-custom').textContent = fmt(custom);
        document.getElementById('txt-ongkir').textContent = fmt(ongkir);
        document.getElementById('txt-total').textContent = fmt(total);
        document.getElementById('txt-dp').textContent = fmt(total * 0.5);
        document.getElementById('total_harga_input').value = total;
    }

    document.getElementById('is_custom').addEventListener('change', function() {
        document.getElementById('form-custom').style.display = this.checked ? 'block' : 'none';
        hitung();
    });
    document.getElementById('jumlah').addEventListener('input', hitung);
    document.getElementById('tipe_pengambilan').addEventListener('change', hitung);
    document.querySelectorAll('.tambahan').forEach(cb => cb.addEventListener('change', hitung));
    document.querySelector('[name="jenis_isi"]')?.addEventListener('change', hitung);
</script>

<?php
// ============================================================
// STEP 2: PEMBAYARAN
// ============================================================
elseif ($step === 'bayar'):
    $pesananId = htmlspecialchars($id);
?>

<nav class="navbar py-3 mb-4">
    <div class="container"><span class="text-white fw-bold">Toko Buket</span></div>
</nav>

<div class="container pb-5" style="max-width:580px">
    <h5 class="fw-bold mb-1">Pembayaran Pesanan</h5>
    <p class="text-muted mb-4">Pesanan #<?= $pesananId ?></p>

    <!-- Pilih Mode Bayar -->
    <div class="mb-4">
        <label class="form-label fw-semibold mb-2">Pilih Mode Pembayaran</label>
        <div class="row g-2">
            <div class="col-6">
                <div class="mode-card p-3 text-center selected" id="card-dp" onclick="pilihMode('dp')">
                    <div style="font-size:1.8rem">💳</div>
                    <div class="fw-bold mt-1">DP 50%</div>
                    <div class="text-muted small">Bayar setengah dulu</div>
                </div>
            </div>
            <div class="col-6">
                <div class="mode-card p-3 text-center" id="card-lunas" onclick="pilihMode('lunas')">
                    <div style="font-size:1.8rem">✅</div>
                    <div class="fw-bold mt-1">Lunas</div>
                    <div class="text-muted small">Bayar penuh sekarang</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Info QRIS -->
    <div class="card p-4 text-center mb-4">
        <h6 class="fw-bold mb-3">Scan QRIS untuk Membayar</h6>
        <?php
        $qrisPath = '../assets/upload/produk/';
        $qrisExt = file_exists($qrisPath . 'qris.jpg') ? 'jpg' : 'png';
        if (file_exists($qrisPath . 'qris.' . $qrisExt)): ?>
            <img src="<?= $qrisPath ?>qris.<?= $qrisExt ?>" alt="QRIS Toko Buket" class="qris-img mb-3">
        <?php else: ?>
            <div class="p-4 bg-light rounded mb-3">
                <div style="font-size:3rem">📱</div>
                <p class="text-muted small mt-2">QR Code QRIS akan ditampilkan di sini</p>
            </div>
        <?php endif; ?>
        <div class="alert alert-warning mb-0">
            <strong>⚠ Wajib bayar via QRIS</strong><br>
            <span class="small">Pelunasan sisa bisa dilakukan COD saat pengambilan</span>
        </div>
    </div>

    <!-- Nominal -->
    <div class="card p-3 mb-4" style="background:#f0f4e8;border:none">
        <div class="d-flex justify-content-between">
            <span>Mode Bayar</span>
            <span class="fw-bold" id="txt-mode">DP 50%</span>
        </div>
        <div class="d-flex justify-content-between mt-1">
            <span>Yang Harus Dibayar</span>
            <span class="fw-bold fs-5" style="color:#3B4A1F" id="txt-nominal">Bayar 50% dari total</span>
        </div>
    </div>

    <a id="btn-upload" href="pesan.php?step=upload&id=<?= $pesananId ?>&mode=dp" class="btn btn-main w-100 fw-bold py-2 fs-5">
        Sudah Bayar? Upload Bukti →
    </a>
</div>

<script>
let modeTerpilih = 'dp';
function pilihMode(mode) {
    modeTerpilih = mode;
    document.getElementById('card-dp').classList.toggle('selected', mode === 'dp');
    document.getElementById('card-lunas').classList.toggle('selected', mode === 'lunas');
    document.getElementById('txt-mode').textContent = mode === 'dp' ? 'DP 50%' : 'Lunas 100%';
    document.getElementById('txt-nominal').textContent = mode === 'dp' ? 'Bayar 50% dari total' : 'Bayar penuh';
    const pesananId = '<?= $pesananId ?>';
    document.getElementById('btn-upload').href =
        `pesan.php?step=upload&id=${pesananId}&mode=${mode}`;
}
</script>

<?php
// ============================================================
// STEP 3: UPLOAD BUKTI
// ============================================================
elseif ($step === 'upload'):
    $mode      = $_GET['mode'] ?? 'dp';
    $pesananId = htmlspecialchars($id);
?>

<nav class="navbar py-3 mb-4">
    <div class="container"><span class="text-white fw-bold">Toko Buket</span></div>
</nav>

<div class="container pb-5" style="max-width:560px">
    <div class="d-flex align-items-center gap-2 mb-4">
        <h5 class="fw-bold mb-0">Upload Bukti Pembayaran</h5>
        <span class="badge <?= $mode === 'lunas' ? 'badge-lunas' : 'badge-dp' ?> px-3 py-2">
            <?= $mode === 'lunas' ? 'LUNAS' : 'DP 50%' ?>
        </span>
    </div>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="card p-4">
        <form method="POST" action="../proses/tambah_pesanan.php?aksi=upload_bukti" enctype="multipart/form-data">
            <input type="hidden" name="id_pesanan" value="<?= $pesananId ?>">
            <input type="hidden" name="tipe_bayar" value="<?= htmlspecialchars($mode) ?>">

            <!-- Info Mode -->
            <div class="alert <?= $mode === 'lunas' ? 'alert-success' : 'alert-warning' ?> mb-4">
                <?php if ($mode === 'lunas'): ?>
                    <strong>✅ Pembayaran Lunas</strong><br>
                    <span class="small">Kamu memilih bayar penuh via QRIS. Tidak ada sisa pembayaran saat pengambilan.</span>
                <?php else: ?>
                    <strong>💳 Pembayaran DP 50%</strong><br>
                    <span class="small">Sisa 50% dapat dibayar saat pengambilan (COD) atau transfer sebelumnya.</span>
                <?php endif; ?>
            </div>

            <!-- Upload Area -->
            <div class="mb-4">
                <label class="form-label fw-semibold">Foto Bukti Transfer QRIS</label>
                <div class="upload-area" onclick="document.getElementById('file-input').click()">
                    <div style="font-size:2.5rem">📤</div>
                    <div class="fw-semibold mt-2">Klik untuk pilih foto</div>
                    <div class="text-muted small">JPG, PNG, atau PDF — maks 2MB</div>
                    <div id="nama-file" class="mt-2 text-success small"></div>
                </div>
                <input type="file" id="file-input" name="bukti_bayar" accept="image/*,.pdf" required style="display:none"
                    onchange="document.getElementById('nama-file').textContent = this.files[0]?.name ?? ''">
            </div>

            <button type="submit" class="btn btn-main w-100 fw-bold py-2">
                Kirim Bukti Pembayaran
            </button>
        </form>
    </div>
</div>

<?php
// ============================================================
// STEP 4: SUKSES
// ============================================================
elseif ($step === 'sukses'):
?>

<nav class="navbar py-3 mb-4">
    <div class="container"><span class="text-white fw-bold">Toko Buket</span></div>
</nav>

<div class="container d-flex align-items-center justify-content-center" style="min-height:70vh">
    <div class="card p-5 text-center" style="max-width:480px">
        <div style="font-size:4rem" class="mb-3">🎉</div>
        <h4 class="fw-bold mb-2">Pesanan Berhasil Dikirim!</h4>
        <p class="text-muted mb-4">Bukti pembayaranmu sedang kami verifikasi. Admin akan segera memproses pesananmu. Pantau status melalui nomor WhatsApp yang kamu daftarkan.</p>
        <div class="alert alert-success">
            <strong>Terima kasih!</strong><br>
            Kami akan menghubungi kamu via WhatsApp jika pesanan sudah dikonfirmasi.
        </div>
        <a href="katalog.php" class="btn btn-main w-100 mt-2">Kembali ke Katalog</a>
        <a href="tracking.php" class="btn btn-outline-secondary w-100 mt-2">Lacak Pesanan</a>
    </div>
</div>

<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>