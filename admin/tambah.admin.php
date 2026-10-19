<?php
require_once '../auth.php'; require_admin();
include '../connection.php';
if($_SERVER['REQUEST_METHOD']!=='POST') die("Form belum dikirim.");
$nama=trim($_POST['nama_admin']??'');$user=trim($_POST['username']??'');$pass=trim($_POST['password']??'');
if($nama===''||$user===''||$pass==='') die("Data admin belum lengkap.");
$s=$conn->prepare("INSERT INTO admin(nama_admin,username,password) VALUES(?,?,?)");$s->bind_param("sss",$nama,$user,$pass);
if(!$s->execute()) die("Gagal menambahkan admin: ".htmlspecialchars($s->error));
header("Location: tampil.admin.php");exit;
?>
