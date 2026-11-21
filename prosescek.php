<?php
include 'koneksi.php';
error_reporting(0); // Supaya gak ada error text yang ngerusak JSON

$tgl = isset($_GET['tgl']) ? $_GET['tgl'] : '';
$lap = isset($_GET['lap']) ? $_GET['lap'] : '';

if($tgl && $lap) {
    // 1. Ambil list booking (Format jam disederhanakan jadi HH:mm)
    $query = mysqli_query($conn, "SELECT TIME_FORMAT(jam_mulai, '%H:%i') as mulai, 
                                         TIME_FORMAT(jam_selesai, '%H:%i') as selesai, 
                                         nama_tim FROM booking 
                                  WHERE tgl_main = '$tgl' AND id_lapangan = '$lap' 
                                  ORDER BY jam_mulai ASC");

    // 2. Ambil jam SELESAI terakhir (Ini untuk estafet otomatis)
    $last_query = mysqli_query($conn, "SELECT TIME_FORMAT(jam_selesai, '%H:%i') as jam_terakhir FROM booking 
                                       WHERE tgl_main = '$tgl' AND id_lapangan = '$lap' 
                                       ORDER BY jam_selesai DESC LIMIT 1");
    $last_data = mysqli_fetch_assoc($last_query);
    
    // Kalau ada data, ambil jam terakhirnya. Kalau kosong, mulai jam 07:00
    $next_jam = ($last_data) ? $last_data['jam_terakhir'] : '00:00';

    $list_html = "";
    if(mysqli_num_rows($query) > 0) {
        while($row = mysqli_fetch_assoc($query)) {
            $list_html .= "• <span style='color:red; font-weight:bold;'>[BOOKED]</span> {$row['mulai']} - {$row['selesai']} ({$row['nama_tim']})<br>";
        }
    } else {
        $list_html = "<span style='color:green; font-weight:bold;'>[READY] Belum ada booking hari ini.</span>";
    }

    // Kirim hasil ke Javascript
    header('Content-Type: application/json');
    echo json_encode([
        'list' => $list_html,
        'next_jam' => $next_jam
    ]);
    exit;
}
?>