<?php

include("../connection.php");

$id_admin = $_POST ["id_admin"];
$nama_admin = $_POST ["nama_admin"];
$username = $_POST ["username"];
$password = $_POST ["password"];

$sql = "INSERT INTO ADMIN (id_admin,nama_admin,username,password)
VALUES ('$id_admin','$nama_admin','$username','$password')";

 if (mysqli_query($conn, $sql)) {
    echo "<script>
            alert('Data berhasil ditambahkan');
            window.location='tampil.admin.php';
          </script>";
} else {
    echo "Gagal menambahkan data: " . mysqli_error($conn);
}
?>