<?php
require_once '../auth.php';
require_detail_petugas();
require_once '../connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: form.detail.php'); exit; }

$id_buku=(int)($_POST['id_buku']??0);
$id_kategori=(int)($_POST['id_kategori']??0);
$id_penerbit=(int)($_POST['id_penerbit']??0);
$pengarang=trim($_POST['pengarang']??'');
$tahun=(int)($_POST['tahun_terbit']??0);
$halaman=(int)($_POST['jumlah_halaman']??0);
$stok=(int)($_POST['jumlah_stok']??0);
$isbn=trim($_POST['isbn']??'');
$id_admin=role()==='admin'?current_user_id():null; $id_petugas=role()==='petugas'?current_user_id():null;

if(!$id_buku||!$id_kategori||!$id_penerbit||$pengarang==='') die('Data detail buku belum lengkap. <a href="form.detail.php">Kembali</a>');
if($tahun<0||$halaman<0||$stok<0) die('Tahun, halaman, dan stok tidak boleh negatif. <a href="form.detail.php">Kembali</a>');

$cek=$conn->prepare("SELECT id_detail FROM detail_buku WHERE id_buku=? LIMIT 1");
$cek->bind_param('i',$id_buku); $cek->execute();
if($cek->get_result()->fetch_assoc()) die('Detail untuk buku tersebut sudah ada. Silakan gunakan Edit. <a href="tampil.detail.php">Kembali</a>');

$st=$conn->prepare("INSERT INTO detail_buku(id_admin,id_petugas,id_buku,id_kategori,id_penerbit,pengarang,tahun_terbit,jumlah_halaman,jumlah_stok,isbn) VALUES(?,?,?,?,?,?,?,?,?,?)");
$st->bind_param('iiiiisiiis',$id_admin,$id_petugas,$id_buku,$id_kategori,$id_penerbit,$pengarang,$tahun,$halaman,$stok,$isbn);
if(!$st->execute()) die('Gagal menambahkan detail buku: '.htmlspecialchars($st->error));
header('Location: tampil.detail.php?pesan=ditambahkan'); exit;
?>