<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

// kalau sudah login, langsung lempar ke dashboard masing-masing
if (!empty($_SESSION['id_user'])) {
    header('Location: ' . ($_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : ($_SESSION['role'] === 'pengepul' ? 'pengepul/dashboard.php' : 'nasabah/dashboard.php')));
    exit;
}

$error = flash('login_error');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Bank Sampah Chepir</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-logo">♻️</div>
            <h1>Sistem Informasi <span class="brand">Bank Sampah</span></h1>
            <p class="subtitle">Kelola setoran &amp; saldo sampahmu dengan mudah</p>

            <div class="welcome-banner">
                🌿 Welcome to Bank Sampah Chepir 🌿
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error"><?= e($error) ?></div>
            <?php endif; ?>

            <form action="login_proses.php" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Masukkan username" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>

            <div class="login-footer">
                🍃 Bank Sampah Chepir &copy; <?= date('Y') ?> — Bersama kelola sampah, wujudkan lingkungan bersih
            </div>
        </div>
    </div>
</body>
</html>
