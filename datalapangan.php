<?php 
session_start();
// Proteksi: Kalau belum login, tendang ke login
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Lapangan - Liverpool GOR</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; background-color: #f4f7f6; display: flex; }
        
        /* --- SIDEBAR MENU (SINKRON OREN) --- */
        .sidebar { width: 250px; background-color: #2c3e50; color: white; height: 100vh; padding: 20px 0; position: fixed; }
        .logo-gor { width: 100px; height: auto; display: block; margin: 0 auto 10px auto; border-radius: 50%; box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
        .sidebar h2 { text-align: center; margin-top: 0; margin-bottom: 30px; font-size: 18px; color: #d35400; }
        .sidebar a { display: block; color: white; padding: 15px 20px; text-decoration: none; border-bottom: 1px solid #34495e; transition: 0.3s; }
        .sidebar a:hover, .active { background-color: #d35400; padding-left: 25px; }

        /* --- MAIN CONTENT --- */
        .main-content { margin-left: 250px; padding: 40px; width: 100%; box-sizing: border-box; }
        
        /* HEADER DENGAN GARIS OREN SAKTI */
        .header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border-bottom: 3px solid #d35400; 
            padding-bottom: 15px; 
            margin-bottom: 30px; 
        }
        .header h2 { color: #2c3e50; margin: 0; font-weight: 700; }
        
        /* Tombol Tambah */
        .btn-tambah { background-color: #d35400; color: white; padding: 10px 20px; text-decoration: none; border-radius: 8px; font-weight: bold; transition: 0.3s; }
        .btn-tambah:hover { background-color: #b34700; box-shadow: 0 4px 8px rgba(0,0,0,0.2); }

        /* --- TABEL ESTETIK --- */
        .table-container { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        table { width: 100%; border-collapse: collapse; overflow: hidden; }
        th, td { padding: 15px; text-align: center; border-bottom: 1px solid #eee; }
        th { background-color: #2c3e50; color: white; text-transform: uppercase; font-size: 13px; letter-spacing: 1px; }
        
        /* Nama Lapangan Rata Kiri biar rapi */
        td:nth-child(2), td:nth-child(3) { text-align: left; }

        tr:hover { background-color: #fff4ed; }

        /* Tombol Aksi */
        .btn-aksi { padding: 8px 15px; border-radius: 6px; text-decoration: none; color: white; font-size: 12px; font-weight: bold; display: inline-block; margin-right: 5px; transition: 0.3s; }
        .btn-edit { background-color: #d35400; }
        .btn-edit:hover { background-color: #b34700; }
        .btn-hapus { background-color: #c0392b; }
        .btn-hapus:hover { background-color: #a03024; }

        /* Style untuk teks harga */
        .harga-text { font-weight: bold; color: #27ae60; white-space: nowrap; }
    </style>
</head>
<body>

   <div class="sidebar">
        <img src="image/logo.png" alt="Logo" class="logo-gor">
        <h2>LIVERPOOL GOR</h2>
        <a href="dashboard.php">📊 Dashboard</a>
        <a href="datalapangan.php" class="active">⚽ Data Lapangan</a>
        <a href="datapelanggan.php">👥 Data Pelanggan</a>
        <a href="databooking.php">📅 Data Booking</a>
        <a href="logout.php">🚪 Logout</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h2>Manajemen Data Lapangan</h2>
            <a href="tambahlapangan.php" class="btn-tambah">+ Tambah Lapangan Baru</a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Lapangan</th>
                        <th>Keterangan</th>
                        <th>Harga / Jam</th> 
                        <th width="180">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = mysqli_query($conn, "SELECT * FROM lapangan ORDER BY id_lapangan ASC");
                    $no = 1;
                    if(mysqli_num_rows($query) > 0) {
                        while($row = mysqli_fetch_assoc($query)) {
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><strong><?= htmlspecialchars($row['nama_lapangan']); ?></strong></td>
                        <td><?= htmlspecialchars($row['keterangan']); ?></td>
                        <td class="harga-text">
                            Rp <?= number_format($row['harga'], 0, ',', '.'); ?>
                        </td> 
                        <td>
                            <a href="editlapangan.php?id=<?= $row['id_lapangan']; ?>" class="btn-aksi btn-edit">Edit</a>
                            <a href="hapuslapangan.php?id=<?= $row['id_lapangan']; ?>" class="btn-aksi btn-hapus" onclick="return confirm('Yakin mau hapus data ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align:center; padding:20px;'>Belum ada data lapangan.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>