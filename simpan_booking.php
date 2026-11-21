<?php
include "koneksi.php";

// Ambil data dari URL (GET) yang dikirim oleh prosesbooking.php
$tgl_main   = $_GET['tgl'];
$jam_mulai  = $_GET['jam'];
$promo      = isset($_GET['promo']) ? $_GET['promo'] : 0; // Cek apakah ada diskon

// Ambil data tambahan dari session atau form sebelumnya (sesuaikan variabelnya)
// Misal kita ambil nama_tim dan id_lapangan dari session atau default
session_start();
$nama_tim    = isset($_SESSION['nama_tim']) ? $_SESSION['nama_tim'] : 'Tim Tamu';
$id_lapangan = isset($_SESSION['id_lapangan']) ? $_SESSION['id_lapangan'] : 1;
$harga_asli  = 150000; // Contoh harga per jam GOR Liverpool Depok

// LOGIKA REVENUE OPTIMIZATION: Hitung harga setelah diskon
$total_harga = $harga_asli;
if ($promo > 0) {
    $diskon      = ($promo / 100) * $harga_asli;
    $total_harga = $harga_asli - $diskon;
}

// Hitung jam selesai (asumsi durasi 1 jam)
$jam_selesai = date('H:i:s', strtotime($jam_mulai . ' +1 hour'));

// Query Insert ke tabel booking sesuai struktur database kamu
$query = "INSERT INTO booking (nama_tim, id_lapangan, tgl_main, jam_mulai, jam_selesai, total_harga, status) 
          VALUES ('$nama_tim', '$id_lapangan', '$tgl_main', '$jam_mulai', '$jam_selesai', '$total_harga', 'Pending')";

if (mysqli_query($koneksi, $query)) {
    if ($promo > 0) {
        echo "<script>
                alert('Berhasil! Kamu dapet diskon $promo% karena menutup slot gantung. Total bayar: Rp " . number_format($total_harga) . "');
                window.location='databooking.php';
              </script>";
    } else {
        echo "<script>
                alert('Booking Berhasil disimpan!');
                window.location='databooking.php';
              </script>";
    }
} else {
    echo "Error: " . mysqli_error($koneksi);
}
?>