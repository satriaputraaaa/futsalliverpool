<?php
session_start();
// Proteksi Satpam: Biar nggak sembarang orang bisa akses URL hapus
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include 'koneksi.php';

// Pastikan ID ada di URL
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Proses Hapus
    $delete = mysqli_query($conn, "DELETE FROM lapangan WHERE id_lapangan='$id'");

    if ($delete) {
        // Kalau berhasil, balik ke halaman utama dengan alert (opsional)
        echo "<script>alert('Data Lapangan Berhasil Dihapus!'); window.location='datalapangan.php';</script>";
    } else {
        echo "Gagal menghapus data: " . mysqli_error($conn);
    }
} else {
    // Kalau nggak ada ID, balikin aja ke dashboard
    header("Location: datalapangan.php");
}
?>