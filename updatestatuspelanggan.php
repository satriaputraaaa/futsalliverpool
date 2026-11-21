<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
include 'koneksi.php';

if (isset($_GET['nama']) && isset($_GET['status'])) {
    $nama = mysqli_real_escape_string($conn, $_GET['nama']);
    $status_lama = $_GET['status'];

    // Jika sekarang Aktif, maka ubah jadi Non-Aktif (Pending), dan sebaliknya
    $status_baru = ($status_lama == 'PELANGGAN AKTIF') ? 'Pending' : 'Lunas';

    // Update semua booking tim tersebut biar statusnya berubah barengan
    $query = mysqli_query($conn, "UPDATE booking SET status = '$status_baru' WHERE nama_tim = '$nama'");

    if ($query) {
        header("Location: datapelanggan.php");
        exit();
    } else {
        echo "<script>alert('Gagal update status'); window.location='datapelanggan.php';</script>";
    }
}
?>