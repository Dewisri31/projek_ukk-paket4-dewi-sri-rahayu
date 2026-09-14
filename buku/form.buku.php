<?php
include("../connection.php");

$query_kategori = $conn->query("SELECT * FROM kategori");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Buku</title>
    <link rel="stylesheet" href="data.css">
</head>

<body>

    <h1>Tambah Data Buku</h1>

    <form action="tambah.buku.php" method="POST" enctype="multipart/form-data">

        <label>ID Buku</label>
        <br>
        <input type="number" name="id_buku" required>

        <br><br>

        <label>Judul Buku</label>
        <br>
        <input type="text" name="judul" required>

        <br><br>

        <label>Kategori</label>
        <br>

        <select name="id_kategori" required>

            <option value="">-- Pilih Kategori --</option>

            <?php while ($kategori = $query_kategori->fetch_assoc()) { ?>

                <option value="<?= $kategori['id_kategori']; ?>">
                    <?= $kategori['nama_kategori']; ?>
                </option>

            <?php } ?>

        </select>

        <br><br>

        <label>Gambar Buku</label>
        <br>
        <input type="file" name="gambar" accept="image/*" required>

        <br><br>

        <label>Sinopsis</label>
        <br>
        <textarea name="sinopsis" rows="5" required></textarea>

        <br><br>

        <button type="submit">
            Simpan
        </button>

        <a href="tampil.buku.php">
            Batal
        </a>

    </form>

</body>

</html>