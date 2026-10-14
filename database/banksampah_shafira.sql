-- =========================================================
-- DATABASE: banksampah_shafira
-- Sistem Informasi Bank Sampah "Chepir"
-- =========================================================

CREATE DATABASE IF NOT EXISTS banksampah_shafira;
USE banksampah_shafira;

-- Reset: file ini bisa diimport ulang kapan saja (data lama ikut ter-reset)
DROP TABLE IF EXISTS pengajuan_setor;
DROP TABLE IF EXISTS penjualan;
DROP TABLE IF EXISTS topup_saldo;
DROP TABLE IF EXISTS tarik_saldo;
DROP TABLE IF EXISTS transaksi;
DROP TABLE IF EXISTS sampah;
DROP TABLE IF EXISTS users;

-- ---------------------------------------------------------
-- TABLE: users  (admin & nasabah)
-- ---------------------------------------------------------
CREATE TABLE users (
    id_user   INT AUTO_INCREMENT PRIMARY KEY,
    nama      VARCHAR(100) NOT NULL,
    username  VARCHAR(50)  NOT NULL UNIQUE,
    password  VARCHAR(255) NOT NULL,
    saldo     BIGINT NOT NULL DEFAULT 0,
    role      ENUM('admin','nasabah','pengepul') NOT NULL DEFAULT 'nasabah',
    kategori  ENUM('siswa','guru') NULL,  -- jenis nasabah: Siswa / Guru
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- TABLE: sampah  (master jenis sampah)
-- ---------------------------------------------------------
CREATE TABLE sampah (
    id_sampah     INT AUTO_INCREMENT PRIMARY KEY,
    jenis_sampah  VARCHAR(100) NOT NULL,
    harga_per_kg  INT NOT NULL DEFAULT 0,
    stok_kg       DECIMAL(10,2) NOT NULL DEFAULT 0
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- TABLE: transaksi  (setoran sampah oleh nasabah)
-- ---------------------------------------------------------
CREATE TABLE transaksi (
    id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
    id_user      INT NOT NULL,
    id_sampah    INT NOT NULL,
    tipe         VARCHAR(20) NOT NULL DEFAULT 'setor',
    berat_kg     DECIMAL(10,2) NOT NULL,
    total_rp     BIGINT NOT NULL,
    tanggal      DATE NOT NULL,
    CONSTRAINT fk_transaksi_user   FOREIGN KEY (id_user)   REFERENCES users(id_user)   ON DELETE CASCADE,
    CONSTRAINT fk_transaksi_sampah FOREIGN KEY (id_sampah) REFERENCES sampah(id_sampah) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- TABLE: tarik_saldo  (penarikan saldo nasabah)
-- ---------------------------------------------------------
CREATE TABLE tarik_saldo (
    id_tarik  INT AUTO_INCREMENT PRIMARY KEY,
    id_user   INT NOT NULL,
    nominal   BIGINT NOT NULL,
    tanggal   DATE NOT NULL,
    status    ENUM('pending','disetujui','ditolak') NOT NULL DEFAULT 'pending',
    CONSTRAINT fk_tarik_user FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================================================
-- TABEL PENGEPUL: top up saldo & penjualan
-- =========================================================
-- Riwayat top up saldo pengepul (hanya bertambah, tidak pernah dihapus)
CREATE TABLE topup_saldo (
    id_topup   INT AUTO_INCREMENT PRIMARY KEY,
    id_user    INT NOT NULL,
    nominal    BIGINT NOT NULL,
    keterangan VARCHAR(150) NULL,
    tanggal    DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_topup_user FOREIGN KEY (id_user) REFERENCES users(id_user)
) ENGINE=InnoDB;

-- Penjualan sampah ke pengepul
CREATE TABLE penjualan (
    id_penjualan INT AUTO_INCREMENT PRIMARY KEY,
    id_pengepul  INT NOT NULL,
    id_sampah    INT NOT NULL,
    berat_kg     DECIMAL(10,2) NOT NULL,
    harga_per_kg INT NOT NULL,
    total_rp     BIGINT NOT NULL,
    tanggal      DATE NOT NULL,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_penjualan_user   FOREIGN KEY (id_pengepul) REFERENCES users(id_user),
    CONSTRAINT fk_penjualan_sampah FOREIGN KEY (id_sampah)   REFERENCES sampah(id_sampah)
) ENGINE=InnoDB;


-- ---------------------------------------------------------
-- TABLE: pengajuan_setor  (setor sampah siswa/guru, menunggu persetujuan admin)
-- ---------------------------------------------------------
CREATE TABLE pengajuan_setor (
    id_pengajuan INT AUTO_INCREMENT PRIMARY KEY,
    id_user      INT NOT NULL,
    id_sampah    INT NOT NULL,
    berat_kg     DECIMAL(10,2) NOT NULL,
    estimasi_rp  BIGINT NOT NULL,
    tanggal      DATE NOT NULL,
    status       ENUM('pending','disetujui','ditolak') NOT NULL DEFAULT 'pending',
    CONSTRAINT fk_pengajuan_user   FOREIGN KEY (id_user)   REFERENCES users(id_user)   ON DELETE CASCADE,
    CONSTRAINT fk_pengajuan_sampah FOREIGN KEY (id_sampah) REFERENCES sampah(id_sampah) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================================================
-- SEED DATA
-- =========================================================

-- Akun login
-- username: admin | password: 111 (nasabah & pengepul: lihat daftar di bawah)
INSERT INTO users (nama, username, password, saldo, role, kategori) VALUES
('Administrator', 'admin', '698d51a19d8a121ce581499d7b701668', 0, 'admin', NULL),
('Chepir', 'chepir', '202cb962ac59075b964b07152d234b70', 120000, 'nasabah', 'siswa'),
('nata', 'nata', 'c8ffe9a587b126f152ed3d89a146b445', 5100, 'nasabah', 'siswa'),
('novi', 'novi', '3def184ad8f4755ff269862ea77393dd', 32100, 'nasabah', 'siswa'),
('dine', 'dine', '069059b7ef840f0c74a814ec9237b6ec', 37500, 'nasabah', 'siswa'),
('sifa', 'sifa', 'ec5decca5ed3d6b8079e2e7e7bacc9f2', 15000, 'nasabah', 'siswa'),
('bu bherta', 'bu bherta', '7ef605fc8dba5425d6965fbd4c8fbe1f', 10000, 'nasabah', 'guru'),
('bu zulfa', 'bu zulfa', '37a749d808e46495a8da1e5352d03cae', 67500, 'nasabah', 'guru'),
('pak rofik', 'pak rofik', 'b3e3e393c77e35a4a3f3cbd1e429b5dc', 140000, 'nasabah', 'guru'),
('pak heru', 'pak heru', '1d7f7abc18fcb43975065399b0d1e48e', 180000, 'nasabah', 'guru'),
('bu nia', 'bu nia', '2a79ea27c279e471f4d180b08d62b00a', 140000, 'nasabah', 'guru'),
('PT.CINTA SAMPAH', 'pengepul', '202cb962ac59075b964b07152d234b70', 276500, 'pengepul', NULL),
('PT.SAYANG FIRA', 'pengepul2', '3644a684f98ea8fe223c713b77189a77', 0, 'pengepul', NULL),
('PT.GO GREEN', 'pengepul3', '757b505cfd34c64c85ca5b5690ee5293', 0, 'pengepul', NULL),
('PT.SUKA SAMPAH', 'pengepul4', '854d6fae5ee42911677c739ee1734486', 0, 'pengepul', NULL);

-- Master jenis sampah
INSERT INTO sampah (jenis_sampah, harga_per_kg, stok_kg) VALUES
('Plastik', 2000, 120.00),
('Kertas',  1500, 80.00),
('Kardus',  1700, 95.00),
('Kaca',    1000, 40.00),
('Logam',   3000, 25.00);

-- Riwayat setoran nasabah "chepir" (id_user = 2)
INSERT INTO transaksi (id_user, id_sampah, tipe, berat_kg, total_rp, tanggal) VALUES
(2, 1, 'setor', 5.25, 10500, '2026-07-25'),
(2, 4, 'setor', 4.00, 4000,  '2026-08-01'),
(2, 5, 'setor', 3.00, 9000,  '2026-08-10'),
(2, 2, 'setor', 5.00, 7500,  '2026-08-20'),
(2, 3, 'setor', 6.00, 10000, '2026-08-28'),
(2, 1, 'setor', 2.00, 4000,  '2026-09-01');

-- ---------------------------------------------------------
-- DATA CONTOH PENGEPUL (opsional)
-- Pengepul contoh: PT.CINTA SAMPAH (username pengepul)
-- ---------------------------------------------------------
SET @p = (SELECT id_user FROM users WHERE username='pengepul');

INSERT INTO topup_saldo (id_user, nominal, keterangan, tanggal) VALUES
(@p, 400000, 'Top up awal',  '2026-06-01'),
(@p, 165000, 'Top up tambahan', '2026-09-01');

INSERT INTO penjualan (id_pengepul, id_sampah, berat_kg, harga_per_kg, total_rp, tanggal) VALUES
(@p, 1, 20.00, 3000, 60000, '2026-07-15'),
(@p, 3, 30.00, 2700, 81000, '2026-08-20'),
(@p, 2, 25.00, 2500, 62500, '2026-09-12'),
(@p, 5, 10.00, 4000, 40000, '2026-09-30'),
(@p, 1, 15.00, 3000, 45000, '2026-09-30');

-- stok sampah menyesuaikan contoh penjualan di atas
UPDATE sampah SET stok_kg = stok_kg - 35 WHERE id_sampah = 1;
UPDATE sampah SET stok_kg = stok_kg - 25 WHERE id_sampah = 2;
UPDATE sampah SET stok_kg = stok_kg - 30 WHERE id_sampah = 3;
UPDATE sampah SET stok_kg = stok_kg - 10 WHERE id_sampah = 5;
