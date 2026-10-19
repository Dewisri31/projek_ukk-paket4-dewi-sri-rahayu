<?php

require_once '../auth.php'; require_staff();
include '../connection.php';


// Mengambil semua data kategori
$data = mysqli_query($conn, "
    SELECT kategori.*, COALESCE(admin.nama_admin, petugas.nama_petugas, '-') AS nama_pengelola
    FROM kategori LEFT JOIN admin ON kategori.id_admin=admin.id_admin LEFT JOIN petugas ON kategori.id_petugas=petugas.id_petugas
    ORDER BY id_kategori DESC
");

?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../style.css">
    <title>Data Kategori</title>
</head>

<body>
<div class="page-head"><div></div><a class="btn btn-light" href="../dashboard.<?= $_SESSION['role'] ?>.php">← Kembali ke Dashboard</a></div>

<h2>Data Kategori</h2>

<a href="form.kategori.php">
    Tambah Kategori
</a>

<br><br>

<table border="1" cellpadding="8" cellspacing="0">

    <tr>
        <th>ID Kategori</th>
        <th>Nama Kategori</th>
        <th>Pengelola</th>
        <th>Aksi</th>
    </tr>


    <?php while ($row = mysqli_fetch_assoc($data)) { ?>

    <tr>

        <td>
            <?php echo $row['id_kategori']; ?>
        </td>

        <td>
            <?php echo htmlspecialchars($row['nama_kategori']); ?>
        </td>
        <td><?= htmlspecialchars($row['nama_pengelola']); ?></td>

        <td>

            <a href="edit.kategori.php?id=<?php
                echo $row['id_kategori'];
            ?>">
                Edit
            </a>

            |

            <a
                href="hapus.kategori.php?id=<?php
                    echo $row['id_kategori'];
                ?>"
                onclick="return confirm(
                    'Yakin ingin menghapus kategori ini?'
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