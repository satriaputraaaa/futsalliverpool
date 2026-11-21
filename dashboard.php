<?php
session_start();

// 1. PROTEKSI HALAMAN
if (!isset($_SESSION['admin'])) {
    echo "<script>
            alert('Silahkan login terlebih dahulu!');
            window.location.href = 'index.php';
          </script>";
    exit();
}

include 'koneksi.php';

// 2. AMBIL DATA STATISTIK
$total_booking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM booking"))['total'];
$total_pelanggan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT nama_tim) as total FROM booking"))['total'];
$total_lunas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM booking WHERE status='Lunas'"))['total'];

// Data untuk Grafik
$labels = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
$chart_data = [5, 8, 12, 6, 15, 20, 18]; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Liverpool GOR</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background-color: #f4f7f6; display: flex; }
        
        /* Sidebar */
        .sidebar { width: 250px; background-color: #2c3e50; color: white; height: 100vh; padding: 20px 0; position: fixed; }
        .logo-gor { width: 100px; height: auto; display: block; margin: 0 auto 10px auto; border-radius: 50%; box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
        .sidebar h2 { text-align: center; margin-top: 0; margin-bottom: 30px; font-size: 20px; color: #d35400; }
        .sidebar a { display: block; color: white; padding: 15px 20px; text-decoration: none; border-bottom: 1px solid #34495e; transition: 0.3s; }
        .sidebar a:hover, .active { background-color: #d35400; padding-left: 25px; }

        /* Main Content */
        .main-content { margin-left: 250px; padding: 40px; width: 100%; box-sizing: border-box; }
        
        .header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 30px; 
            border-bottom: 3px solid #d35400; 
            padding-bottom: 10px; 
        }
        
        /* Card Stats */
        .stats-container { display: flex; gap: 20px; margin-bottom: 30px; }
        .card { 
            background: white; 
            padding: 20px; 
            border-radius: 12px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.05); 
            flex: 1; 
            text-align: center; 
            border-top: 5px solid #d35400; 
        }
        .card h3 { margin: 0; color: #7f8c8d; font-size: 14px; text-transform: uppercase; }
        .card h1 { margin: 10px 0 0 0; color: #2c3e50; font-size: 32px; }

        /* KUNCI GRAFIK MEMANJANG KE SAMPING */
        .chart-container { 
            background: white; 
            padding: 20px; 
            border-radius: 12px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.05); 
            margin-bottom: 30px;
            width: 100%; /* Penuh ke samping */
            box-sizing: border-box;
        }

        .table-container { 
            background: white; 
            padding: 25px; 
            border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.08); 
        }
        
        table.dataTable thead th { background-color: #2c3e50 !important; color: white !important; text-align: center !important; }
        table.dataTable tbody td { text-align: center !important; vertical-align: middle !important; }

        .badge { padding: 6px 12px; border-radius: 6px; color: white; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .bg-success { background-color: #2ecc71; }
        .bg-warning { background-color: #f39c12; }
    </style>
</head>
<body>

    <div class="sidebar">
        <img src="image/logo.png" alt="Logo" class="logo-gor">
        <h2>LIVERPOOL GOR</h2>
        <a href="dashboard.php" class="active">📊 Dashboard</a>
        <a href="datalapangan.php">⚽ Data Lapangan</a>
        <a href="datapelanggan.php">👥 Data Pelanggan</a>
        <a href="databooking.php">📅 Data Booking</a>
        <a href="logout.php">🚪 Logout</a>
    </div>
    
    <div class="main-content">
        <div class="header">
            <div>
                <h2 style="margin:0; color: #2c3e50;">Selamat Datang, Admin!</h2>
                <p style="margin:5px 0 0 0; color: #7f8c8d;">Ringkasan aktivitas Liverpool GOR hari ini.</p>
            </div>
            <div style="background: #2c3e50; color: white; padding: 8px 18px; border-radius: 20px; font-size: 14px;">
                📅 <?= date('d F Y'); ?>
            </div>
        </div>

        <div class="stats-container">
            <div class="card">
                <h3>Total Booking</h3>
                <h1><?= $total_booking; ?></h1>
            </div>
            <div class="card">
                <h3>Total Pelanggan</h3>
                <h1><?= $total_pelanggan; ?></h1>
            </div>
            <div class="card">
                <h3>Pembayaran Lunas</h3>
                <h1><?= $total_lunas; ?></h1>
            </div>
        </div>

        <div class="chart-container">
            <h3 style="margin-top:0; color: #2c3e50; border-left: 5px solid #d35400; padding-left: 10px;">Trend Booking Mingguan</h3>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="bookingChart"></canvas>
            </div>
        </div>

        <div class="table-container">
            <h3 style="margin-top:0; color: #2c3e50; border-left: 5px solid #d35400; padding-left: 10px;">Antrean Booking Terbaru</h3>
            <table id="tabelAntrean" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Tim</th>
                        <th>Lapangan</th>
                        <th>Tanggal</th>
                        <th>Jam Main</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query_tabel = "SELECT b.nama_tim, l.nama_lapangan, b.tgl_main, b.jam_mulai, b.jam_selesai, b.status
                                    FROM booking b
                                    JOIN lapangan l ON b.id_lapangan = l.id_lapangan
                                    ORDER BY b.id_booking DESC LIMIT 10";
                    $result = mysqli_query($conn, $query_tabel);
                    $no = 1;
                    while($row = mysqli_fetch_assoc($result)) {
                        $jam_main = date('H.i', strtotime($row['jam_mulai'])) . " - " . date('H.i', strtotime($row['jam_selesai']));
                        $status_badge = ($row['status'] == 'Lunas') ? "<span class='badge bg-success'>Lunas</span>" : "<span class='badge bg-warning'>Pending</span>";
                    ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><strong><?= htmlspecialchars($row['nama_tim']); ?></strong></td>
                            <td><?= htmlspecialchars($row['nama_lapangan']); ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tgl_main'])); ?></td>
                            <td><?= $jam_main; ?> WIB</td>
                            <td><?= $status_badge; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#tabelAntrean').DataTable({
                "language": { "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json" },
                "pageLength": 5,
                "lengthChange": false,
                "info": false
            });

            // KONFIGURASI GRAFIK WIDE (PANJANG KE SAMPING)
            const ctx = document.getElementById('bookingChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($labels); ?>,
                    datasets: [{
                        label: 'Jumlah Booking',
                        data: <?= json_encode($chart_data); ?>,
                        backgroundColor: '#d35400',
                        borderRadius: 5,
                        hoverBackgroundColor: '#2c3e50',
                        barThickness: 50 // Mengatur ketebalan batang agar tidak terlalu kurus
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false, // Menghilangkan paksaan rasio asli biar bisa kita atur manual
                    plugins: { 
                        legend: { display: false } 
                    },
                    scales: {
                        y: { 
                            beginAtZero: true,
                            grid: { color: '#f0f0f0' }
                        },
                        x: { 
                            grid: { display: false }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>