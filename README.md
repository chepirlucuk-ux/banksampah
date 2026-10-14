# Bank Sampah Chepir — banksampah_shafira

Sistem informasi bank sampah sekolah berbasis PHP native + MySQL, tema hijau daun.

## Cara Menjalankan (XAMPP / Laragon)

1. Salin folder `banksampah_shafira` ke `htdocs` (XAMPP) atau `www` (Laragon).
2. Buka **phpMyAdmin**, buat/isi database dengan cara import file:
   `database/banksampah_shafira.sql`
   (file ini otomatis membuat database `banksampah_shafira` beserta semua tabel dan data contoh).
3. Cek pengaturan koneksi di `config/database.php` (default: host `localhost`, user `root`, password kosong — sesuaikan jika perlu).
4. Buka di browser: `http://localhost/banksampah_shafira/`

## Akun Login

| Role    | Username | Password |
|---------|----------|----------|
| Admin   | admin    | 111      |
| Nasabah | chepir   | 123      |

## Fitur

**Admin**
- Dashboard ringkasan (jumlah nasabah, total setoran, total berat sampah, total saldo, penarikan pending)
- CRUD jenis sampah (nama, harga/kg, stok kg)
- CRUD data nasabah
- Input setoran sampah nasabah → saldo nasabah & stok sampah otomatis bertambah
- Approve / tolak pengajuan tarik saldo nasabah

**Nasabah**
- Halaman login dengan sapaan **"Welcome to Bank Sampah Chepir"**
- Dashboard: kartu saldo + tombol "Ajukan Tarik" + tabel riwayat setoran terakhir
- Ajukan penarikan saldo (status pending → menunggu admin)
- Lihat seluruh riwayat setoran & riwayat pengajuan penarikan

## Struktur Database (sesuai ERD)

- `users` (id_user, nama, username, password, saldo, role)
- `sampah` (id_sampah, jenis_sampah, harga_per_kg, stok_kg)
- `transaksi` (id_transaksi, id_user, id_sampah, tipe, berat_kg, total_rp, tanggal)
- `tarik_saldo` (id_tarik, id_user, nominal, tanggal, status)

## Update: Pengepul, Top Up Saldo & Statistik Penjualan

Database sekarang **satu file**: `database/banksampah_shafira.sql` (sudah termasuk tabel pengepul). Import ulang file ini; karena memuat `DROP TABLE`, data di database akan kembali ke data contoh.

| Role     | Username | Password |
|----------|----------|----------|
| Pengepul | pengepul | 123      |

- **Admin → Beranda**: bagian baru *Statistik Penjualan* (Hari Ini / Bulan Ini, dampak lingkungan, grafik 6 bulan, penjualan terakhir).
- **Admin → Pengepul**: tambah pengepul & top up saldo (saldo hanya ditambah, riwayat top up tidak dihapus).
- **Admin → Penjualan**: input penjualan sampah ke pengepul (stok berkurang, saldo pengepul terpotong).
- **Pengepul**: login untuk melihat saldo, riwayat pembelian & riwayat top up.
