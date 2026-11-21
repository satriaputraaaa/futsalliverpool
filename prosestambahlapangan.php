<?php
include 'koneksi.php';
if (isset($_POST['submit'])) {
    $nama = $_POST['nama_lapangan'];
    $ket = $_POST['keterangan'];
    $sql = "INSERT INTO lapangan (nama_lapangan, keterangan) VALUES ('$nama', '$ket')";
    if (mysqli_query($conn, $sql)) {
        header("Location: datalapangan.php");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>