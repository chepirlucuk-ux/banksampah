<?php
$title = 'Riwayat Setoran';
require_once __DIR__ . '/includes/header.php';

$id_user = $_SESSION['id_user'];
$riwayat = $pdo->prepare("
    SELECT t.*, s.jenis_sampah
    FROM transaksi t
    JOIN sampah s ON s.id_sampah = t.id_sampah
    WHERE t.id_user = ?
    ORDER BY t.tanggal DESC, t.id_transaksi DESC
");
$riwayat->execute([$id_user]);
$riwayat = $riwayat->fetchAll();

$totalBerat = array_sum(array_column($riwayat, 'berat_kg'));
$totalRp    = array_sum(array_column($riwayat, 'total_rp'));
?>

<div class="cards-row">
    <div class="stat-card"><div class="label">Total Setoran</div><div class="value"><?= count($riwayat) ?>x</div></div>
    <div class="stat-card"><div class="label">Total Berat</div><div class="value"><?= number_format($totalBerat,2) ?> kg</div></div>
    <div class="stat-card"><div class="label">Total Rupiah Terkumpul</div><div class="value"><?= rupiah($totalRp) ?></div></div>
</div>

<div class="panel">
    <h3>📜 Semua Riwayat Setoran</h3>
    <?php if (!$riwayat): ?>
        <div class="empty-state">Belum ada riwayat setoran.</div>
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
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
