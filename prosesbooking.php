<?php
// 1. KONEKSI DATABASE (Sesuaikan dengan file koneksi lu)
include "koneksi.php"; // atau sesuaikan path-nya

// 2. AMBIL DATA DARI FORM
// Pastikan nama di dalam $_POST sesuai dengan atribut 'name' di form HTML lu
$tgl_main   = $_POST['tgl_main']; 
$jam_mulai  = $_POST['jam_mulai'];
$nama_tim   = $_POST['nama_tim'];
$id_lapangan = $_POST['id_lapangan'];

// 3. CEK BENTROK (Versi Otomatis Lu yang Dulu)
// Kita cek apakah sudah ada yang booking 'Lunas' di jam dan tanggal tersebut
$query_cek = mysqli_query($koneksi, "SELECT * FROM booking WHERE tgl_main='$tgl_main' AND jam_mulai='$jam_mulai' AND status='Lunas'");

if (mysqli_num_rows($query_cek) > 0) {
    // --- START INTEGRASI AGENTIC AI ---
    
    // Siapkan data untuk dikirim ke Python (Port 5000)
    $data_kirim = array(
        "tanggal" => $tgl_main,
        "jam"     => $jam_mulai
    );
    $payload = json_encode($data_kirim);
    
    // Inisialisasi CURL untuk memanggil AI Agent
    $ch = curl_init('http://127.0.0.1:5000/analyze');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Timeout 5 detik biar gak nunggu kelamaan kalau Python mati
    
    $res = curl_exec($ch);
    $httpCode = curl_getinfo($ch, medical_code: CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $result = json_decode($res, true);
    
    // Jika AI memberikan respon sukses (status 200)
if ($result['status'] == 'conflict') {
    // JIKA PENUH
    $saran = implode(", ", $result['suggestions']);
    echo "<script>
            alert('Waduh penuh! Tapi Agentic AI nyaranin jam: $saran');
            window.history.back();
          </script>";
} else if ($result['status'] == 'available' && $result['promo'] == true) {
    // JIKA SLOT GANTUNG (REVENUE OPTIMIZATION)
    echo "<script>
            if(confirm('HOKI! " . $result['message'] . " \\n\\nAmbil promo ini?')) {
                // Lanjut simpan dengan info diskon (bisa kamu simpan ke session atau database)
                window.location.href = 'simpan_booking.php?promo=10&jam=$jam_mulai&tgl=$tgl_main';
            } else {
                window.location.href = 'simpan_booking.php?jam=$jam_mulai&tgl=$tgl_main';
            }
          </script>";
} else {
    // JIKA TERSEDIA BIASA
    header("Location: simpan_booking.php?jam=$jam_mulai&tgl=$tgl_main");
}
}
?>