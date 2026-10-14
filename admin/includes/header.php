<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_role('admin');

$page = basename($_SERVER['PHP_SELF']);
function navclass($file, $page) { return $file === $page ? 'active' : ''; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($title) ? e($title) . ' - ' : '' ?>Admin Bank Sampah Chepir</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <div class="brand-box">
            <div class="icon">♻️</div>
            <div class="txt"><b>Bank Sampah</b><span>Chepir · Admin</span></div>
        </div>
        <nav>
            <a href="dashboard.php" class="<?= navclass('dashboard.php', $page) ?>">🏠 Beranda</a>
            <a href="sampah.php" class="<?= navclass('sampah.php', $page) ?>">🗑️ Jenis Sampah</a>
            <a href="transaksi.php" class="<?= navclass('transaksi.php', $page) ?>">📥 Setoran</a>
            <a href="pengajuan_setor.php" class="<?= navclass('pengajuan_setor.php', $page) ?>">📨 Pengajuan Setor</a>
            <a href="tarik.php" class="<?= navclass('tarik.php', $page) ?>">💸 Tarik Saldo</a>
            <a href="nasabah.php" class="<?= navclass('nasabah.php', $page) ?>">👤 Nasabah</a>
            <a href="pengepul.php" class="<?= navclass('pengepul.php', $page) ?>">🚚 Pengepul</a>
            <a href="penjualan.php" class="<?= navclass('penjualan.php', $page) ?>">🧾 Penjualan</a>
            <a href="../logout.php">🚪 Keluar</a>
        </nav>
    </aside>
    <main class="main">
        <div class="topbar">
            <h2><?= isset($title) ? e($title) : 'Beranda' ?></h2>
            <div class="user-chip">
                <div class="avatar"><?= strtoupper(substr($_SESSION['nama'],0,1)) ?></div>
                <span>Halo, <b><?= e($_SESSION['nama']) ?></b> (Admin)</span>
            </div>
        </div>
