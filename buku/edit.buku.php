<?php

include("../connection.php");

$id = $_GET['id_buku'];

if (isset($_POST['update'])) {

    $judul       = $_POST['judul'];
    $id_kategori = $_POST['id_kategori'];
    $sinopsis    = $_POST['sinopsis'];

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {

        $tmp_name = $_FILES['gambar']['tmp_name'];
        $gambar_biner = file_get_contents($tmp_name);
        $gambar = mysqli_real_escape_string($conn, $gambar_biner);

        $sql = "UPDATE buku SET
                judul = '$judul',
                id_kategori = '$id_kategori',
                gambar = '$gambar',
                sinopsis = '$sinopsis'
                WHERE id_buku = '$id'";

    } else {

        $sql = "UPDATE buku SET
                judul = '$judul',
                id_kategori = '$id_kategori',
                sinopsis = '$sinopsis'
                WHERE id_buku = '$id'";
    }

    if ($conn->query($sql) === TRUE) {

        header("Location: tampil.buku.php");
        exit;

    } else {

        echo "Gagal mengubah data: " . $conn->error;

    }
}


/* Ambil data buku */
$query = $conn->query(
    "SELECT * FROM buku WHERE id_buku = '$id'"
);

$buku = $query->fetch_assoc();


/* Ambil data kategori */
$query_kategori = $conn->query(
    "SELECT * FROM kategori"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Data Buku</title>

    <link rel="stylesheet" href="data.css">

</head>

<body>

    <h1>Edit Data Buku</h1>

    <form method="POST" enctype="multipart/form-data">

        <label>Judul Buku</label>
        <br>

        <input
            type="text"
            name="judul"
            value="<?= $buku['judul']; ?>"
            required
        >

        <br><br>


        <label>Kategori</label>
        <br>

        <select name="id_kategori" required>

            <option value="">-- Pilih Kategori --</option>

            <?php while ($kategori = $query_kategori->fetch_assoc()) { ?>

                <option
                    value="<?= $kategori['id_kategori']; ?>"
                    <?= ($kategori['id_kategori'] == $buku['id_kategori']) ? 'selected' : ''; ?>
                >

                    <?= $kategori['nama_kategori']; ?>

                </option>

            <?php } ?>

        </select>

        <br><br>


        <label>Gambar Buku Saat Ini</label>
        <br>

        <?php if (!empty($buku['gambar'])) { ?>

            <img
                src="data:image/jpeg;base64,<?= base64_encode($buku['gambar']); ?>"
                width="100"
            >

        <?php } ?>

        <br><br>


        <label>Ganti Gambar (Opsional)</label>
        <br>

        <input
            type="file"
            name="gambar"
            accept="image/*"
        >

        <br><br>


        <label>Sinopsis</label>
        <br>

        <textarea
            name="sinopsis"
            rows="5"
            required
        ><?= $buku['sinopsis']; ?></textarea>

        <br><br>


        <button type="submit" name="update">
            Simpan Perubahan
        </button>

        <a href="tampil.buku.php">
            Batal
        </a>

    </form>

</body>

</html>