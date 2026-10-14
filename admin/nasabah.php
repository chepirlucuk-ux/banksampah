<?php
$title = 'Nasabah';
require_once __DIR__ . '/includes/header.php';

$editData = null;
if (!empty($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id_user = ? AND role IN ('nasabah','pengepul')");
    $stmt->execute([(int)$_GET['edit']]);
    $editData = $stmt->fetch();
}

$list = $pdo->query("SELECT * FROM users WHERE role IN ('nasabah','pengepul') ORDER BY nama ASC")->fetchAll();
$msg = flash('nasabah_msg');
$err = flash('nasabah_err');
?>

<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endif; ?>

<div class="panel">
    <h3><?= $editData ? '✏️ Edit Nasabah' : '➕ Tambah Nasabah' ?></h3>
    <form action="nasabah_proses.php" method="POST" class="form-inline">
        <?php if ($editData): ?>
            <input type="hidden" name="id_user" value="<?= (int)$editData['id_user'] ?>">
        <?php endif; ?>
        <div class="form-group">
            <label>Nama</label>
            <input type="text" name="nama" required value="<?= e($editData['nama'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required value="<?= e($editData['username'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Password <?= $editData ? '(kosongkan jika tidak diubah)' : '' ?></label>
            <input type="text" name="password" <?= $editData ? '' : 'required' ?> placeholder="•••">
        </div>
        <div class="form-group">
            <label>Role</label>
            <?php $roleNow = $editData ? ($editData['role'] === 'pengepul' ? 'pengepul' : ($editData['kategori'] ?? 'siswa')) : 'siswa'; ?>
            <select name="role" <?= $editData ? 'disabled' : '' ?>>
                <option value="siswa"    <?= $roleNow==='siswa' ? 'selected' : '' ?>>Siswa</option>
                <option value="guru"     <?= $roleNow==='guru' ? 'selected' : '' ?>>Guru</option>
                <option value="pengepul" <?= $roleNow==='pengepul' ? 'selected' : '' ?>>Pengepul</option>
            </select>
        </div>
        <button type="submit" name="action" value="<?= $editData ? 'update' : 'create' ?>" class="btn btn-primary" style="width:auto">
            <?= $editData ? 'Simpan Perubahan' : 'Tambah' ?>
        </button>
        <?php if ($editData): ?>
            <a href="nasabah.php" class="btn btn-outline">Batal</a>
        <?php endif; ?>
    </form>
</div>

<div class="panel">
    <h3>👤 Daftar Nasabah</h3>
    <?php if (!$list): ?>
        <div class="empty-state">Belum ada nasabah.</div>
    <?php else: ?>
    <table>
        <thead><tr><th>Nama</th><th>Username</th><th>Role</th><th>Saldo</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php foreach ($list as $row): ?>
            <tr>
                <td><?= e($row['nama']) ?></td>
                <td><?= e($row['username']) ?></td>
                <td><?= $row['role']==='pengepul' ? 'Pengepul' : ucfirst($row['kategori'] ?: 'siswa') ?></td>
                <td><?= rupiah($row['saldo']) ?></td>
                <td>
                    <a class="btn btn-outline btn-small" href="nasabah.php?edit=<?= $row['id_user'] ?>">Edit</a>
                    <?php if ($row['role'] === 'nasabah'): ?>
                    <a class="btn btn-danger btn-small" href="nasabah_proses.php?action=delete&id=<?= $row['id_user'] ?>"
                       onclick="return confirm('Hapus nasabah ini? Semua riwayat setoran ikut terhapus.');">Hapus</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
