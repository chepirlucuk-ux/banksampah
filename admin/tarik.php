<?php
$title = 'Penarikan Saldo';
require_once __DIR__ . '/includes/header.php';

$list = $pdo->query("
    SELECT tr.*, u.nama, u.saldo
    FROM tarik_saldo tr
    JOIN users u ON u.id_user = tr.id_user
    ORDER BY (tr.status='pending') DESC, tr.tanggal DESC, tr.id_tarik DESC
")->fetchAll();

$msg = flash('tarik_msg');
?>

<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="panel">
    <h3>💸 Daftar Pengajuan Tarik Saldo</h3>
    <?php if (!$list): ?>
        <div class="empty-state">Belum ada pengajuan penarikan.</div>
    <?php else: ?>
    <table>
        <thead><tr><th>Tanggal</th><th>Nasabah</th><th>Nominal</th><th>Saldo Saat Ini</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php foreach ($list as $row): ?>
            <tr>
                <td><?= tanggal_indo($row['tanggal']) ?></td>
                <td><?= e($row['nama']) ?></td>
                <td><?= rupiah($row['nominal']) ?></td>
                <td><?= rupiah($row['saldo']) ?></td>
                <td>
                    <?php if ($row['status']==='pending'): ?>
                        <span class="badge badge-pending">Pending</span>
                    <?php elseif ($row['status']==='disetujui'): ?>
                        <span class="badge badge-ok">Disetujui</span>
                    <?php else: ?>
                        <span class="badge badge-no">Ditolak</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($row['status']==='pending'): ?>
                        <a class="btn btn-primary btn-small" style="width:auto"
                           href="tarik_proses.php?action=approve&id=<?= $row['id_tarik'] ?>"
                           onclick="return confirm('Setujui penarikan ini? Saldo nasabah akan langsung dikurangi.');">Setujui</a>
                        <a class="btn btn-danger btn-small"
                           href="tarik_proses.php?action=reject&id=<?= $row['id_tarik'] ?>"
                           onclick="return confirm('Tolak pengajuan ini?');">Tolak</a>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
