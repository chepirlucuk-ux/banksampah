<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    flash('login_error', 'Username dan password wajib diisi.');
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user || md5($password) !== $user['password']) {
    flash('login_error', 'Username atau password salah.');
    header('Location: index.php');
    exit;
}

$_SESSION['id_user'] = $user['id_user'];
$_SESSION['nama']    = $user['nama'];
$_SESSION['username']= $user['username'];
$_SESSION['role']    = $user['role'];

header('Location: ' . ($user['role'] === 'admin' ? 'admin/dashboard.php' : ($user['role'] === 'pengepul' ? 'pengepul/dashboard.php' : 'nasabah/dashboard.php')));
exit;
