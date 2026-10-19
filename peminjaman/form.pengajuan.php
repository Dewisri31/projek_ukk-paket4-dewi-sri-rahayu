<?php
require_once '../auth.php'; require_anggota();
include '../connection.php';
$buku=$conn->query("SELECT id_buku,judul FROM buku ORDER BY judul");
$today=date('Y-m-d');
$selected_buku=(int)($_GET['id_buku']??0);
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Ajukan Peminjaman</title><link rel="stylesheet" href="../style.css"></head><body>
<div class="page-head"><div><h1>Ajukan Peminjaman</h1><p class="muted">Tanggal pinjam hanya boleh hari ini atau setelahnya. Jatuh tempo ditentukan Admin setelah menyetujui.</p></div><a class="btn btn-light" href="../dashboard.anggota.php">← Kembali ke Dashboard</a></div>
<form action="ajukan.peminjaman.php" method="post">
<label>Buku</label><select name="id_buku" required><option value="">-- Pilih Buku --</option><?php while($r=$buku->fetch_assoc()): ?><option value="<?=$r['id_buku']?>" <?=$selected_buku===(int)$r['id_buku']?'selected':''?>><?=htmlspecialchars($r['judul'])?></option><?php endwhile; ?></select>
<label>Tanggal Pinjam</label><input type="date" name="tanggal_pinjam" min="<?=$today?>" value="<?=$today?>" required>
<div class="info">⏳ Setelah dikirim, permintaan berstatus <b>Menunggu ACC Admin</b>. Kamu belum bisa meminjam sebelum Admin menyetujui.</div>
<div class="form-actions"><button type="submit">Kirim Pengajuan</button><a class="btn btn-light" href="../dashboard.anggota.php">Batal</a></div>
</form></body></html>