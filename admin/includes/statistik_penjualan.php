<?php
// =========================================================
// Statistik Penjualan (hari ini / bulan ini) — dipakai di admin/dashboard.php
// Bagian tambahan; tidak mengubah statistik lama.
// =========================================================
$hariIni  = date('Y-m-d');
$awalBln  = date('Y-m-01');
$akhirBln = date('Y-m-t');
$F_CO2 = 1.5;   // estimasi kg CO2 terhindar per kg sampah
$F_KWH = 0.45;  // estimasi kWh energi dihemat per kg sampah

function stat_periode($pdo, $dari, $sampai) {
    $q = $pdo->prepare("SELECT COUNT(*) AS jml, COALESCE(SUM(berat_kg),0) AS berat, COALESCE(SUM(total_rp),0) AS omzet
                        FROM penjualan WHERE tanggal BETWEEN ? AND ?");
    $q->execute([$dari, $sampai]);
    return $q->fetch();
}
$statHari  = stat_periode($pdo, $hariIni, $hariIni);
$statBulan = stat_periode($pdo, $awalBln, $akhirBln);

// 6 bulan terakhir (termasuk bulan ini)
$namaBln = [1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
$bulan6 = [];
for ($i = 5; $i >= 0; $i--) {
    $ts = strtotime(date('Y-m-01') . " -$i month");
    $bulan6[date('Y-m', $ts)] = ['label' => $namaBln[(int)date('n', $ts)], 'jml' => 0, 'omzet' => 0];
}
$q = $pdo->prepare("SELECT DATE_FORMAT(tanggal,'%Y-%m') AS ym, COUNT(*) AS jml, COALESCE(SUM(total_rp),0) AS omzet
                    FROM penjualan WHERE tanggal >= ? GROUP BY ym");
$q->execute([date('Y-m-01', strtotime(date('Y-m-01') . ' -5 month'))]);
foreach ($q->fetchAll() as $r) {
    if (isset($bulan6[$r['ym']])) { $bulan6[$r['ym']]['jml'] = (int)$r['jml']; $bulan6[$r['ym']]['omzet'] = (int)$r['omzet']; }
}
$maxOmzet = max(1, max(array_column($bulan6, 'omzet')));

$jualTerakhir = $pdo->query("
    SELECT p.*, u.nama, s.jenis_sampah
    FROM penjualan p
    JOIN users u ON u.id_user = p.id_pengepul
    JOIN sampah s ON s.id_sampah = p.id_sampah
    ORDER BY p.tanggal DESC, p.id_penjualan DESC
    LIMIT 6
")->fetchAll();
?>
<style>
.sp-head{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin:8px 0 14px}
.sp-head h3{margin:0;font-size:17px;color:var(--hijau-tua)}
.sp-tabs{display:inline-flex;background:#e3efe8;border-radius:10px;padding:3px}
.sp-tabs button{border:0;background:transparent;padding:7px 16px;border-radius:8px;font-weight:600;font-size:13px;color:#4f7a63;cursor:pointer}
.sp-tabs button.on{background:#fff;color:var(--hijau-tua);box-shadow:0 1px 4px rgba(0,0,0,.1)}
.sp-hero{background:linear-gradient(135deg,#40916c,#2d6a4f);color:#fff;border-radius:14px;padding:22px 26px;margin-bottom:16px}
.sp-hero .l{font-size:13px;opacity:.9}
.sp-hero .v{font-size:32px;font-weight:800;margin-top:4px}
.sp-hero .s{font-size:12.5px;opacity:.85;margin-top:2px}
.sp-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:16px}
.sp-mini{background:#fff;border-radius:12px;padding:16px 18px;box-shadow:0 2px 8px rgba(0,0,0,.05)}
.sp-mini .l{font-size:12px;color:#6c8f7c;font-weight:600}
.sp-mini .v{font-size:22px;font-weight:800;color:var(--hijau-tua);margin-top:4px}
.sp-impact{background:#e9f7ef;border:1px solid #cfead9;border-radius:12px;padding:16px 20px;margin-bottom:16px}
.sp-impact h4{margin:0 0 10px;font-size:14px;color:#1b5e3f}
.sp-impact .row{display:flex;gap:40px;flex-wrap:wrap}
.sp-impact b{font-size:16px;color:#1b5e3f}
.sp-impact small{display:block;font-size:12px;color:#5b8a71}
.sp-two{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.sp-chart{display:flex;align-items:flex-end;gap:12px;height:190px;padding:22px 4px 0;border-bottom:1px solid #dbe8e0}
.sp-chart .col{flex:1;display:flex;flex-direction:column;justify-content:flex-end;align-items:center;height:100%}
.sp-chart .bar{width:70%;max-width:46px;background:#40916c;border-radius:6px 6px 0 0;min-height:2px}
.sp-chart .val{font-size:11px;color:#4f7a63;margin-bottom:4px;white-space:nowrap}
.sp-lbls{display:flex;gap:12px;padding:6px 4px 0}
.sp-lbls span{flex:1;text-align:center;font-size:12px;color:#6c8f7c}
@media(max-width:860px){.sp-grid,.sp-two{grid-template-columns:1fr}}
</style>

<div class="sp-head">
    <h3>📈 Statistik Penjualan</h3>
    <div class="sp-tabs">
        <button type="button" id="spBtnHari" class="on" onclick="spTab('hari')">Hari Ini</button>
        <button type="button" id="spBtnBulan" onclick="spTab('bulan')">Bulan Ini</button>
    </div>
</div>

<?php foreach ([['hari', $statHari, tanggal_indo($hariIni), 'Penjualan hari ini'],
                ['bulan', $statBulan, $namaBln[(int)date('n')] . ' ' . date('Y'), 'Penjualan bulan ini']] as [$id, $st, $ket, $judul]): ?>
<div id="sp-<?= $id ?>" style="<?= $id === 'bulan' ? 'display:none' : '' ?>">
    <div class="sp-hero">
        <div class="l"><?= $judul ?> · <?= e($ket) ?></div>
        <div class="v"><?= rupiah($st['omzet']) ?></div>
        <div class="s">Total nilai penjualan sampah ke pengepul</div>
    </div>
    <div class="sp-grid">
        <div class="sp-mini"><div class="l">🧾 Total Penjualan</div><div class="v"><?= (int)$st['jml'] ?>x</div></div>
        <div class="sp-mini"><div class="l">⚖️ Total Sampah Terjual</div><div class="v"><?= number_format($st['berat'],1,',','.') ?> kg</div></div>
        <div class="sp-mini"><div class="l">💰 Keuntungan (<?= rupiah(UNTUNG_PER_KG) ?>/kg)</div><div class="v"><?= rupiah($st['berat'] * UNTUNG_PER_KG) ?></div></div>
    </div>
    <div class="sp-impact">
        <h4>🌍 Dampak Lingkungan</h4>
        <div class="row">
            <div><b><?= number_format($st['berat'] * $F_CO2,1,',','.') ?> kg</b><small>Estimasi emisi CO₂ yang terhindar</small></div>
            <div><b><?= number_format($st['berat'] * $F_KWH,2,',','.') ?> kWh</b><small>Estimasi energi yang dihemat</small></div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<div class="sp-two">
    <div class="panel">
        <h3>Penjualan 6 Bulan Terakhir</h3>
        <div class="sp-chart">
            <?php foreach ($bulan6 as $b): $h = round($b['omzet'] / $maxOmzet * 100); ?>
            <div class="col" title="<?= e($b['label']) ?>: <?= rupiah($b['omzet']) ?> (<?= $b['jml'] ?>x)">
                <?php if ($b['omzet'] > 0): ?><div class="val"><?= rupiah($b['omzet']) ?></div><?php endif; ?>
                <div class="bar" style="height:<?= $h ?>%"></div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="sp-lbls"><?php foreach ($bulan6 as $b): ?><span><?= e($b['label']) ?></span><?php endforeach; ?></div>
    </div>
    <div class="panel">
        <h3>Penjualan Terakhir</h3>
        <?php if (!$jualTerakhir): ?>
            <div class="empty-state">Belum ada data penjualan.</div>
        <?php else: ?>
        <table>
            <thead><tr><th>Tanggal</th><th>Pengepul</th><th>Jenis</th><th>Nominal</th></tr></thead>
            <tbody>
            <?php foreach ($jualTerakhir as $r): ?>
                <tr>
                    <td><?= date('d-m-Y', strtotime($r['tanggal'])) ?></td>
                    <td><?= e($r['nama']) ?></td>
                    <td><?= e($r['jenis_sampah']) ?></td>
                    <td><b><?= rupiah($r['total_rp']) ?></b></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
        <p style="text-align:right;margin:12px 0 0;"><a href="penjualan.php" style="color:var(--hijau);font-weight:600;font-size:13px;">Lihat semua penjualan →</a></p>
    </div>
</div>
<script>
function spTab(t){
    document.getElementById('sp-hari').style.display  = t==='hari'  ? '' : 'none';
    document.getElementById('sp-bulan').style.display = t==='bulan' ? '' : 'none';
    document.getElementById('spBtnHari').className  = t==='hari'  ? 'on' : '';
    document.getElementById('spBtnBulan').className = t==='bulan' ? 'on' : '';
}
</script>
