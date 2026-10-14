<?php
$title = 'Penjualan';
require_once __DIR__ . '/includes/header.php';

$pengepulList = $pdo->query("SELECT id_user, nama, saldo FROM users WHERE role='pengepul' ORDER BY nama ASC")->fetchAll();
$sampahList   = $pdo->query("SELECT id_sampah, jenis_sampah, harga_per_kg, stok_kg FROM sampah ORDER BY jenis_sampah ASC")->fetchAll();

$riwayat = $pdo->query("
    SELECT p.*, u.nama, s.jenis_sampah
    FROM penjualan p
    JOIN users u ON u.id_user = p.id_pengepul
    JOIN sampah s ON s.id_sampah = p.id_sampah
    ORDER BY p.tanggal DESC, p.id_penjualan DESC
")->fetchAll();

$msg = flash('penjualan_msg');
$err = flash('penjualan_err');
?>

<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endif; ?>

<div class="panel">
    <h3>🚚 Input Penjualan ke Pengepul</h3>
    <?php if (!$pengepulList): ?>
        <div class="empty-state">Belum ada pengepul. Tambahkan di menu Pengepul.</div>
    <?php else: ?>
    <form action="penjualan_proses.php" method="POST" class="form-inline">
        <div class="form-group">
            <label>Pengepul</label>
            <select name="id_pengepul" required>
                <option value="">-- Pilih Pengepul --</option>
                <?php foreach ($pengepulList as $p): ?>
                    <option value="<?= $p['id_user'] ?>"><?= e($p['nama']) ?> (saldo <?= rupiah($p['saldo']) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Jenis Sampah</label>
            <select name="id_sampah" required>
                <option value="">-- Pilih Jenis --</option>
                <?php foreach ($sampahList as $s): ?>
                    <option value="<?= $s['id_sampah'] ?>"><?= e($s['jenis_sampah']) ?> (<?= rupiah(harga_jual($s['harga_per_kg'])) ?>/kg · stok <?= number_format($s['stok_kg'],2) ?> kg)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group"><label>Berat (kg)</label><input type="number" step="0.01" min="0.01" name="berat_kg" required></div>
        <div class="form-group"><label>Tanggal</label><input type="date" name="tanggal" required value="<?= date('Y-m-d') ?>"></div>
        <button type="submit" class="btn btn-primary" style="width:auto">Simpan Penjualan</button>
    </form>
    <p style="font-size:12.5px;color:#6c8f7c;margin:10px 0 0">Harga jual = harga sampah + untung <?= rupiah(UNTUNG_PER_KG) ?>/kg. Stok berkurang dan saldo pengepul terpotong sebesar total penjualan.</p>
    <?php endif; ?>
</div>

<div class="panel">
    <h3>📋 Riwayat Semua Penjualan</h3>
    <?php if (!$riwayat): ?>
        <div class="empty-state">Belum ada data penjualan.</div>
    <?php else: ?>
    <table>
        <thead><tr><th>Tanggal</th><th>Pengepul</th><th>Jenis</th><th>Berat</th><th>Harga/kg</th><th>Total</th></tr></thead>
        <tbody>
        <?php foreach ($riwayat as $row): ?>
            <tr>
                <td><?= tanggal_indo($row['tanggal']) ?></td>
                <td><?= e($row['nama']) ?></td>
                <td><?= e($row['jenis_sampah']) ?></td>
                <td><?= number_format($row['berat_kg'],2) ?> kg</td>
                <td><?= rupiah($row['harga_per_kg']) ?></td>
                <td><?= rupiah($row['total_rp']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
