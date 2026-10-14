<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('nasabah');

$id_user = $_SESSION['id_user'];
$nominal = (int)($_POST['nominal'] ?? 0);

$stmt = $pdo->prepare('SELECT saldo FROM users WHERE id_user=?');
$stmt->execute([$id_user]);
$saldo = (int)$stmt->fetchColumn();

if ($nominal <= 0) {
    flash('tarik_err', 'Nominal penarikan tidak valid.');
} elseif ($nominal > $saldo) {
    flash('tarik_err', 'Saldo Anda tidak mencukupi untuk penarikan sebesar itu.');
} else {
    $ins = $pdo->prepare("INSERT INTO tarik_saldo (id_user, nominal, tanggal, status) VALUES (?,?,?,'pending')");
    $ins->execute([$id_user, $nominal, date('Y-m-d')]);
    flash('tarik_msg', 'Pengajuan penarikan berhasil dikirim, menunggu persetujuan admin.');
}

header('Location: tarik.php');
exit;
