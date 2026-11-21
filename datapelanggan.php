<?php 
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
include 'koneksi.php'; 

// --- PROSES LOGO UNTUK PDF & PRINT ---
$path = 'image/logo.png'; 
$base64 = '';
if (file_exists($path)) {
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pelanggan - Liverpool GOR</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
    
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background-color: #f4f7f6; display: flex; }
        .sidebar { width: 250px; background-color: #2c3e50; color: white; height: 100vh; padding: 20px 0; position: fixed; }
        .logo-gor { width: 100px; height: auto; display: block; margin: 0 auto 10px auto; border-radius: 50%; box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
        .sidebar h2 { text-align: center; color: #d35400; font-size: 20px; margin-bottom: 30px; }
        .sidebar a { display: block; color: white; padding: 15px 20px; text-decoration: none; border-bottom: 1px solid #34495e; transition: 0.3s; }
        .sidebar a:hover, .active { background-color: #d35400; padding-left: 25px; }
        .main-content { margin-left: 250px; padding: 40px; width: 100%; box-sizing: border-box; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 3px solid #d35400; padding-bottom: 10px; }
        .table-container { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        
        /* Tombol Export */
        .dt-button { background: #2c3e50 !important; color: white !important; border-radius: 8px !important; border: none !important; font-size: 12px !important; margin-bottom: 15px !important; padding: 8px 15px !important; }
        .dt-button:hover { background: #d35400 !important; }

        /* ==========================================================
           KUNCI SIMETRIS: HEADER & ISI SEMUA RATA TENGAH
           ========================================================== */
        #tabelPelanggan thead th { 
            text-align: center !important; 
            vertical-align: middle !important; 
            background-color: #2c3e50; 
            color: white;
            padding: 15px !important;
        }

        #tabelPelanggan tbody td { 
            text-align: center !important; 
            vertical-align: middle !important; 
            padding: 15px !important;
            height: 55px;
        }

        /* Khusus Nama Tim kalau lu mau tetep agak tebel */
        .team-name { font-weight: 700; color: #2c3e50; }

        .badge-status { color: white; font-weight: 600; padding: 6px 12px; border-radius: 6px; font-size: 11px; text-transform: uppercase; display: inline-block; min-width: 110px; text-align: center; }
        
        tr:hover { background-color: #fdf2e9; }
        .print-header-logo { display: none; }

        @media print {
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .sidebar, .dt-buttons, .admin-name, .dataTables_filter, .dataTables_info, .dataTables_paginate { display: none !important; }
            .main-content { margin: 0 !important; padding: 0 !important; width: 100% !important; }
            .print-header-logo { display: block !important; text-align: center !important; margin-bottom: 20px !important; }
            .print-header-logo img { width: 90px !important; border-radius: 50% !important; }
            table#tabelPelanggan { width: 100% !important; border-collapse: collapse !important; border: 1px solid #333 !important; }
            table#tabelPelanggan thead th, table#tabelPelanggan tbody td { border: 1px solid #333 !important; text-align: center !important; }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <img src="image/logo.png" alt="Logo" class="logo-gor">
        <h2>LIVERPOOL GOR</h2>
        <a href="dashboard.php">📊 Dashboard</a>
        <a href="datalapangan.php">⚽ Data Lapangan</a>
        <a href="datapelanggan.php" class="active">👥 Data Pelanggan</a>
        <a href="databooking.php">📅 Data Booking</a>
        <a href="logout.php">🚪 Logout</a>
    </div>

    <div class="main-content">
        <div class="print-header-logo"><img src="image/logo.png" alt="Logo"></div>
        <div class="header">
            <h2>Daftar Database Pelanggan</h2>
            <div style="background: #2c3e50; color: white; padding: 5px 15px; border-radius: 20px; font-size: 14px;">👤 Admin: <?= htmlspecialchars($_SESSION['admin']); ?></div>
        </div>

        <div class="table-container">
            <table id="tabelPelanggan" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Tim</th>
                        <th>No. WhatsApp</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT DISTINCT nama_tim, no_whatsapp, status FROM booking WHERE nama_tim != '' GROUP BY nama_tim ORDER BY nama_tim ASC";
                    $query = mysqli_query($conn, $sql);
                    $no = 1;
                    while($row = mysqli_fetch_assoc($query)) {
                        $status_sekarang = ($row['status'] == 'Lunas' || $row['status'] == 'Aktif') ? 'PELANGGAN AKTIF' : 'NON-AKTIF';
                        $warna_badge = ($status_sekarang == 'PELANGGAN AKTIF') ? '#2ecc71' : '#e74c3c';
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td class="team-name"><?= htmlspecialchars($row['nama_tim']); ?></td>
                        <td><?= !empty($row['no_whatsapp']) ? htmlspecialchars($row['no_whatsapp']) : 'N/A'; ?></td>
                        <td>
                            <span class="badge-status" style="background: <?= $warna_badge; ?>;">
                                <?= $status_sekarang; ?>
                            </span>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>

    <script>
    $(document).ready(function() {
        var logoBase64 = "<?= $base64; ?>";

        $('#tabelPelanggan').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '📊 Export Excel',
                    title: 'Database Pelanggan Liverpool GOR',
                    exportOptions: { columns: [0, 1, 2, 3] }
                },
                {
                    extend: 'pdfHtml5',
                    text: '📕 Export PDF',
                    title: 'DATABASE PELANGGAN LIVERPOOL GOR',
                    exportOptions: { columns: [0, 1, 2, 3] },
                    customize: function (doc) {
                        if (logoBase64) {
                            doc.content.splice(0, 0, { margin: [0, 0, 0, 12], alignment: 'center', image: logoBase64, width: 70 });
                        }
                        doc.content[1].text = 'DATABASE PELANGGAN LIVERPOOL GOR';
                        doc.content[1].color = '#d35400';
                        doc.content[1].alignment = 'center';
                        doc.content[1].margin = [0, 0, 0, 20];
                        doc.content[2].table.widths = ['10%', '40%', '25%', '25%'];
                        
                        var objLayout = {};
                        objLayout['hLineWidth'] = function(i) { return .8; };
                        objLayout['vLineWidth'] = function(i) { return .8; };
                        objLayout['hLineColor'] = function(i) { return '#444'; };
                        objLayout['vLineColor'] = function(i) { return '#444'; };
                        doc.content[2].layout = objLayout;

                        doc.content[2].table.body[0].forEach(function(h) {
                            h.fillColor = '#2c3e50'; h.color = 'white'; h.alignment = 'center';
                        });

                        for (var i = 1; i < doc.content[2].table.body.length; i++) {
                            doc.content[2].table.body[i].forEach(function(c) { c.alignment = 'center'; });
                            if (i % 2 === 0) { doc.content[2].table.body[i].forEach(function(c) { c.fillColor = '#f2f2f2'; }); }
                        }
                    }
                },
                {
                    extend: 'print',
                    text: '🖨️ Cetak',
                    exportOptions: { columns: [0, 1, 2, 3] }
                }
            ],
            "language": { "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json" }
        });
    });
    </script>
</body>
</html>