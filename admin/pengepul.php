<?php
$title = 'Pengepul';
require_once __DIR__ . '/includes/header.php';

$list = $pdo->query("SELECT * FROM users WHERE role='pengepul' ORDER BY nama ASC")->fetchAll();
$riwayatTopup = $pdo->query("
    SELECT t.*, u.nama FROM topup_saldo t
    JOIN users u ON u.id_user = t.id_user
    ORDER BY t.tanggal DESC, t.id_topup DESC
")->fetchAll();

$msg = flash('pengepul_msg');
$err = flash('pengepul_err');
?>

<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endif; ?>

<div class="panel">
    <h3>💳 Top Up Saldo Pengepul</h3>
    <?php if (!$list): ?>
        <div class="empty-state">Belum ada pengepul. Tambahkan dulu di atas.</div>
    <?php else: ?>
    <form action="pengepul_proses.php" method="POST" class="form-inline">
        <div class="form-group">
            <label>Pengepul</label>
            <select name="id_user" required>
                <option value="">-- Pilih Pengepul --</option>
                <?php foreach ($list as $p): ?>
                    <option value="<?= $p['id_user'] ?>"><?= e($p['nama']) ?> (saldo <?= rupiah($p['saldo']) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group"><label>Nominal Top Up (Rp)</label><input type="number" min="1" name="nominal" required></div>
        <div class="form-group"><label>Keterangan (opsional)</label><input type="text" name="keterangan" maxlength="150"></div>
        <div class="form-group"><label>Tanggal</label><input type="date" name="tanggal" required value="<?= date('Y-m-d') ?>"></div>
        <button type="submit" name="action" value="topup" class="btn btn-primary" style="width:auto">Top Up</button>
    </form>
    <p style="font-size:12.5px;color:#6c8f7c;margin:10px 0 0">Top up akan <b>menambah</b> saldo yang sudah ada; riwayat sebelumnya tidak dihapus.</p>
    <?php endif; ?>
</div>

<div class="panel">
    <h3>🚚 Daftar Pengepul</h3>
    <?php if (!$list): ?>
        <div class="empty-state">Belum ada pengepul.</div>
    <?php else: ?>
    <table>
        <thead><tr><th>Nama</th><th>Username</th><th>Saldo</th></tr></thead>
        <tbody>
        <?php foreach ($list as $row): ?>
            <tr><td><?= e($row['nama']) ?></td><td><?= e($row['username']) ?></td><td><?= rupiah($row['saldo']) ?></td></tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<div class="panel">
    <h3>📋 Riwayat Top Up</h3>
    <?php if (!$riwayatTopup): ?>
        <div class="empty-state">Belum ada top up.</div>
    <?php else: ?>
    <table>
        <thead><tr><th>Tanggal</th><th>Pengepul</th><th>Nominal</th><th>Keterangan</th></tr></thead>
        <tbody>
        <?php foreach ($riwayatTopup as $r): ?>
            <tr>
                <td><?= tanggal_indo($r['tanggal']) ?></td>
                <td><?= e($r['nama']) ?></td>
                <td><?= rupiah($r['nominal']) ?></td>
                <td><?= e($r['keterangan'] ?: '—') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
