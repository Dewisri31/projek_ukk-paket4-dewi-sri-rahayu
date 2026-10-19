<?php
require_once '../auth.php'; require_admin();
include '../connection.php';
if($_SERVER['REQUEST_METHOD']!=='POST') die("Form belum dikirim.");
$id_admin=current_user_id();$nama=trim($_POST['nama_petugas']??'');$user=trim($_POST['username']??'');$pass=trim($_POST['password']??'');
if(!$id_admin||$nama===''||$user===''||$pass==='') die("Data petugas belum lengkap.");
$s=$conn->prepare("INSERT INTO petugas(id_admin,nama_petugas,username,password) VALUES(?,?,?,?)");$s->bind_param("isss",$id_admin,$nama,$user,$pass);
if(!$s->execute()) die("Gagal menambahkan petugas: ".htmlspecialchars($s->error));
header("Location: tampil.petugas.php");exit;
?>
