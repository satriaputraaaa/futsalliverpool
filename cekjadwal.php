<?php
include 'koneksi.php';

$tgl = $_GET['tgl'];
$lap = $_GET['lap'];

$query = mysqli_query($conn, "SELECT jam_mulai, jam_selesai, nama_tim FROM booking 
                              WHERE tgl_main = '$tgl' AND id_lapangan = '$lap'");

if(mysqli_num_rows($query) > 0) {
    echo "<ul>";
    while($row = mysqli_fetch_assoc($query)) {
        $mulai = date('H:i', strtotime($row['jam_mulai']));
        $selesai = date('H:i', strtotime($row['jam_selesai']));
        echo "<li class='jam-booked'>$mulai - $selesai (Tim: {$row['nama_tim']})</li>";
    }
    echo "</ul>";
} else {
    echo "<span style='color:green;'>Semua jam masih kosong (Free).</span>";
}
?>