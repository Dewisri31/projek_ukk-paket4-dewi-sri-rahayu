<?php

require_once '../auth.php'; require_petugas();
include '../connection.php';

if (!isset($_GET['id'])) {
    header("Location: tampil.pengembalian.php");
    exit;
}

$id = $_GET['id'];

// Mengambil data pengembalian
$query = mysqli_query($conn, "
    SELECT pg.*, p.id_petugas AS petugas_peminjaman
    FROM pengembalian pg
    JOIN peminjaman p ON pg.id_peminjaman=p.id_peminjaman
    WHERE pg.id_pengembalian = '$id'
");

$row = mysqli_fetch_assoc($query);

if (!$row) {
    die("Data pengembalian tidak ditemukan.");
}

if ((int)($row['id_petugas'] ?? 0) !== (int)$_SESSION['id_petugas']) {
    die("Pengembalian ini diproses oleh petugas lain. Gunakan akun petugas yang sesuai.");
}


// Jika tombol update ditekan
if (isset($_POST['update'])) {

    $tanggal_kembali = $_POST['tanggal_kembali'];
    $keterangan = $_POST['keterangan'];

    // Mengambil tanggal jatuh tempo
    $query_peminjaman = mysqli_query($conn, "
        SELECT tanggal_jatuh_tempo
        FROM peminjaman
        WHERE id_peminjaman = '{$row['id_peminjaman']}'
    ");

    $peminjaman = mysqli_fetch_assoc($query_peminjaman);

    // Menghitung keterlambatan
    $jatuh_tempo = new DateTime(
        $peminjaman['tanggal_jatuh_tempo']
    );

    $kembali = new DateTime($tanggal_kembali);

    if ($kembali > $jatuh_tempo) {

        $terlambat = $jatuh_tempo->diff($kembali)->days;

    } else {

        $terlambat = 0;
    }

    // Tarif denda
    $tarif_per_hari = 1000;

    // Total denda
    $denda = $terlambat * $tarif_per_hari;


    // Update pengembalian
    $id_petugas_login = (int)$_SESSION['id_petugas'];
    $update = mysqli_query($conn, "
        UPDATE pengembalian SET
            id_petugas = '$id_petugas_login',
            tanggal_kembali = '$tanggal_kembali',
            terlambat = '$terlambat',
            denda = '$denda',
            keterangan = '$keterangan'
        WHERE id_pengembalian = '$id'
    ");

    if (!$update) {
        die("Gagal update: " . mysqli_error($conn));
    }


    // Update tabel denda
    $update_denda = mysqli_query($conn, "
        UPDATE denda SET
            terlambat = '$terlambat',
            tarif_per_hari = '$tarif_per_hari',
            total_denda = '$denda'
        WHERE id_pengembalian = '$id'
    ");

    if (!$update_denda) {
        die("Gagal update denda: " . mysqli_error($conn));
    }


    header("Location: tampil.pengembalian.php");
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../style.css">
    <title>Edit Pengembalian</title>
</head>

<body>
<div class="page-head"><div><h1>📝 Edit Pengembalian</h1><p class="muted">Lengkapi data dengan benar.</p></div><a class="btn btn-light" href="<?='../dashboard.'.($_SESSION['role']??'').'.php'?>">← Kembali ke Dashboard</a></div>

<h2>Edit Pengembalian</h2>

<form method="POST">

    <label>ID Peminjaman</label><br>

    <input
        type="text"
        value="<?php echo $row['id_peminjaman']; ?>"
        readonly
    >

    <br><br>

    <label>Tanggal Kembali</label><br>

    <input
        type="date"
        name="tanggal_kembali"
        value="<?php echo $row['tanggal_kembali']; ?>"
        required
    >

    <br><br>

    <label>Keterangan</label><br>

    <input
        type="text"
        name="keterangan"
        value="<?php echo $row['keterangan']; ?>"
        maxlength="100"
    >

    <br><br>

    <button type="submit" name="update">
        Update
    </button>

    <a href="tampil.pengembalian.php">
        Kembali
    </a>

</form>

</body>
</html>