<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html>
<head><title>Form Booking</title></head>
<body>
    <h2>Form Booking Lapangan (FCFS)</h2>
    <form action="prosesbooking.php" method="POST">
        Nama Tim: <input type="text" name="nama_tim" required><br>
        Tanggal: <input type="date" name="tanggal" required><br>
        Lapangan: 
        <select name="id_lapangan">
            <?php
            $l = mysqli_query($conn, "SELECT * FROM lapangan");
            while($row = mysqli_fetch_assoc($l)) echo "<option value='{$row['id_lapangan']}'>{$row['nama_lapangan']}</option>";
            ?>
        </select><br>
        Jam: 
        <select name="id_jam">
            <?php
            $j = mysqli_query($conn, "SELECT * FROM jam_operasional");
            while($row = mysqli_fetch_assoc($j)) echo "<option value='{$row['id_jam']}'>{$row['jam_mulai']} - {$row['jam_selesai']}</option>";
            ?>
        </select><br>
        <button type="submit" name="booking">Booking Sekarang</button>
    </form>
</body>
</html>