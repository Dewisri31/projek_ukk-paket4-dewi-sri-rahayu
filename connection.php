<?php
date_default_timezone_set('Asia/Jakarta');
mysqli_report(MYSQLI_REPORT_OFF);

$servername = "sql206.infinityfree.com";
$username   = "if0_43222741";
$password   = "kncKh9TVjXy";
$dbname     = "if0_43222741_perpustakaan_ukk_dewi";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_errno) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>