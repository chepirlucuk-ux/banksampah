<?php
$title = 'Setoran Sampah';
require_once __DIR__ . '/includes/header.php';

$nasabahList = $pdo->query("SELECT id_user, nama FROM users WHERE role='nasabah' ORDER BY nama ASC")->fetchAll();
$sampahList  = $pdo->query("SELECT id_sampah, jenis_sampah, harga_per_kg FROM sampah ORDER BY jenis_sampah ASC")->fetchAll();

$riwayat = $pdo->query("
    SELECT t.*, u.nama, s.jenis_sampah
    FROM transaksi t
    JOIN users u ON u.id_user = t.id_user
    JOIN sampah s ON s.id_sampah = t.id_sampah
    ORDER BY t.tanggal DESC, t.id_transaksi DESC
")->fetchAll();

$msg = flash('transaksi_msg');
?>

<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="panel">
    <h3>📥 Input Setoran Sampah</h3>
    <form action="transaksi_proses.php" method="POST" class="form-inline">
        <div class="form-group">
            <label>Nasabah</label>
            <select name="id_user" required>
                <option value="">-- Pilih Nasabah --</option>
                <?php foreach ($nasabahList as $n): ?>
                    <option value="<?= $n['id_user'] ?>"><?= e($n['nama']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Jenis Sampah</label>
            <select name="id_sampah" required>
                <option value="">-- Pilih Jenis --</option>
                <?php foreach ($sampahList as $s): ?>
                    <option value="<?= $s['id_sampah'] ?>">
                        <?= e($s['jenis_sampah']) ?> (<?= rupiah($s['harga_per_kg']) ?>/kg)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Berat (kg)</label>
            <input type="number" step="0.01" min="0.01" name="berat_kg" required>
        </div>
        <div class="form-group">
            <label>Tanggal</label>
            <input type="date" name="tanggal" required value="<?= date('Y-m-d') ?>">
        </div>
        <button type="submit" class="btn btn-primary" style="width:auto">Simpan Setoran</button>
    </form>
</div>

<div class="panel">
    <h3>📋 Riwayat Semua Setoran</h3>
    <?php if (!$riwayat): ?>
        <div class="empty-state">Belum ada data setoran.</div>
    <?php else: ?>
    <table>
        <thead><tr><th>Tanggal</th><th>Nasabah</th><th>Jenis</th><th>Berat</th><th>Total</th></tr></thead>
        <tbody>
        <?php foreach ($riwayat as $row): ?>
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

<?php require_once __DIR__ . '/includes/footer.php'; ?>
