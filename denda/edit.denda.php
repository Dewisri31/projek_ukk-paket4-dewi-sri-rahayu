<?php
require_once '../auth.php'; require_login();
header('Location: tampil.denda.php?pesan='.urlencode('Denda dikelola otomatis saat pengembalian.')); exit;
?>
