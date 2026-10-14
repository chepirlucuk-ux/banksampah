<?php
$title = 'Jenis Sampah';
require_once __DIR__ . '/includes/header.php';

$editData = null;
if (!empty($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM sampah WHERE id_sampah = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editData = $stmt->fetch();
}

$list = $pdo->query('SELECT * FROM sampah ORDER BY jenis_sampah ASC')->fetchAll();
$msg = flash('sampah_msg');
?>

<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="panel">
    <h3><?= $editData ? '✏️ Edit Jenis Sampah' : '➕ Tambah Jenis Sampah' ?></h3>
    <form action="sampah_proses.php" method="POST" class="form-inline">
        <?php if ($editData): ?>
            <input type="hidden" name="id_sampah" value="<?= (int)$editData['id_sampah'] ?>">
        <?php endif; ?>
        <div class="form-group">
            <label>Jenis Sampah</label>
            <input type="text" name="jenis_sampah" required value="<?= e($editData['jenis_sampah'] ?? '') ?>" placeholder="mis. Plastik">
        </div>
        <div class="form-group">
            <label>Harga / kg (Rp)</label>
            <input type="number" name="harga_per_kg" min="0" required value="<?= e($editData['harga_per_kg'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Stok (kg)</label>
            <input type="number" step="0.01" min="0" name="stok_kg" required value="<?= e($editData['stok_kg'] ?? 0) ?>">
        </div>
        <button type="submit" name="action" value="<?= $editData ? 'update' : 'create' ?>" class="btn btn-primary" style="width:auto">
            <?= $editData ? 'Simpan Perubahan' : 'Tambah' ?>
        </button>
        <?php if ($editData): ?>
            <a href="sampah.php" class="btn btn-outline">Batal</a>
        <?php endif; ?>
    </form>
</div>

<div class="panel">
    <h3>🗑️ Daftar Jenis Sampah</h3>
    <?php if (!$list): ?>
        <div class="empty-state">Belum ada jenis sampah.</div>
    <?php else: ?>
    <table>
        <thead><tr><th>Jenis</th><th>Harga/kg</th><th>Stok (kg)</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php foreach ($list as $row): ?>
            <tr>
                <td><?= e($row['jenis_sampah']) ?></td>
                <td><?= rupiah($row['harga_per_kg']) ?></td>
                <td><?= number_format($row['stok_kg'],2) ?> kg</td>
                <td>
                    <a class="btn btn-outline btn-small" href="sampah.php?edit=<?= $row['id_sampah'] ?>">Edit</a>
                    <a class="btn btn-danger btn-small" href="sampah_proses.php?action=delete&id=<?= $row['id_sampah'] ?>"
                       onclick="return confirm('Hapus jenis sampah ini?');">Hapus</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
