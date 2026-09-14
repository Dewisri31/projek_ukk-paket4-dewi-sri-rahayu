<?php

include("../connection.php");

$query = $conn ->query("SELECT*FROM anggota");
$result = $query->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Data Anggota</title>
    </head>

    <body>
        <H1>ANGGOTA</H1>

            <a href="form.anggota.html" class="tambah">
        + Tambah Data
    </a>

      <table border="1">
            <thead>
                <tr>
                    <th>ID ANGGOTA</th>
                    <th>NAMA ANGGOTA</th>
                    <th>JENIS KELAMIN</th>
                    <th>TEMPAT LAHIR</th>
                    <th>TANGGAL LAHIR</th>
                    <th>TELPON</th>
                    <th>ALAMAT</th>
                    <th>AKSI</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach($result as $a): ?>

                <tr>
                    <td><?=$a['id_anggota']; ?></td>
                    <td><?=$a['nama_anggota']; ?></td>
                    <td><?=$a['jenis_kelamin']; ?></td>
                    <td><?=$a['tempat_lahir']; ?></td>
                    <td><?=$a['tanggal_lahir']; ?></td>
                    <td><?=$a['telpon']; ?></td>
                    <td><?=$a['alamat']; ?></td>
                     <td>

                    <a href="edit.anggota.php?id_anggota=<?= $a['id_anggota']; ?>">
                        <button>Edit</button>
                    </a>

                    <a href="hapus.anggota.php?id_anggota=<?= $a['id_anggota']; ?>"
                       onclick="return confirm('Yakin ingin menghapus data ini?')">

                        <button>Hapus</button>

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