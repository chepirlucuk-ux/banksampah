<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');

$action = $_GET['action'] ?? '';
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM tarik_saldo WHERE id_tarik=?');
$stmt->execute([$id]);
$data = $stmt->fetch();

if ($data && $data['status'] === 'pending') {
    if ($action === 'approve') {
        $u = $pdo->prepare('SELECT saldo FROM users WHERE id_user=?');
        $u->execute([$data['id_user']]);
        $saldo = (int)$u->fetchColumn();

        if ($saldo >= $data['nominal']) {
            $pdo->beginTransaction();
            try {
                $pdo->prepare("UPDATE tarik_saldo SET status='disetujui' WHERE id_tarik=?")->execute([$id]);
                $pdo->prepare('UPDATE users SET saldo = saldo - ? WHERE id_user=?')->execute([$data['nominal'], $data['id_user']]);
                $pdo->commit();
                flash('tarik_msg', 'Penarikan disetujui, saldo nasabah telah dikurangi.');
            } catch (Exception $e) {
                $pdo->rollBack();
                flash('tarik_msg', 'Gagal memproses penarikan.');
            }
        } else {
            $pdo->prepare("UPDATE tarik_saldo SET status='ditolak' WHERE id_tarik=?")->execute([$id]);
            flash('tarik_msg', 'Saldo nasabah tidak mencukupi, pengajuan otomatis ditolak.');
        }
    } elseif ($action === 'reject') {
        $pdo->prepare("UPDATE tarik_saldo SET status='ditolak' WHERE id_tarik=?")->execute([$id]);
        flash('tarik_msg', 'Pengajuan penarikan ditolak.');
    }
}

header('Location: tarik.php');
exit;
