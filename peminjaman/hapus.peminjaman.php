<?php

require_once '../auth.php'; require_petugas();
include '../connection.php';


// Ambil ID peminjaman
$id = $_GET['id'] ?? '';

if ($id == '') {
    die("ID peminjaman tidak ditemukan.");
}


// Mulai transaksi
mysqli_begin_transaction($conn);

try {

    // Hapus histori terlebih dahulu
    $hapus_histori = mysqli_query($conn, "
        DELETE FROM histori
        WHERE id_peminjaman = '$id'
    ");

    if (!$hapus_histori) {
        throw new Exception(
            "Gagal menghapus histori: " . mysqli_error($conn)
        );
    }


    // Hapus denda jika ada
    $hapus_denda = mysqli_query($conn, "
        DELETE FROM denda
        WHERE id_peminjaman = '$id'
    ");

    if (!$hapus_denda) {
        throw new Exception(
            "Gagal menghapus denda: " . mysqli_error($conn)
        );
    }


    // Hapus pengembalian jika ada
    $hapus_pengembalian = mysqli_query($conn, "
        DELETE FROM pengembalian
        WHERE id_peminjaman = '$id'
    ");

    if (!$hapus_pengembalian) {
        throw new Exception(
            "Gagal menghapus pengembalian: " . mysqli_error($conn)
        );
    }


    // Hapus peminjaman
    $hapus_peminjaman = mysqli_query($conn, "
        DELETE FROM peminjaman
        WHERE id_peminjaman = '$id'
    ");

    if (!$hapus_peminjaman) {
        throw new Exception(
            "Gagal menghapus peminjaman: " . mysqli_error($conn)
        );
    }


    // Simpan perubahan
    mysqli_commit($conn);


    header("Location: tampil.peminjaman.php");
    exit;


} catch (Exception $e) {

    // Batalkan semua jika terjadi error
    mysqli_rollback($conn);

    die($e->getMessage());
}

?>