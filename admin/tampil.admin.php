<?php

require_once '../auth.php'; require_admin();
include '../connection.php';

// Mengambil semua data admin
$data = mysqli_query($conn, "
    SELECT *
    FROM admin
    ORDER BY id_admin DESC
");

?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../style.css">
    <title>Data Admin</title>
</head>

<body>
<div class="page-head"><div></div><a class="btn btn-light" href="../dashboard.<?= $_SESSION['role'] ?>.php">← Kembali ke Dashboard</a></div>

<h2>Data Admin</h2>

<a href="form.admin.html">
    Tambah Admin
</a>

<br><br>

<table border="1" cellpadding="8" cellspacing="0">

    <tr>
        <th>ID Admin</th>
        <th>Nama Admin</th>
        <th>USERNAME</th>
        <th>PASSWORD</th>
        <th>Aksi</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($data)) { ?>

    <tr>

        <td>
            <?php echo $row['id_admin']; ?>
        </td>

        <td>
            <?php echo $row['nama_admin']; ?>
        </td>
        <td>
                        <?= $row['username']; ?>
                    </td>

                    <td>
                        <?= $row['password']; ?>
                    </td>
        <td>

            <a href="edit.admin.php?id_admin=<?php
                echo $row['id_admin'];
            ?>">
                Edit
            </a>

            |

            <a
                href="hapus.admin.php?id_admin=<?php
                    echo $row['id_admin'];
                ?>"
                onclick="return confirm(
                    'Yakin ingin menghapus admin ini?'
                )"
            >
                Hapus
            </a>

        </td>

    </tr>

    <?php } ?>

</table>

</body>
</html>