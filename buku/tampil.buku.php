<?php

include("../connection.php");

// Ambil data buku sekaligus nama kategori
$query = $conn->query("
    SELECT 
        buku.id_buku,
        buku.judul,
        buku.id_kategori,
        kategori.nama_kategori,
        buku.gambar,
        buku.sinopsis
    FROM buku
    LEFT JOIN kategori 
        ON buku.id_kategori = kategori.id_kategori
");

$result = $query->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Buku</title>
</head>

<body>

    <h1>BUKU</h1>

    <a href="form.buku.php" class="tambah">
        + Tambah Data
    </a>

    <br><br>

    <table border="1">

        <thead>
            <tr>
                <th>ID BUKU</th>
                <th>JUDUL</th>
                <th>KATEGORI</th>
                <th>GAMBAR</th>
                <th>SINOPSIS</th>
                <th>AKSI</th>
            </tr>
        </thead>

        <tbody>

            <?php foreach ($result as $b): ?>

                <tr>

                    <td>
                        <?= $b['id_buku']; ?>
                    </td>

                    <td>
                        <?= $b['judul']; ?>
                    </td>

                    <td>
                        <?= $b['nama_kategori']; ?>
                    </td>

                    <td>
                        <?php if (!empty($b['gambar'])): ?>

                            <img 
                                src="data:image/jpeg;base64,<?= base64_encode($b['gambar']); ?>" 
                                width="100"
                            >

                        <?php else: ?>

                            Tidak ada gambar

                        <?php endif; ?>
                    </td>

                    <td>
                        <?= $b['sinopsis']; ?>
                    </td>

                    <td>

                        <a href="edit.buku.php?id_buku=<?= $b['id_buku']; ?>">
                            <button type="button">Edit</button>
                        </a>

                        <a 
                            href="hapus.buku.php?id_buku=<?= $b['id_buku']; ?>"
                            onclick="return confirm('Yakin ingin menghapus data ini?')"
                        >
                            <button type="button">Hapus</button>
                        </a>

                        <a href="/12rpl1/detail_buku/tampil.detail.php">
                            <button type="button">Detail</button>
                        </a>

                    </td>

                </tr>

            <?php endforeach; ?>

        </tbody>

    </table>

    <div class="footer">
        DEWI SRI RAHAYU
    </div>

</body>

</html>