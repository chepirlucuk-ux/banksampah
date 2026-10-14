<?php
// Jalankan session di setiap halaman yang butuh login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_login() {
    if (empty($_SESSION['id_user'])) {
        header('Location: ../index.php');
        exit;
    }
}

function require_role($role) {
    require_login();
    if ($_SESSION['role'] !== $role) {
        header('Location: ../index.php');
        exit;
    }
}
