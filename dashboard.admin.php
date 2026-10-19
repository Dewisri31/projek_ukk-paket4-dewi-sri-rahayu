<?php

require_once 'auth.php';
require_admin();

include 'connection.php';


// ============================================================
// STATISTIK
// ============================================================

$buku = $conn->query("
    SELECT COUNT(*) total
    FROM buku
")->fetch_assoc()['total'];


$anggota = $conn->query("
    SELECT COUNT(*) total
    FROM anggota
")->fetch_assoc()['total'];


$kategori = $conn->query("
    SELECT COUNT(*) total
    FROM kategori
")->fetch_assoc()['total'];


$penerbit = $conn->query("
    SELECT COUNT(*) total
    FROM penerbit
")->fetch_assoc()['total'];


// Jumlah denda

$denda = $conn->query("
    SELECT COUNT(*) total
    FROM denda
")->fetch_assoc()['total'];


// Total nominal denda

$total_denda = $conn->query("
    SELECT COALESCE(SUM(total_denda), 0) total
    FROM denda
")->fetch_assoc()['total'];

?>

<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Dashboard Admin</title>

    <link
        rel="stylesheet"
        href="style.css"
    >


    

</head>


<body>


<!-- ==========================================================
     TOPBAR
========================================================== -->

<div class="topbar">

    <strong>

        📚 Perpustakaan

        <span class="role-badge">
            ADMIN
        </span>

    </strong>


    <a
        class="btn btn-light"
        href="logout.php"
    >

        Keluar

    </a>

</div>



<!-- ==========================================================
     CONTAINER
========================================================== -->

<div class="container">


    <!-- ======================================================
         HEADER
    ======================================================= -->

    <div class="page-head">

        <div>

            <h1>
                Dashboard Admin
            </h1>


            <p class="muted">

                Admin fokus pada pengelolaan
                data master, akun, dan denda perpustakaan.

            </p>

        </div>

    </div>



    <!-- ======================================================
         STATISTIK
    ======================================================= -->

    <div class="stats">


        <div class="stat">

            <small>
                Total Buku
            </small>

            <strong>
                <?= $buku ?>
            </strong>

        </div>



        <div class="stat">

            <small>
                Total Anggota
            </small>

            <strong>
                <?= $anggota ?>
            </strong>

        </div>



        <div class="stat">

            <small>
                Kategori
            </small>

            <strong>
                <?= $kategori ?>
            </strong>

        </div>



        <div class="stat">

            <small>
                Penerbit
            </small>

            <strong>
                <?= $penerbit ?>
            </strong>

        </div>



        <div class="stat stat-denda">

            <small>
                Data Denda
            </small>

            <strong>
                <?= $denda ?>
            </strong>

        </div>



        <div class="stat stat-denda">

            <small>
                Total Denda
            </small>

            <strong class="money">

                Rp <?= number_format(
                    $total_denda,
                    0,
                    ',',
                    '.'
                ) ?>

            </strong>

        </div>


    </div>



    <!-- ======================================================
         MENU ADMIN
    ======================================================= -->

    <div class="card-grid">


        <!-- ==================================================
             BUKU
        =================================================== -->

        <div class="card">

            <div class="menu-icon">
                📚
            </div>


            <h3>
                Kelola Buku
            </h3>


            <p>

                Tambah, ubah, hapus,
                dan lihat detail buku.

            </p>


            <a
                class="btn"
                href="buku/tampil.buku.php"
            >

                Buka Data Buku

            </a>

        </div>



        <!-- ==================================================
             ANGGOTA
        =================================================== -->

        <div class="card">

            <div class="menu-icon">
                👥
            </div>


            <h3>
                Kelola Anggota
            </h3>


            <p>

                Kelola data anggota
                perpustakaan.

            </p>


            <a
                class="btn"
                href="anggota/tampil.anggota.php"
            >

                Buka Data Anggota

            </a>

        </div>



        <!-- ==================================================
             KATEGORI
        =================================================== -->

        <div class="card">

            <div class="menu-icon">
                🏷️
            </div>


            <h3>
                Kelola Kategori
            </h3>


            <p>

                Kelola kategori
                buku perpustakaan.

            </p>


            <a
                class="btn"
                href="kategori/tampil.kategori.php"
            >

                Buka Kategori

            </a>

        </div>



        <!-- ==================================================
             PENERBIT
        =================================================== -->

        <div class="card">

            <div class="menu-icon">
                🏢
            </div>


            <h3>
                Kelola Penerbit
            </h3>


            <p>

                Kelola data
                penerbit buku.

            </p>


            <a
                class="btn"
                href="penerbit/tampil.penerbit.php"
            >

                Buka Penerbit

            </a>

        </div>



        <!-- ==================================================
             AKUN
        =================================================== -->

        <div class="card">

            <div class="menu-icon">
                👨‍💼
            </div>


            <h3>
                Kelola Akun
            </h3>


            <p>

                Kelola akun
                Admin dan Petugas.

            </p>


            <a
                class="btn"
                href="admin/tampil.admin.php"
            >

                Admin

            </a>


            <a
                class="btn"
                href="petugas/tampil.petugas.php"
            >

                Petugas

            </a>

        </div>



        <!-- ==================================================
             DENDA
        =================================================== -->

        <div class="card denda-card">

            <div class="menu-icon">
                💰
            </div>


            <h3>
                Kelola Denda
            </h3>


            <p>

                Lihat riwayat denda yang dibuat
                otomatis saat pengembalian.

            </p>


            <a
                class="btn btn-danger"
                href="./denda/tampil.denda.php"
            >

                Kelola Denda

            </a>

        </div>


    </div>



    <!-- ======================================================
         INFORMASI
    ======================================================= -->

    <div class="info">

        ℹ️

        <b>Catatan:</b>

        <b>Petugas</b> menangani detail buku, persetujuan (ACC/Tolak) peminjaman,
        dan proses pengembalian. <b>Admin</b> fokus pada data master dan pengelolaan akun.
        Denda dibuat <b>otomatis saat pengembalian</b> berdasarkan keterlambatan.

    </div>


</div>


</body>

</html>
