<?php

include("../connection.php");

$id          = $_POST["id_buku"];
$judul       = $_POST["judul"];
$id_kategori = $_POST["id_kategori"];
$sinopsis    = $_POST["sinopsis"];


if (isset($_FILES["gambar"]) && $_FILES["gambar"]["error"] == 0) {

    $tmp_name = $_FILES["gambar"]["tmp_name"];

    // Membaca gambar
    $gambar_biner = file_get_contents($tmp_name);

    // Mengamankan gambar
    $gambar = mysqli_real_escape_string($conn, $gambar_biner);


    $sql = "INSERT INTO buku
            (
                id_buku,
                judul,
                id_kategori,
                gambar,
                sinopsis
            )
            VALUES
            (
                '$id',
                '$judul',
                '$id_kategori',
                '$gambar',
                '$sinopsis'
            )";


    if (mysqli_query($conn, $sql)) {

        echo "<script>
                alert('Data buku berhasil ditambahkan!');
                window.location='tampil.buku.php';
              </script>";

    } else {

        echo "Gagal menambahkan data: " . mysqli_error($conn);
    }

} else {

    echo "<script>
            alert('Harap pilih file gambar!');
            window.history.back();
          </script>";
}

?>