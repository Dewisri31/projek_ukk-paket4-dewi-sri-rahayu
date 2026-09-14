<?php

include("../connection.php");

$query = $conn ->query("SELECT*FROM petugas");
$result = $query->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Data petugas</title>
    </head>

    <body>
        <H1>PETUGAS</H1>

      <a href="form.petugas.html" class="tambah">
        + Tambah Data
    </a>  


      <table border="1">
            <thead>
                <tr>
                    <th>ID PETUGAS</th>
                    <th>NAMA PETUGAS</th>
                    <th>USERNAME</th>
                    <th>PASSWORD</th>
                    <th>AKSI</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach($result as $ap): ?>

                <tr>
                    <td><?=$ap['id_petugas']; ?></td>
                    <td><?=$ap['nama_petugas']; ?></td>
                    <td><?=$ap['username']; ?></td>
                    <td><?=$ap['password']; ?></td>
                    <td>
                    <a href="edit.petugas.php?id_petugas=<?= $ap['id_petugas']; ?>">
                        <button>Edit</button>
                    </a>

                    <a href="hapus.petugas.php?id_petugas=<?= $ap['id_petugas']; ?>"
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