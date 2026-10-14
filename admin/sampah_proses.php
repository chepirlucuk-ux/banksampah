<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'create') {
    $jenis = trim($_POST['jenis_sampah']);
    $harga = (int)$_POST['harga_per_kg'];
    $stok  = (float)$_POST['stok_kg'];
    $stmt = $pdo->prepare('INSERT INTO sampah (jenis_sampah, harga_per_kg, stok_kg) VALUES (?,?,?)');
    $stmt->execute([$jenis, $harga, $stok]);
    flash('sampah_msg', 'Jenis sampah berhasil ditambahkan.');
}

if ($action === 'update') {
    $id    = (int)$_POST['id_sampah'];
    $jenis = trim($_POST['jenis_sampah']);
    $harga = (int)$_POST['harga_per_kg'];
    $stok  = (float)$_POST['stok_kg'];
    $stmt = $pdo->prepare('UPDATE sampah SET jenis_sampah=?, harga_per_kg=?, stok_kg=? WHERE id_sampah=?');
    $stmt->execute([$jenis, $harga, $stok, $id]);
    flash('sampah_msg', 'Perubahan berhasil disimpan.');
}

if ($action === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $pdo->prepare('DELETE FROM sampah WHERE id_sampah=?');
    $stmt->execute([$id]);
    flash('sampah_msg', 'Jenis sampah berhasil dihapus.');
}

header('Location: sampah.php');
exit;
