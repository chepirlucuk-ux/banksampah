<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');

$action = $_POST['action'] ?? '';

if ($action === 'create') {
    $nama = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if ($nama === '' || $username === '' || $password === '') {
        flash('pengepul_err', 'Semua kolom wajib diisi.');
    } else {
        $cek = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username=?');
        $cek->execute([$username]);
        if ($cek->fetchColumn() > 0) {
            flash('pengepul_err', 'Username sudah digunakan.');
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (nama, username, password, saldo, role) VALUES (?,?,?,0,'pengepul')");
            $stmt->execute([$nama, $username, md5($password)]);
            flash('pengepul_msg', 'Pengepul berhasil ditambahkan.');
        }
    }
}

if ($action === 'topup') {
    $id  = (int)($_POST['id_user'] ?? 0);
    $nom = (int)($_POST['nominal'] ?? 0);
    $ket = trim($_POST['keterangan'] ?? '');
    $tgl = $_POST['tanggal'] ?? date('Y-m-d');

    $cek = $pdo->prepare("SELECT COUNT(*) FROM users WHERE id_user=? AND role='pengepul'");
    $cek->execute([$id]);
    if ($nom > 0 && $cek->fetchColumn() > 0) {
        $pdo->beginTransaction();
        try {
            // saldo hanya DITAMBAH, tidak menimpa saldo sebelumnya
            $pdo->prepare('UPDATE users SET saldo = saldo + ? WHERE id_user=?')->execute([$nom, $id]);
            $pdo->prepare('INSERT INTO topup_saldo (id_user, nominal, keterangan, tanggal) VALUES (?,?,?,?)')
                ->execute([$id, $nom, $ket, $tgl]);
            $pdo->commit();
            flash('pengepul_msg', 'Top up ' . rupiah($nom) . ' berhasil. Saldo pengepul bertambah.');
        } catch (Exception $e) {
            $pdo->rollBack();
            flash('pengepul_err', 'Top up gagal.');
        }
    } else {
        flash('pengepul_err', 'Data top up tidak valid.');
    }
}

header('Location: pengepul.php');
exit;
