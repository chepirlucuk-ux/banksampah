<?php
$title = 'Tarik Saldo';
require_once __DIR__ . '/includes/header.php';

$id_user = $_SESSION['id_user'];

$stmt = $pdo->prepare('SELECT saldo FROM users WHERE id_user=?');
$stmt->execute([$id_user]);
$saldo = $stmt->fetchColumn();

$riwayatTarik = $pdo->prepare('SELECT * FROM tarik_saldo WHERE id_user=? ORDER BY tanggal DESC, id_tarik DESC');
$riwayatTarik->execute([$id_user]);
$riwayatTarik = $riwayatTarik->fetchAll();

$msg = flash('tarik_msg');
$err = flash('tarik_err');
?>

<div class="saldo-card">
    <div>
        <div class="label">💰 Saldo Tersedia</div>
        <div class="amount"><?= rupiah($saldo) ?></div>
    </div>
</div>

<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endif; ?>

<div class="panel">
    <h3>💸 Ajukan Penarikan Saldo</h3>
    <form action="tarik_proses.php" method="POST" class="form-inline">
        <div class="form-group">
            <label>Nominal Penarikan (Rp)</label>
            <input type="number" name="nominal" min="1000" max="<?= (int)$saldo ?>" step="500" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width:auto">Ajukan Tarik</button>
    </form>
</div>

<div class="panel">
    <h3>📋 Riwayat Pengajuan Penarikan</h3>
    <?php if (!$riwayatTarik): ?>
        <div class="empty-state">Belum pernah mengajukan penarikan.</div>
    <?php else: ?>
    <table>
        <thead><tr><th>Tanggal</th><th>Nominal</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach ($riwayatTarik as $row): ?>
            <tr>
                <td><?= tanggal_indo($row['tanggal']) ?></td>
                <td><?= rupiah($row['nominal']) ?></td>
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
