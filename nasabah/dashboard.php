<?php
$title = 'Beranda';
require_once __DIR__ . '/includes/header.php';

$id_user = $_SESSION['id_user'];

$stmt = $pdo->prepare('SELECT saldo FROM users WHERE id_user=?');
$stmt->execute([$id_user]);
$saldo = $stmt->fetchColumn();

$riwayat = $pdo->prepare("
    SELECT t.*, s.jenis_sampah
    FROM transaksi t
    JOIN sampah s ON s.id_sampah = t.id_sampah
    WHERE t.id_user = ?
    ORDER BY t.tanggal DESC, t.id_transaksi DESC
    LIMIT 5
");
$riwayat->execute([$id_user]);
$riwayat = $riwayat->fetchAll();
?>

<div class="saldo-card">
    <div>
        <div class="label">💰 Kartu Saldo Anda</div>
        <div class="amount"><?= rupiah($saldo) ?></div>
    </div>
    <a href="tarik.php" class="btn btn-outline" style="background:#fff;">Ajukan Tarik</a>
</div>

<div class="panel">
    <h3>📜 Tabel Riwayat Setoran Terakhir</h3>
    <?php if (!$riwayat): ?>
        <div class="empty-state">Kamu belum pernah setor sampah. Yuk mulai setor! 🌱</div>
    <?php else: ?>
    <table>
        <thead><tr><th>Tanggal</th><th>Jenis</th><th>Berat</th><th>Total Rp</th></tr></thead>
        <tbody>
        <?php foreach ($riwayat as $row): ?>
            <tr>
                <td><?= tanggal_indo($row['tanggal']) ?></td>
                <td><?= e($row['jenis_sampah']) ?></td>
                <td><?= number_format($row['berat_kg'],2) ?> kg</td>
                <td><?= rupiah($row['total_rp']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <p style="text-align:right;margin:12px 0 0;">
        <a href="riwayat.php" style="color:var(--hijau);font-weight:600;font-size:13px;">Lihat semua riwayat →</a>
    </p>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
