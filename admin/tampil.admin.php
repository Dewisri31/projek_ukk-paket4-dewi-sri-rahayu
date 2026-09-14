<?php

include("../connection.php");

$query = $conn ->query("SELECT*FROM admin");
$result = $query->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Data admin</title>
    </head>

    <body>
        <H1>ADMIN</H1>

     <a href="form.admin.html" class="tambah">
        + Tambah Data
    </a>

      <table border="1">
            <thead>
                <tr>
                    <th>ID ADMIN</th>
                    <th>NAMA ADMIN</th>
                    <th>USERNAME</th>
                    <th>PASSWORD</th>
                    <th>AKSI</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach($result as $ad): ?>

                <tr>
                    <td><?=$ad['id_admin']; ?></td>
                    <td><?=$ad['nama_admin']; ?></td>
                    <td><?=$ad['username']; ?></td>
                    <td><?=$ad['password']; ?></td>
                    <td>
                    <a href="edit.admin.php?id_admin=<?= $ad['id_admin']; ?>">
                        <button>Edit</button>
                    </a>

                    <a href="hapus.admin.php?id_admin=<?= $ad['id_admin']; ?>"
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