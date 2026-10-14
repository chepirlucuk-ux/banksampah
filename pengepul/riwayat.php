<?php
$title = 'Riwayat Pembelian';
require_once __DIR__ . '/includes/header.php';
$id_user = $_SESSION['id_user'];

$beli = $pdo->prepare("
    SELECT p.*, s.jenis_sampah FROM penjualan p
    JOIN sampah s ON s.id_sampah = p.id_sampah
    WHERE p.id_pengepul = ? ORDER BY p.tanggal DESC, p.id_penjualan DESC
");
$beli->execute([$id_user]);
$beli = $beli->fetchAll();

$topup = $pdo->prepare('SELECT * FROM topup_saldo WHERE id_user=? ORDER BY tanggal DESC, id_topup DESC');
$topup->execute([$id_user]);
$topup = $topup->fetchAll();
?>
<div class="panel">
    <h3>🧾 Riwayat Pembelian</h3>
    <?php if (!$beli): ?>
        <div class="empty-state">Belum ada pembelian.</div>
    <?php else: ?>
    <table>
        <thead><tr><th>Tanggal</th><th>Jenis</th><th>Berat</th><th>Harga/kg</th><th>Total Bayar</th></tr></thead>
        <tbody>
        <?php foreach ($beli as $r): ?>
            <tr>
                <td><?= tanggal_indo($r['tanggal']) ?></td>
                <td><?= e($r['jenis_sampah']) ?></td>
                <td><?= number_format($r['berat_kg'],2) ?> kg</td>
                <td><?= rupiah($r['harga_per_kg']) ?></td>
                <td><b style="color:var(--hijau)"><?= rupiah($r['total_rp']) ?></b></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<div class="panel">
    <h3>💳 Riwayat Top Up Saldo</h3>
    <?php if (!$topup): ?>
        <div class="empty-state">Belum ada top up.</div>
    <?php else: ?>
    <table>
        <thead><tr><th>Tanggal</th><th>Nominal</th><th>Keterangan</th></tr></thead>
        <tbody>
        <?php foreach ($topup as $r): ?>
            <tr><td><?= tanggal_indo($r['tanggal']) ?></td><td><?= rupiah($r['nominal']) ?></td><td><?= e($r['keterangan'] ?: '—') ?></td></tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
