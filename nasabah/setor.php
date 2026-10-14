<?php
$title = 'Setor Sampah';
require_once __DIR__ . '/includes/header.php';

$id_user = $_SESSION['id_user'];

$stmt = $pdo->prepare('SELECT saldo FROM users WHERE id_user=?');
$stmt->execute([$id_user]);
$saldo = $stmt->fetchColumn();

$sampahList = $pdo->query("SELECT id_sampah, jenis_sampah, harga_per_kg FROM sampah ORDER BY jenis_sampah ASC")->fetchAll();

$riwayat = $pdo->prepare("
    SELECT p.*, s.jenis_sampah
    FROM pengajuan_setor p
    JOIN sampah s ON s.id_sampah = p.id_sampah
    WHERE p.id_user = ?
    ORDER BY p.tanggal DESC, p.id_pengajuan DESC
    LIMIT 10
");
$riwayat->execute([$id_user]);
$riwayat = $riwayat->fetchAll();

$msg = flash('setor_msg');
$err = flash('setor_err');
?>

<div class="saldo-card">
    <div>
        <div class="label">💰 Saldo Anda</div>
        <div class="amount"><?= rupiah($saldo) ?></div>
    </div>
</div>

<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endif; ?>

<div class="panel">
    <h3>♻️ Setor Sampah</h3>
    <form action="setor_proses.php" method="POST" class="form-inline">
        <div class="form-group">
            <label>Jenis Sampah</label>
            <select name="id_sampah" required>
                <option value="">-- Pilih Jenis --</option>
                <?php foreach ($sampahList as $s): ?>
                    <option value="<?= $s['id_sampah'] ?>"><?= e($s['jenis_sampah']) ?> (<?= rupiah($s['harga_per_kg']) ?>/kg)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Berat (kg)</label>
            <input type="number" step="0.01" min="0.01" name="berat_kg" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width:auto">Ajukan Setor</button>
    </form>
    <p style="font-size:12.5px;color:#6c8f7c;margin:10px 0 0">Pengajuan akan diperiksa admin. Saldo bertambah setelah disetujui.</p>
</div>

<div class="panel">
    <h3>📜 Pengajuan Setor Terakhir</h3>
    <?php if (!$riwayat): ?>
        <div class="empty-state">Kamu belum pernah mengajukan setor sampah. Yuk mulai setor! 🌱</div>
    <?php else: ?>
    <table>
        <thead><tr><th>Tanggal</th><th>Jenis</th><th>Berat</th><th>Estimasi</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach ($riwayat as $row): ?>
            <tr>
                <td><?= tanggal_indo($row['tanggal']) ?></td>
                <td><?= e($row['jenis_sampah']) ?></td>
                <td><?= number_format($row['berat_kg'],2) ?> kg</td>
                <td><?= rupiah($row['estimasi_rp']) ?></td>
                <td>
                    <?php if ($row['status']==='pending'): ?>
                        <span class="badge badge-pending">Pending</span>
                    <?php elseif ($row['status']==='disetujui'): ?>
                        <span class="badge badge-ok">Disetujui</span>
                    <?php else: ?>
                        <span class="badge badge-no">Ditolak</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
