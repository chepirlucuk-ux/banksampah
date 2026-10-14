<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('nasabah');

$id_user   = (int)$_SESSION['id_user'];
$id_sampah = (int)($_POST['id_sampah'] ?? 0);
$berat     = (float)($_POST['berat_kg'] ?? 0);
$tanggal   = date('Y-m-d');

if ($id_sampah > 0 && $berat > 0) {
    $stmt = $pdo->prepare('SELECT harga_per_kg FROM sampah WHERE id_sampah=?');
    $stmt->execute([$id_sampah]);
    $harga = $stmt->fetchColumn();

    if ($harga === false) {
        flash('setor_err', 'Jenis sampah tidak ditemukan.');
    } else {
        $total = (int)round((int)$harga * $berat);
        $pdo->prepare("INSERT INTO pengajuan_setor (id_user, id_sampah, berat_kg, estimasi_rp, tanggal, status) VALUES (?,?,?,?,?,'pending')")
            ->execute([$id_user, $id_sampah, $berat, $total, $tanggal]);
        flash('setor_msg', 'Pengajuan setor berhasil dikirim, menunggu persetujuan admin. Estimasi ' . rupiah($total) . '.');
    }
} else {
    flash('setor_err', 'Data setoran tidak valid.');
}

header('Location: setor.php');
exit;
