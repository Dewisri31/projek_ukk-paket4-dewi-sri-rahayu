<?php

include("../connection.php");

$query = $conn ->query("SELECT*FROM detail_buku");
$result = $query->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Detail buku</title>
    </head>

    <body>
        <H1>DETAIL BUKU</H1>

     <a href="form.detail.php" class="tambah">
        + Tambah Data
    </a>

      <table border="1">
            <thead>
                <tr>
                    <th>ID DETAIL</th>
                    <th>ID KATEGORI</th>
                    <th>ID PENERBIT</th>
                    <th>PENGARANG</th>
                    <th>TAHUN TERBIT</th>
                    <th>JUMLAH HALAMAN</th>
                    <th>JUMLAH STOK</th>
                    <th>ISBN</th>
                    <th>AKSI</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach($result as $d): ?>

                <tr>
                    <td><?=$d['id_detailb']; ?></td>
                    <td><?=$d['id_kategori']; ?></td>
                    <td><?=$d['id_penerbit']; ?></td>
                    <td><?=$d['pengarang']; ?></td>  
                    <td><?=$d['tahun_terbit']; ?></td>
                    <td><?=$d['jumlah_halaman']; ?></td>
                    <td><?=$d['jumlah_stok']; ?></td>
                    <td><?=$d['isbn']; ?></td>
                     <td>

                    <a href="edit.detail.php?id_detailb=<?= $d['id_detailb']; ?>">
                        <button>Edit</button>
                    </a>

                    <a href="hapus.detail.php?id_detailb=<?= $d['id_detailb']; ?>"
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