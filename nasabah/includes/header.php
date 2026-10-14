<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_role('nasabah');

$page = basename($_SERVER['PHP_SELF']);
function navclass2($file, $page) { return $file === $page ? 'active' : ''; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($title) ? e($title) . ' - ' : '' ?>Bank Sampah Chepir</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <div class="brand-box">
            <div class="icon">♻️</div>
            <div class="txt"><b>Bank Sampah</b><span>Chepir</span></div>
        </div>
        <nav>
            <a href="dashboard.php" class="<?= navclass2('dashboard.php', $page) ?>">🏠 Beranda</a>
            <a href="setor.php" class="<?= navclass2('setor.php', $page) ?>">♻️ Setor Sampah</a>
            <a href="tarik.php" class="<?= navclass2('tarik.php', $page) ?>">💸 Tarik Saldo</a>
            <a href="riwayat.php" class="<?= navclass2('riwayat.php', $page) ?>">📜 Riwayat</a>
            <a href="../logout.php">🚪 Keluar</a>
        </nav>
    </aside>
    <main class="main">
        <div class="topbar">
            <h2><?= isset($title) ? e($title) : 'Beranda' ?></h2>
            <div class="user-chip">
                <div class="avatar"><?= strtoupper(substr($_SESSION['nama'],0,1)) ?></div>
                <span>Halo, <b><?= e($_SESSION['nama']) ?></b></span>
            </div>
        </div>
