<?php
include("../connection.php");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Detail Buku</title>
</head>

<body>

<h1>Tambah Detail Buku</h1>

<form action="tambah.detail.php" method="POST">

    <label>ID Detail</label><br>
    <input type="number" name="id_detailb" required>

    <br><br>

    <label>Kategori</label><br>

    <select name="id_kategori" required>

        <option value="">-- Pilih Kategori --</option>

        <?php
        $query_kategori = $conn->query("SELECT * FROM kategori");

        while ($kategori = $query_kategori->fetch_assoc()) {
        ?>

            <option value="<?= $kategori['id_kategori']; ?>">
                <?= $kategori['nama_kategori']; ?>
            </option>

        <?php } ?>

    </select>

    <br><br>

    <label>ID Penerbit</label><br>
    <input type="number" name="id_penerbit" required>

    <br><br>

    <label>Pengarang</label><br>
    <input type="text" name="pengarang" required>

    <br><br>

    <label>Tahun Terbit</label><br>
    <input type="number" name="tahun_terbit" required>

    <br><br>

    <label>Jumlah Halaman</label><br>
    <input type="number" name="jumlah_halaman" required>

    <br><br>

    <label>Jumlah Stok</label><br>
    <input type="number" name="jumlah_stok" required>

    <br><br>

    <label>ISBN</label><br>
    <input type="text" name="isbn" required>

    <br><br>

    <button type="submit">Simpan</button>

</form>

</body>
</html>