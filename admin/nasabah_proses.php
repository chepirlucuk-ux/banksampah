<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'create') {
    $nama = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $password = md5(trim($_POST['password']));

    $cek = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username=?');
    $cek->execute([$username]);
    if ($cek->fetchColumn() > 0) {
        flash('nasabah_err', 'Username sudah digunakan.');
    } else {
        $roleIn = $_POST['role'] ?? 'siswa';
        if ($roleIn === 'pengepul') {
            $stmt = $pdo->prepare("INSERT INTO users (nama, username, password, saldo, role) VALUES (?,?,?,0,'pengepul')");
            $stmt->execute([$nama, $username, $password]);
            flash('nasabah_msg', 'Pengepul berhasil ditambahkan.');
        } else {
            $kategori = $roleIn === 'guru' ? 'guru' : 'siswa';
            $stmt = $pdo->prepare("INSERT INTO users (nama, username, password, saldo, role, kategori) VALUES (?,?,?,0,'nasabah',?)");
            $stmt->execute([$nama, $username, $password, $kategori]);
            flash('nasabah_msg', 'Nasabah berhasil ditambahkan.');
        }
    }
}

if ($action === 'update') {
    $id = (int)$_POST['id_user'];
    $nama = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $cek = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username=? AND id_user<>?');
    $cek->execute([$username, $id]);
    if ($cek->fetchColumn() > 0) {
        flash('nasabah_err', 'Username sudah digunakan nasabah lain.');
    } else {
        if ($password !== '') {
            $stmt = $pdo->prepare('UPDATE users SET nama=?, username=?, password=? WHERE id_user=?');
            $stmt->execute([$nama, $username, md5($password), $id]);
        } else {
            $stmt = $pdo->prepare('UPDATE users SET nama=?, username=? WHERE id_user=?');
            $stmt->execute([$nama, $username, $id]);
        }
        flash('nasabah_msg', 'Perubahan nasabah berhasil disimpan.');
    }
}

if ($action === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $pdo->prepare("DELETE FROM users WHERE id_user=? AND role='nasabah'");
    $stmt->execute([$id]);
    flash('nasabah_msg', 'Nasabah berhasil dihapus.');
}

header('Location: nasabah.php');
exit;
