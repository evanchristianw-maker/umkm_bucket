# 🚀 Panduan Pengujian Website Toko Buket (XAMPP)

Panduan ini dibuat untuk memudahkan Anda menguji website **Toko Buket** menggunakan XAMPP secara lokal di PC Anda.

---

## 📋 Langkah-Langkah Persiapan

### 1. Salin Folder Project ke XAMPP
(Sudah dilakukan otomatis oleh sistem ke folder `C:\xampp\htdocs\umkm_bucket`).

### 2. Jalankan Apache & MySQL
(Sudah Anda lakukan di XAMPP Control Panel).

### 3. Setup Database Otomatis (Sekali Klik)
Kami telah menyediakan script otomatis untuk membuat database `db_buket` dan mengimpor tabel-tabelnya langsung dengan sekali klik!
1. Buka browser Anda (Chrome/Edge/Firefox).
2. Akses tautan berikut:
   👉 **[http://localhost/umkm_bucket/setup_db.php](http://localhost/umkm_bucket/setup_db.php)**
3. Halaman akan menampilkan status koneksi dan impor database. Jika sukses, Anda akan melihat pesan berwarna hijau: **"Setup Selesai! Anda sekarang dapat menggunakan website."**

---

## 🧪 Menguji Fitur-Fitur Website

### 1. Halaman Pelanggan (Customer)
* **Katalog Produk:**
  Akses di: **[http://localhost/umkm_bucket/customer/katalog.php](http://localhost/umkm_bucket/customer/katalog.php)**
  * Klik tombol **Pesan Sekarang** untuk memesan.

* **Form Pemesanan & Custom Buket (Multi-step):**
  * Setelah memilih produk, isi data diri dan aktifkan toggle **"Saya ingin custom buket (+Rp 15.000)"** untuk memilih kustomisasi.
  * Pilih opsi pembayaran, unggah bukti pembayaran, dan kirimkan.

* **Tracking Pesanan:**
  Akses di: **[http://localhost/umkm_bucket/customer/tracking.php](http://localhost/umkm_bucket/customer/tracking.php)**
  * Masukkan ID Pesanan Anda (contoh: `PES-20260629-0001`) untuk melacak status pesanan Anda.

---

### 2. Halaman Admin
* **Halaman Login Admin:**
  Akses di: **[http://localhost/umkm_bucket/login.php](http://localhost/umkm_bucket/login.php)**
  * Masukkan kredensial berikut:
    * **Username:** `admin`
    * **Password:** `admin123`

* **Rekap Keuangan Admin:**
  Akses di: **[http://localhost/umkm_bucket/admin/keuangan.php](http://localhost/umkm_bucket/admin/keuangan.php)**.
  * Anda dapat memfilter rekap keuangan, mencatat pengeluaran baru secara manual, atau menghapus riwayat pengeluaran langsung di tempat!
