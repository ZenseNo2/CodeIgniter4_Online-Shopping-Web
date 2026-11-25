# ShopEase — Toko Online (CodeIgniter 4)

Implementasi **Hands-On Lab ShopEase** (Praktikum 1–12) sekaligus **Hands-On Lab Authentication System**.
Antarmuka bergaya iOS (radius sudut kecil, CSS buatan sendiri tanpa CDN, mendukung mode gelap).

| Peran | Fitur utama |
|---|---|
| **Pengunjung** | Beranda, katalog (cari, filter kategori, urutkan), detail produk, registrasi, login |
| **Customer** | Keranjang, checkout (alamat, kurir, metode bayar), upload bukti transfer, riwayat & lacak pesanan, batalkan, konfirmasi diterima, akun & alamat |
| **Operator** | Antrean kerja, verifikasi pembayaran, input resi pengiriman, kelola produk & pesanan |
| **Admin** | Semua fitur operator + dashboard statistik, CRUD kategori & produk (upload gambar), laporan penjualan + ekspor CSV, kelola role pengguna |

CodeIgniter **4.7.6** sudah disertakan (folder `system/`), jadi **tidak perlu Composer**.

## Kebutuhan
- XAMPP dengan **PHP 8.2+** (Apache + MySQL/MariaDB)
- Ekstensi PHP `intl` dan `mbstring` aktif. Di XAMPP: buka `php.ini`, hapus `;` di depan `extension=intl`, restart Apache.

## Cara 1 — Lewat XAMPP (paling mudah)
1. Salin folder `shopease` ke `C:\xampp\htdocs\shopease`.
2. Start **Apache** dan **MySQL** di XAMPP Control Panel.
3. Buka http://localhost/phpmyadmin → tab **Import** → pilih `db_shopease.sql` → **Import**
   (file ini otomatis membuat database `db_shopease` beserta tabel dan data contoh).
4. Buka **http://localhost/shopease/public/**

`baseURL` terdeteksi otomatis saat dibuka dari localhost, jadi `.env` tidak perlu diubah
selama MySQL memakai user `root` tanpa password (bawaan XAMPP).

## Cara 2 — Lewat `php spark serve`
```bash
cd C:\xampp\htdocs\shopease
# database: import db_shopease.sql, ATAU buat database kosong "db_shopease" lalu:
php spark migrate
php spark db:seed DatabaseSeeder
php spark serve
```
Buka http://localhost:8080

## Akun demo (password semua: `shopease123`)
| Peran | Email |
|---|---|
| Admin | `admin@shopease.com` |
| Operator | `operator@shopease.com` |
| Customer | `budi@example.com`, `sari@example.com`, `rina@example.com` |

Mengulang data dari awal: `php spark migrate:refresh` lalu `php spark db:seed DatabaseSeeder`
(atau import ulang `db_shopease.sql`).

## Konfigurasi `.env`
```
CI_ENVIRONMENT = development
# app.baseURL = 'http://localhost/shopease/public/'   ← isi bila diakses bukan dari localhost
database.default.hostname = localhost
database.default.database = db_shopease
database.default.username = root
database.default.password =
```

## Struktur penting
```
app/Config/Routes.php          rute + filter role (auth:admin, auth:admin,operator, auth:customer)
app/Config/Shop.php            nama toko, kurir & ongkir, metode bayar, label status
app/Filters/AuthFilter.php     cek login & role (modul Authentication langkah 9)
app/Controllers/AuthController.php   register, login, logout (modul Authentication)
app/Controllers/…              Home, Product, Cart, Checkout, Order, Account
app/Controllers/Admin/…        Dashboard, Category, Product, Order, Payment, Shipment, Report, User
app/Controllers/Operator/…     Dashboard operator
app/Libraries/CartService.php  aturan keranjang & stok
app/Libraries/OrderService.php checkout dalam satu transaksi DB, batal, verifikasi, kirim, selesai
app/Database/Migrations, Seeds tabel (11) dan data contoh
public/assets/css/app.css      tema iOS
writable/uploads/payments      bukti transfer (tidak bisa diakses langsung dari URL)
```

## Catatan
- Semua produk, rekening "(contoh)", dan transaksi adalah data fiktif untuk praktikum.
- Gambar produk adalah ilustrasi buatan, bukan foto produk asli.
