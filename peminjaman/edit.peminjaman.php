<?php
require_once '../auth.php'; require_petugas(); include '../connection.php';
$id=(int)($_GET['id']??0);
if(!$id) die('ID peminjaman tidak ditemukan. <a href="tampil.peminjaman.php">Kembali</a>');

$st=$conn->prepare("SELECT * FROM peminjaman WHERE id_peminjaman=?"); $st->bind_param('i',$id); $st->execute(); $data=$st->get_result()->fetch_assoc();
if(!$data) die('Data peminjaman tidak ditemukan. <a href="tampil.peminjaman.php">Kembali</a>');

$anggota=$conn->query("SELECT id_anggota,nama_anggota FROM anggota ORDER BY nama_anggota");
$buku=$conn->query("SELECT id_buku,judul FROM buku ORDER BY judul");
$today=date('Y-m-d');

if(isset($_POST['update'])){
    $ia=(int)($_POST['id_anggota']??0); $ib=(int)($_POST['id_buku']??0); $pin=trim($_POST['tanggal_pinjam']??''); $tempo=trim($_POST['tanggal_jatuh_tempo']??'');
    if(!$ia||!$ib||!$pin||!$tempo) die('Data belum lengkap. <a href="edit.peminjaman.php?id='.$id.'">Kembali</a>');
    if($pin<$today || $tempo<$pin) die('Tanggal tidak valid. <a href="edit.peminjaman.php?id='.$id.'">Kembali</a>');
    $up=$conn->prepare("UPDATE peminjaman SET id_anggota=?,id_petugas=?,id_admin=NULL,id_buku=?,tanggal_pinjam=?,tanggal_jatuh_tempo=? WHERE id_peminjaman=?");
    $up->bind_param('iiisssi',$ia,$_SESSION['id_petugas'],$ib,$pin,$tempo,$id);
    if(!$up->execute()) die('Gagal update: '.htmlspecialchars($up->error));
    $h=$conn->prepare("UPDATE histori SET id_admin=NULL,id_buku=? WHERE id_peminjaman=?"); $h->bind_param('ii',$ib,$id); $h->execute();
    header('Location: tampil.peminjaman.php?pesan=berhasil'); exit;
}
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Edit Peminjaman</title><link rel="stylesheet" href="../style.css"></head><body>
<div class="page-head"><div><h1>✏ Edit Peminjaman</h1><p class="muted">Perubahan transaksi dilakukan oleh Petugas.</p></div><a class="btn btn-light" href="tampil.peminjaman.php">← Kembali</a></div>
<form method="POST">
<label>Anggota</label><select name="id_anggota" required><?php while($r=$anggota->fetch_assoc()): ?><option value="<?=$r['id_anggota']?>" <?=$r['id_anggota']==$data['id_anggota']?'selected':''?>><?=htmlspecialchars($r['nama_anggota'])?></option><?php endwhile; ?></select>
<label>Buku</label><select name="id_buku" required><?php while($r=$buku->fetch_assoc()): ?><option value="<?=$r['id_buku']?>" <?=$r['id_buku']==$data['id_buku']?'selected':''?>><?=htmlspecialchars($r['judul'])?></option><?php endwhile; ?></select>
<label>Tanggal Pinjam</label><input type="date" name="tanggal_pinjam" min="<?=$today?>" value="<?=htmlspecialchars($data['tanggal_pinjam'])?>" required>
<label>Tanggal Jatuh Tempo</label><input type="date" name="tanggal_jatuh_tempo" min="<?=$today?>" value="<?=htmlspecialchars($data['tanggal_jatuh_tempo'])?>" required>
<div class="info">Petugas aktif: <b><?=htmlspecialchars($_SESSION['nama'])?></b></div>
<div class="form-actions"><button type="submit" name="update">✓ Simpan Perubahan</button><a class="btn btn-light" href="tampil.peminjaman.php">Batal</a></div>
</form></body></html>
