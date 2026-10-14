<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');

$action = $_GET['action'] ?? '';
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM pengajuan_setor WHERE id_pengajuan=?');
$stmt->execute([$id]);
$data = $stmt->fetch();

if ($data && $data['status'] === 'pending') {
    if ($action === 'approve') {
        $pdo->beginTransaction();
        try {
            $h = $pdo->prepare('SELECT harga_per_kg FROM sampah WHERE id_sampah=?');
            $h->execute([$data['id_sampah']]);
            $harga = (int)$h->fetchColumn();
            $total = (int)round($harga * (float)$data['berat_kg']);

            $pdo->prepare("UPDATE pengajuan_setor SET status='disetujui' WHERE id_pengajuan=?")->execute([$id]);
            $pdo->prepare("INSERT INTO transaksi (id_user, id_sampah, tipe, berat_kg, total_rp, tanggal) VALUES (?,?,'setor',?,?,?)")
                ->execute([$data['id_user'], $data['id_sampah'], $data['berat_kg'], $total, $data['tanggal']]);
            $pdo->prepare('UPDATE users SET saldo = saldo + ? WHERE id_user=?')->execute([$total, $data['id_user']]);
            $pdo->prepare('UPDATE sampah SET stok_kg = stok_kg + ? WHERE id_sampah=?')->execute([$data['berat_kg'], $data['id_sampah']]);
            $pdo->commit();
            flash('pengajuan_msg', 'Setoran disetujui. Saldo nasabah bertambah ' . rupiah($total) . '.');
        } catch (Exception $e) {
            $pdo->rollBack();
            flash('pengajuan_msg', 'Gagal memproses pengajuan.');
        }
    } elseif ($action === 'reject') {
        $pdo->prepare("UPDATE pengajuan_setor SET status='ditolak' WHERE id_pengajuan=?")->execute([$id]);
        flash('pengajuan_msg', 'Pengajuan setor ditolak.');
    }
}

header('Location: pengajuan_setor.php');
exit;
