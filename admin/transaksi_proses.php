<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');

$id_user   = (int)($_POST['id_user'] ?? 0);
$id_sampah = (int)($_POST['id_sampah'] ?? 0);
$berat     = (float)($_POST['berat_kg'] ?? 0);
$tanggal   = $_POST['tanggal'] ?? date('Y-m-d');

if ($id_user > 0 && $id_sampah > 0 && $berat > 0) {
    $stmt = $pdo->prepare('SELECT harga_per_kg FROM sampah WHERE id_sampah=?');
    $stmt->execute([$id_sampah]);
    $harga = (int)$stmt->fetchColumn();
    $total = (int)round($harga * $berat);

    $pdo->beginTransaction();
    try {
        $ins = $pdo->prepare("INSERT INTO transaksi (id_user, id_sampah, tipe, berat_kg, total_rp, tanggal) VALUES (?,?,'setor',?,?,?)");
        $ins->execute([$id_user, $id_sampah, $berat, $total, $tanggal]);

        $updSaldo = $pdo->prepare('UPDATE users SET saldo = saldo + ? WHERE id_user=?');
        $updSaldo->execute([$total, $id_user]);

        $updStok = $pdo->prepare('UPDATE sampah SET stok_kg = stok_kg + ? WHERE id_sampah=?');
        $updStok->execute([$berat, $id_sampah]);

        $pdo->commit();
        flash('transaksi_msg', 'Setoran berhasil disimpan. Saldo nasabah otomatis bertambah ' . rupiah($total) . '.');
    } catch (Exception $e) {
        $pdo->rollBack();
        flash('transaksi_msg', 'Gagal menyimpan setoran.');
    }
}

header('Location: transaksi.php');
exit;
