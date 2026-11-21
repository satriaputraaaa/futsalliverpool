<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Proses update status
    $query = mysqli_query($conn, "UPDATE booking SET status = 'Lunas' WHERE id_booking = '$id'");
    
    // Setelah update, balik otomatis ke halaman databooking.php
    header("Location: databooking.php");
    exit();
} else {
    header("Location: databooking.php");
    exit();
}
?>