<?php
require_once '../auth.php'; require_detail_petugas();
?>
<?php

// Menghubungkan ke database
include("../connection.php");

// Mengambil data admin
$query_admin = $conn->query("
    SELECT * FROM admin
");

// Mengambil data buku
$query_buku = $conn->query("
    SELECT * FROM buku
");

// Mengambil data kategori
$query_kategori = $conn->query("
    SELECT * FROM kategori
");

// Mengambil data penerbit
$query_penerbit = $conn->query("
    SELECT * FROM penerbit
");

?>

<!DOCTYPE html>
<html lang="id">

<head>
<link rel="stylesheet" href="../style.css">
    <meta charset="UTF-8">
    <title>Tambah Detail Buku</title>
</head>

<body>
<div class="page-head"><div><h1>📝 Form Detail</h1><p class="muted">Lengkapi data dengan benar.</p></div><a class="btn btn-light" href="<?='../dashboard.'.($_SESSION['role']??'').'.php'?>">← Kembali ke Dashboard</a></div>

    <h1>Tambah Detail Buku</h1>

    <form action="tambah.detail.php" method="POST">

        <label>ID Detail</label>
        <br>

        <input type="number" name="id_detail" placeholder="Otomatis">

        <br><br>


        <label>Akun Pengelola</label><br><input value="<?= htmlspecialchars(current_user_label()) ?>" readonly><br><br>


        <label>Buku</label>
        <br>

        <select name="id_buku" required>

            <option value="">
                -- Pilih Buku --
            </option>

            <?php while ($buku = $query_buku->fetch_assoc()) { ?>

                <option value="<?= $buku['id_buku']; ?>">

                    <?= $buku['judul']; ?>

                </option>

            <?php } ?>

        </select>

        <br><br>


        <label>Kategori</label>
        <br>

        <select name="id_kategori" required>

            <option value="">
                -- Pilih Kategori --
            </option>

            <?php while ($kategori = $query_kategori->fetch_assoc()) { ?>

                <option value="<?= $kategori['id_kategori']; ?>">

                    <?= $kategori['nama_kategori']; ?>

                </option>

            <?php } ?>

        </select>

        <br><br>


        <label>Penerbit</label>
        <br>

        <select name="id_penerbit" required>

            <option value="">
                -- Pilih Penerbit --
            </option>

            <?php while ($penerbit = $query_penerbit->fetch_assoc()) { ?>

                <option value="<?= $penerbit['id_penerbit']; ?>">

                    <?= $penerbit['nama_penerbit']; ?>

                </option>

            <?php } ?>

        </select>

        <br><br>


        <label>Pengarang</label>
        <br>

        <input
            type="text"
            name="pengarang"
            required
        >

        <br><br>


        <label>Tahun Terbit</label>
        <br>

        <input
            type="number"
            name="tahun_terbit"
            required
        >

        <br><br>


        <label>Jumlah Halaman</label>
        <br>

        <input
            type="number"
            name="jumlah_halaman"
            required
        >

        <br><br>


        <label>Jumlah Stok</label>
        <br>

        <input
            type="number"
            name="jumlah_stok"
            required
        >

        <br><br>


        <label>ISBN</label>
        <br>

        <input
            type="text"
            name="isbn"
            required
        >

        <br><br>


        <button type="submit">
            Simpan
        </button>

        <button type="reset">
            Reset
        </button>

        <a href="tampil.detail.php">
            <button type="button">
                Kembali
            </button>
        </a>

    </form>

</body>

</html>