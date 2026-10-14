<?php
$title = 'Pengajuan Setor Sampah';
require_once __DIR__ . '/includes/header.php';

$list = $pdo->query("
    SELECT p.*, u.nama, u.role, u.kategori, s.jenis_sampah
    FROM pengajuan_setor p
    JOIN users u ON u.id_user = p.id_user
    JOIN sampah s ON s.id_sampah = p.id_sampah
    ORDER BY (p.status='pending') DESC, p.tanggal DESC, p.id_pengajuan DESC
")->fetchAll();

$msg = flash('pengajuan_msg');
?>

<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="panel">
    <h3>📨 Daftar Pengajuan Setor</h3>
    <p style="font-size:12.5px;color:#6c8f7c;margin:-4px 0 12px">Verifikasi setoran sampah dari siswa &amp; guru.</p>
    <?php if (!$list): ?>
        <div class="empty-state">Belum ada pengajuan setor.</div>
    <?php else: ?>
    <table>
        <thead><tr><th>Tanggal</th><th>Nasabah</th><th>Role</th><th>Jenis Sampah</th><th>Berat</th><th>Estimasi</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php foreach ($list as $row): ?>
            <tr>
                <td><?= tanggal_indo($row['tanggal']) ?></td>
                <td><?= e($row['nama']) ?></td>
                <td><?= ucfirst($row['kategori'] ?: 'siswa') ?></td>
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
                <td>
                    <?php if ($row['status']==='pending'): ?>
                        <a class="btn btn-primary btn-small" style="width:auto"
                           href="pengajuan_setor_proses.php?action=approve&id=<?= $row['id_pengajuan'] ?>"
                           onclick="return confirm('Setujui setoran ini? Saldo nasabah dan stok sampah akan bertambah.');">Setujui</a>
                        <a class="btn btn-danger btn-small"
                           href="pengajuan_setor_proses.php?action=reject&id=<?= $row['id_pengajuan'] ?>"
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
