<?php
$title = 'Beranda';
require_once __DIR__ . '/includes/header.php';

$id_user = $_SESSION['id_user'];
$stmt = $pdo->prepare('SELECT saldo FROM users WHERE id_user=?');
$stmt->execute([$id_user]);
$saldo = $stmt->fetchColumn();

$awal = date('Y-m-01'); $akhir = date('Y-m-t');
$q = $pdo->prepare('SELECT COUNT(*) c, COALESCE(SUM(berat_kg),0) kg, COALESCE(SUM(total_rp),0) rp FROM penjualan WHERE id_pengepul=? AND tanggal BETWEEN ? AND ?');
$q->execute([$id_user, $awal, $akhir]);
$bln = $q->fetch();
$q = $pdo->prepare('SELECT COALESCE(SUM(berat_kg),0) FROM penjualan WHERE id_pengepul=?');
$q->execute([$id_user]);
$kgAll = $q->fetchColumn();

$stok = $pdo->query('SELECT * FROM sampah WHERE stok_kg > 0 ORDER BY jenis_sampah ASC')->fetchAll();

$beli = $pdo->prepare("
    SELECT p.*, s.jenis_sampah FROM penjualan p
    JOIN sampah s ON s.id_sampah = p.id_sampah
    WHERE p.id_pengepul = ? ORDER BY p.tanggal DESC, p.id_penjualan DESC LIMIT 5
");
$beli->execute([$id_user]);
$beli = $beli->fetchAll();

$msg = flash('beli_msg');
$err = flash('beli_err');
?>

<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endif; ?>

<div class="saldo-card">
    <div>
        <div class="label">Saldo Beli Kamu Saat Ini</div>
        <div class="amount"><?= rupiah($saldo) ?></div>
        <a href="riwayat.php" class="btn btn-outline" style="margin-top:12px;background:rgba(255,255,255,.2);color:#fff;border-color:rgba(255,255,255,.6);width:auto">Lihat Riwayat Pembelian</a>
    </div>
</div>

<div class="pg-cards">
    <div class="pg-card"><div class="l">📦 Pembelian Bulan Ini</div><div class="v"><?= (int)$bln['c'] ?>x</div></div>
    <div class="pg-card"><div class="l">⚖️ Kg Dibeli Bulan Ini</div><div class="v"><?= number_format($bln['kg'],1,',','.') ?> kg</div></div>
    <div class="pg-card"><div class="l">💵 Total Dibelanjakan Bulan Ini</div><div class="v"><?= rupiah($bln['rp']) ?></div></div>
    <div class="pg-card"><div class="l">♻️ Total Kg Sepanjang Waktu</div><div class="v"><?= number_format($kgAll,1,',','.') ?> kg</div></div>
</div>

<div class="panel">
    <h3>Beli Sampah dari Stok Bank Sampah</h3>
    <?php if (!$stok): ?>
        <div class="empty-state">Stok sampah sedang kosong.</div>
    <?php else: ?>
    <table>
        <thead><tr><th>Jenis Sampah</th><th>Stok Tersedia</th><th>Harga / Kg</th><th style="text-align:right">Beli</th></tr></thead>
        <tbody>
        <?php foreach ($stok as $s): ?>
            <tr>
                <td><b><?= e($s['jenis_sampah']) ?></b></td>
                <td><?= number_format($s['stok_kg'],1,',','.') ?> kg</td>
                <td><?= rupiah(harga_jual($s['harga_per_kg'])) ?></td>
                <td>
                    <form action="beli_proses.php" method="POST" class="beli-form">
                        <input type="hidden" name="id_sampah" value="<?= (int)$s['id_sampah'] ?>">
                        <input type="number" step="0.01" min="0.01" max="<?= e($s['stok_kg']) ?>" name="berat_kg" placeholder="Kg" required>
                        <button type="submit" class="btn-beli" onclick="return confirm('Beli sampah <?= e($s['jenis_sampah']) ?>? Saldo akan langsung terpotong.');">Beli</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<div class="panel">
    <h3>Pembelian Terakhir</h3>
    <?php if (!$beli): ?>
        <div class="empty-state">Belum ada pembelian.</div>
    <?php else: ?>
    <table>
        <thead><tr><th>Tanggal</th><th>Jenis</th><th>Berat</th><th>Total Bayar</th></tr></thead>
        <tbody>
        <?php foreach ($beli as $r): ?>
            <tr>
                <td><?= date('d-m-Y', strtotime($r['tanggal'])) ?></td>
                <td><?= e($r['jenis_sampah']) ?></td>
                <td><?= number_format($r['berat_kg'],2) ?> kg</td>
                <td><b style="color:var(--hijau)"><?= rupiah($r['total_rp']) ?></b></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <p style="text-align:right;margin:12px 0 0"><a class="link-oren" href="riwayat.php">Lihat semua riwayat →</a></p>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
