<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('pengepul');

$id_pengepul = (int)$_SESSION['id_user'];
$id_sampah   = (int)($_POST['id_sampah'] ?? 0);
$berat       = (float)($_POST['berat_kg'] ?? 0);
$tanggal     = date('Y-m-d');

if ($id_sampah > 0 && $berat > 0) {
    $pdo->beginTransaction();
    try {
        $sp = $pdo->prepare('SELECT harga_per_kg, stok_kg FROM sampah WHERE id_sampah=? FOR UPDATE');
        $sp->execute([$id_sampah]);
        $sampah = $sp->fetch();
        $us = $pdo->prepare('SELECT saldo FROM users WHERE id_user=? FOR UPDATE');
        $us->execute([$id_pengepul]);
        $saldo = $us->fetchColumn();

        if (!$sampah) throw new Exception('Jenis sampah tidak ditemukan.');
        $harga = harga_jual($sampah['harga_per_kg']); // harga dasar + untung Rp1.000/kg
        $total = (int)round($harga * $berat);

        if ($berat > (float)$sampah['stok_kg']) {
            throw new Exception('Stok tidak cukup (tersedia ' . number_format($sampah['stok_kg'], 2) . ' kg).');
        }
        if ($total > (int)$saldo) {
            throw new Exception('Saldo tidak cukup. Butuh ' . rupiah($total) . ', saldo kamu ' . rupiah($saldo) . '. Hubungi admin untuk top up.');
        }

        $pdo->prepare('INSERT INTO penjualan (id_pengepul, id_sampah, berat_kg, harga_per_kg, total_rp, tanggal) VALUES (?,?,?,?,?,?)')
            ->execute([$id_pengepul, $id_sampah, $berat, $harga, $total, $tanggal]);
        $pdo->prepare('UPDATE users SET saldo = saldo - ? WHERE id_user=?')->execute([$total, $id_pengepul]);
        $pdo->prepare('UPDATE sampah SET stok_kg = stok_kg - ? WHERE id_sampah=?')->execute([$berat, $id_sampah]);

        $pdo->commit();
        flash('beli_msg', 'Pembelian berhasil: ' . number_format($berat, 2) . ' kg senilai ' . rupiah($total) . '.');
    } catch (Exception $e) {
        $pdo->rollBack();
        flash('beli_err', $e->getMessage());
    }
}
header('Location: dashboard.php');
exit;
