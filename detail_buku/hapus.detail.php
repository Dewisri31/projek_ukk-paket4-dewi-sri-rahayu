<?php
require_once '../auth.php'; require_detail_admin(); require_once '../connection.php';
$id=(int)($_GET['id_detail']??0);
if($id<=0){header('Location: tampil.detail.php');exit;}
$st=$conn->prepare('DELETE FROM detail_buku WHERE id_detail=?');$st->bind_param('i',$id);
if(!$st->execute()) die('Gagal menghapus data: '.htmlspecialchars($st->error));
header('Location: tampil.detail.php?pesan=dihapus');exit;
?>