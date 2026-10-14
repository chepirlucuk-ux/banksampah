<?php
$title = 'Beranda';
require_once __DIR__ . '/includes/header.php';

$totalNasabah = $pdo->query("SELECT COUNT(*) FROM users WHERE role='nasabah'")->fetchColumn();
$totalSetoran = $pdo->query("SELECT COUNT(*) FROM transaksi")->fetchColumn();
$totalBerat   = $pdo->query("SELECT COALESCE(SUM(berat_kg),0) FROM transaksi")->fetchColumn();
$totalSaldo   = $pdo->query("SELECT COALESCE(SUM(saldo),0) FROM users WHERE role='nasabah'")->fetchColumn();
$pendingTarik = $pdo->query("SELECT COUNT(*) FROM tarik_saldo WHERE status='pending'")->fetchColumn();

$terbaru = $pdo->query("
    SELECT t.*, u.nama, s.jenis_sampah
    FROM transaksi t
    JOIN users u ON u.id_user = t.id_user
    JOIN sampah s ON s.id_sampah = t.id_sampah
    ORDER BY t.tanggal DESC, t.id_transaksi DESC
    LIMIT 6
")->fetchAll();
?>

<div class="cards-row">
    <div class="stat-card" style="border-color:#40916c">
        <div class="label">👤 Total Nasabah</div>
        <div class="value"><?= $totalNasabah ?></div>
    </div>
    <div class="stat-card" style="border-color:#74c69d">
        <div class="label">📥 Total Setoran</div>
        <div class="value"><?= $totalSetoran ?>x</div>
    </div>
    <div class="stat-card" style="border-color:#b08968">
        <div class="label">🗑️ Total Berat Sampah</div>
        <div class="value"><?= number_format($totalBerat,1) ?> kg</div>
    </div>
    <div class="stat-card" style="border-color:#2d6a4f">
        <div class="label">💰 Total Saldo Nasabah</div>
        <div class="value"><?= rupiah($totalSaldo) ?></div>
    </div>
    <div class="stat-card" style="border-color:#d64545">
        <div class="label">⏳ Penarikan Pending</div>
        <div class="value"><?= $pendingTarik ?></div>
    </div>
</div>

<div class="panel">
    <h3>📋 Setoran Terbaru</h3>
    <?php if (!$terbaru): ?>
        <div class="empty-state">Belum ada data setoran.</div>
    <?php else: ?>
    <table>
        <thead>
            <tr><th>Tanggal</th><th>Nasabah</th><th>Jenis</th><th>Berat</th><th>Total</th></tr>
        </thead>
        <tbody>
        <?php foreach ($terbaru as $row): ?>
            <tr>
                <td><?= tanggal_indo($row['tanggal']) ?></td>
                <td><?= e($row['nama']) ?></td>
                <td><?= e($row['jenis_sampah']) ?></td>
                <td><?= number_format($row['berat_kg'],2) ?> kg</td>
                <td><?= rupiah($row['total_rp']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/statistik_penjualan.php'; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
