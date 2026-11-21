<?php 
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
include 'koneksi.php';

if (isset($_POST['simpan'])) {
    $nama_tim = mysqli_real_escape_string($conn, $_POST['nama_tim']);
    $nama_pelanggan = mysqli_real_escape_string($conn, $_POST['nama_pelanggan']);
    $no_wa = mysqli_real_escape_string($conn, $_POST['no_whatsapp']);
    $id_lapangan = $_POST['id_lapangan'];
    $tgl = $_POST['tgl_main'];
    $jam_mulai = $_POST['jam_mulai'];
    $durasi = $_POST['durasi'];
    $total = $_POST['total_harga'];
    $status = $_POST['status'];

    // Hitung Jam Selesai
    $jam_selesai = date('H:i', strtotime("+$durasi hour", strtotime($jam_mulai)));

    // VALIDASI: Cek tabrakan jadwal
    $cek = mysqli_query($conn, "SELECT * FROM booking 
                                WHERE id_lapangan = '$id_lapangan' 
                                AND tgl_main = '$tgl' 
                                AND (
                                    ('$jam_mulai' >= jam_mulai AND '$jam_mulai' < jam_selesai) OR 
                                    ('$jam_selesai' > jam_mulai AND '$jam_selesai' <= jam_selesai) OR
                                    (jam_mulai >= '$jam_mulai' AND jam_mulai < '$jam_selesai')
                                )");

    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Gagal! Jam ini sudah terisi orang lain.'); window.history.back();</script>";
    } else {
        // SIMPAN ke tabel booking (Pastikan kolom no_whatsapp sudah kamu tambah di tabel booking)
        $query_booking = "INSERT INTO booking (nama_tim, no_whatsapp, id_lapangan, tgl_main, jam_mulai, jam_selesai, durasi, total_harga, status) 
                          VALUES ('$nama_tim', '$no_wa', '$id_lapangan', '$tgl', '$jam_mulai', '$jam_selesai', '$durasi', '$total', '$status')";
        
        if(mysqli_query($conn, $query_booking)) {
            echo "<script>alert('Booking Berhasil!'); window.location='databooking.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Booking Baru - Liverpool GOR</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background-color: #f4f7f6; display: flex; }
        .sidebar { width: 250px; background-color: #2c3e50; color: white; height: 100vh; padding: 20px 0; position: fixed; }
        .sidebar h2 { text-align: center; color: #d35400; font-weight: bold; }
        .sidebar a { display: block; color: white; padding: 15px 20px; text-decoration: none; border-bottom: 1px solid #34495e; }
        .main-content { margin-left: 250px; padding: 40px; width: 100%; display: flex; justify-content: center; }
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 100%; max-width: 550px; border-top: 5px solid #d35400; }
        .box-jadwal { background: #fff3cd; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ffeeba; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #34495e; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        .btn-simpan { background: #d35400; color: white; border: none; padding: 15px; cursor: pointer; font-weight: bold; width: 100%; border-radius: 8px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>LIVERPOOL GOR</h2>
        <a href="databooking.php">⬅ KEMBALI</a>
    </div>

    <div class="main-content">
        <div class="card">
            <h2>Form Booking Baru</h2>

            <div class="box-jadwal" id="box-jadwal">
                <p><strong>Status Lapangan:</strong></p>
                <div id="list-jadwal" style="font-size: 14px;">Pilih Tanggal & Lapangan...</div>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label>Nama Tim:</label>
                    <input type="text" name="nama_tim" required>
                </div>
                <div class="form-group">
                    <label>Nama Pembooking:</label>
                    <input type="text" name="nama_pelanggan" required>
                </div>
                <div class="form-group">
                    <label>No Whatsapp:</label>
                    <input type="text" name="no_whatsapp" placeholder="Contoh: 08123456789" required>
                </div>
                <div class="form-group">
                    <label>Pilih Lapangan:</label>
                    <select name="id_lapangan" id="id_lapangan" onchange="cekJadwal()" required>
                        <option value="">-- Pilih Lapangan --</option>
                        <?php 
                        $l = mysqli_query($conn, "SELECT * FROM lapangan");
                        while($lap = mysqli_fetch_assoc($l)) echo "<option value='{$lap['id_lapangan']}' data-harga='{$lap['harga']}'>{$lap['nama_lapangan']}</option>";
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Durasi (Jam):</label>
                    <input type="number" name="durasi" id="durasi" min="1" value="1" oninput="hitung()" required>
                </div>
                <div class="form-group">
                    <label>Tanggal Main:</label>
                    <input type="date" name="tgl_main" id="tgl_main" onchange="cekJadwal()" required>
                </div>
                <div style="display:flex; gap:10px;">
                    <div class="form-group" style="flex:1;">
                        <label>Jam Mulai:</label>
                        <input type="time" name="jam_mulai" id="jam_mulai" onchange="hitung()" required>
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Jam Selesai:</label>
                        <input type="time" id="jam_selesai_display" readonly style="background:#eee;">
                    </div>
                </div>
                <div class="form-group">
                    <label>Total Harga:</label>
                    <input type="number" name="total_harga" id="total_harga" readonly style="background:#eee; font-weight:bold; color:green;">
                </div>
                <div class="form-group">
                    <label>Status:</label>
                    <select name="status">
                        <option value="Pending">Pending</option>
                        <option value="Lunas">Lunas</option>
                    </select>
                </div>
                <button type="submit" name="simpan" class="btn-simpan">Konfirmasi Booking</button>
            </form>
        </div>
    </div>

<script>
// (Script cekJadwal dan hitung tetap sama seperti kode kamu)
function cekJadwal() {
    var tgl = document.getElementById("tgl_main").value;
    var lap = document.getElementById("id_lapangan").value;
    var list = document.getElementById("list-jadwal");
    if(tgl && lap) {
        fetch('prosescek.php?tgl=' + tgl + '&lap=' + lap)
        .then(res => res.json())
        .then(data => { list.innerHTML = data.list; document.getElementById("jam_mulai").value = data.next_jam; hitung(); });
    }
}
function hitung() {
    var mulai = document.getElementById("jam_mulai").value;
    var durasi = parseInt(document.getElementById("durasi").value) || 1;
    var lap = document.getElementById("id_lapangan");
    if(mulai && lap.selectedIndex > 0) {
        var harga = lap.options[lap.selectedIndex].getAttribute("data-harga");
        var jamSplit = mulai.split(":");
        var jamSelesai = parseInt(jamSplit[0]) + durasi;
        document.getElementById("jam_selesai_display").value = (jamSelesai < 10 ? '0' : '') + jamSelesai + ":" + jamSplit[1];
        if(harga) document.getElementById("total_harga").value = parseInt(harga) * durasi;
    }
}
</script>
</body>
</html>