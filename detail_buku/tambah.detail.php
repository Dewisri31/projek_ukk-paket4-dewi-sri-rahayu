<?php

include("../connection.php");

$id_detailb     = $_POST["id_detailb"];
$id_kategori    = $_POST["id_kategori"];
$id_penerbit    = $_POST["id_penerbit"];
$pengarang      = $_POST["pengarang"];
$tahun_terbit   = $_POST["tahun_terbit"];
$jumlah_halaman = $_POST["jumlah_halaman"];
$jumlah_stok    = $_POST["jumlah_stok"];
$isbn           = $_POST["isbn"];


$sql = "INSERT INTO detail_buku
        (
            id_detailb,
            id_kategori,
            id_penerbit,
            pengarang,
            tahun_terbit,
            jumlah_halaman,
            jumlah_stok,
            isbn
        )
        VALUES
        (
            '$id_detailb',
            '$id_kategori',
            '$id_penerbit',
            '$pengarang',
            '$tahun_terbit',
            '$jumlah_halaman',
            '$jumlah_stok',
            '$isbn'
        )";


if (mysqli_query($conn, $sql)) {

    echo "<script>
            alert('Data detail buku berhasil ditambahkan!');
            window.location='tampil.detail.php';
          </script>";

} else {

    echo "Gagal menambahkan data: " . mysqli_error($conn);

}

?>