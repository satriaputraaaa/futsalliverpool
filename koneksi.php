<?php
$host = "localhost";
$user = "root"; // Default user XAMPP
$pass = "";     // Default password XAMPP kosong
$db   = "dbfutsal";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>