<?php
require_once '../auth.php'; require_staff();
include '../connection.php';
$id=(int)($_GET['id']??$_GET['id_kategori']??0);
if(!$id) die("ID kategori tidak ditemukan.");
$s=$conn->prepare("SELECT COUNT(*) n FROM buku WHERE id_kategori=?");$s->bind_param("i",$id);$s->execute();
$b=(int)$s->get_result()->fetch_assoc()['n'];
$s=$conn->prepare("SELECT COUNT(*) n FROM detail_buku WHERE id_kategori=?");$s->bind_param("i",$id);$s->execute();
$d=(int)$s->get_result()->fetch_assoc()['n'];
if($b||$d) die("<div class='error action-message'><h2>Kategori tidak dapat dihapus</h2><p>Kategori masih dipakai oleh ".$b." data buku dan ".$d." data detail buku.</p><a class='btn btn-light' href='tampil.kategori.php'>← Kembali</a></div>");
$s=$conn->prepare("DELETE FROM kategori WHERE id_kategori=?");$s->bind_param("i",$id);
if(!$s->execute()) die("Gagal menghapus kategori: ".htmlspecialchars($s->error));
header("Location: tampil.kategori.php");exit;
?>
