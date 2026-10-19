<?php
require_once '../auth.php'; require_petugas(); include '../connection.php';
$selected=(int)($_GET['id_peminjaman']??0);
$data=mysqli_query($conn,"SELECT p.id_peminjaman,p.tanggal_jatuh_tempo,a.nama_anggota,b.judul FROM peminjaman p JOIN anggota a ON p.id_anggota=a.id_anggota JOIN buku b ON p.id_buku=b.id_buku WHERE p.status='dipinjam' ORDER BY p.id_peminjaman DESC");
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Pengembalian Buku</title><link rel="stylesheet" href="../style.css"></head><body>
<div class="page-head"><div><h1>📝 Pengembalian Buku</h1><p class="muted">Pilih peminjaman aktif. Denda dihitung otomatis berdasarkan keterlambatan.</p></div><a class="btn btn-light" href="../dashboard.<?= htmlspecialchars($_SESSION['role']) ?>.php">← Kembali ke Dashboard</a></div>
<form action="tambah.pengembalian.php" method="POST">
<label>Peminjaman</label>
<select name="id_peminjaman" required><option value="">-- Pilih Peminjaman --</option>
<?php while($r=mysqli_fetch_assoc($data)): ?><option value="<?= (int)$r['id_peminjaman'] ?>" <?= $selected===(int)$r['id_peminjaman']?'selected':'' ?>>ID <?= (int)$r['id_peminjaman'] ?> — <?= htmlspecialchars($r['nama_anggota']) ?> — <?= htmlspecialchars($r['judul']) ?> — jatuh tempo <?= htmlspecialchars($r['tanggal_jatuh_tempo']) ?></option><?php endwhile; ?></select>
<label>Tanggal Kembali</label><input id="tanggal_kembali" type="date" name="tanggal_kembali" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>" required>
<div class="info" id="denda-preview">Denda otomatis: Rp 0</div>
<label>Keterangan</label><textarea name="keterangan" maxlength="100" placeholder="Contoh: Buku dikembalikan dengan baik"></textarea>
<div class="form-actions"><button type="submit" name="simpan">✓ Proses Pengembalian</button><a class="btn btn-light" href="tampil.pengembalian.php">Batal</a></div>
</form><script>
const select=document.querySelector('select[name="id_peminjaman"]');
const kembali=document.getElementById('tanggal_kembali');
const preview=document.getElementById('denda-preview');
function previewDenda(){
 const o=select.options[select.selectedIndex]; const jatuh=o?o.textContent.match(/jatuh tempo (\d{4}-\d{2}-\d{2})/) : null;
 if(!jatuh){preview.textContent='Denda otomatis: Rp 0'; return;}
 const a=new Date(jatuh[1]+'T00:00:00'); const b=new Date(kembali.value+'T00:00:00');
 const hari=Math.max(0,Math.floor((b-a)/86400000));
 preview.textContent='Denda otomatis: Rp '+(hari*1000).toLocaleString('id-ID')+' ('+hari+' hari terlambat)';
}
select.addEventListener('change',previewDenda); kembali.addEventListener('change',previewDenda); previewDenda();
</script></body></html>
