<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_role('pengepul');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($title) ? e($title) . ' - ' : '' ?>Pengepul Bank Sampah Chepir</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
/* Layout dashboard pengepul (warna mengikuti tema hijau utama) */
.pg-cards{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:22px}
.pg-card{background:#fff;border-radius:14px;padding:16px 18px;box-shadow:0 2px 10px rgba(0,0,0,.05)}
.pg-card .l{font-size:12px;color:#6c8f7c;font-weight:600}
.pg-card .v{font-size:22px;font-weight:800;color:var(--hijau-tua);margin-top:4px}
.btn-beli{background:var(--hijau);color:#fff;border:0;border-radius:8px;padding:8px 16px;font-weight:700;cursor:pointer}
.btn-beli:hover{background:var(--hijau-sedang)}
.beli-form{display:flex;gap:8px;justify-content:flex-end}
.beli-form input{width:90px;padding:8px 10px;border:1px solid #cfe3d6;border-radius:8px}
.link-oren{color:var(--hijau);font-weight:600;font-size:13px}
@media(max-width:860px){.pg-cards{grid-template-columns:1fr 1fr}}
</style>
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <div class="brand-box">
            <div class="icon">♻️</div>
            <div class="txt"><b>Bank Sampah</b><span>Chepir · Pengepul</span></div>
        </div>
        <nav>
            <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF'])==='dashboard.php' ? 'active' : '' ?>">🏠 Beranda</a>
            <a href="riwayat.php" class="<?= basename($_SERVER['PHP_SELF'])==='riwayat.php' ? 'active' : '' ?>">🧾 Riwayat Pembelian</a>
            <a href="../logout.php">🚪 Keluar</a>
        </nav>
    </aside>
    <main class="main">
        <div class="topbar">
            <h2><?= isset($title) ? e($title) : 'Beranda' ?></h2>
            <div class="user-chip">
                <div class="avatar"><?= strtoupper(substr($_SESSION['nama'],0,1)) ?></div>
                <span>Halo, <b><?= e($_SESSION['nama']) ?></b> (Pengepul)</span>
            </div>
        </div>
