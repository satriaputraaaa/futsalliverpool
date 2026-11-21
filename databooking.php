<?php 
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
include 'koneksi.php'; 

// --- PROSES EDIT STATUS (AJAX HANDLER) ---
if (isset($_POST['update_status'])) {
    $id = $_POST['id'];
    $current_status = $_POST['status'];
    $new_status = ($current_status == 'Pending') ? 'Lunas' : 'Pending';
    
    $update = mysqli_query($conn, "UPDATE booking SET status='$new_status' WHERE id_booking='$id'");
    if ($update) { echo $new_status; } else { echo "error"; }
    exit();
}

// --- PROSES EDIT JAM OTOMATIS (AJAX HANDLER) ---
if (isset($_POST['update_jam_otomatis'])) {
    $id = $_POST['id'];
    $jam_baru = $_POST['jam_baru'];
    
    // Ambil data lama untuk menghitung durasi asli agar durasi tetap sama
    $res = mysqli_query($conn, "SELECT jam_mulai, jam_selesai FROM booking WHERE id_booking='$id'");
    $data = mysqli_fetch_assoc($res);
    
    $durasi_detik = strtotime($data['jam_selesai']) - strtotime($data['jam_mulai']);
    $jam_selesai_baru = date('H:i:s', strtotime($jam_baru) + $durasi_detik);

    $update = mysqli_query($conn, "UPDATE booking SET jam_mulai='$jam_baru', jam_selesai='$jam_selesai_baru' WHERE id_booking='$id'");
    if ($update) { 
        // Mengembalikan teks jam format baru untuk tampilan tabel
        echo date('H.i', strtotime($jam_baru)) . " - " . date('H.i', strtotime($jam_selesai_baru)) . " WIB"; 
    } else { 
        echo "error"; 
    }
    exit();
}

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
    <title>Data Booking - Liverpool GOR</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
    
    <style>
        /* --- STYLE WEB (LAYAR) --- */
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background-color: #f4f7f6; display: flex; }
        .sidebar { width: 250px; background-color: #2c3e50; color: white; height: 100vh; padding: 20px 0; position: fixed; }
        .logo-gor { width: 100px; height: auto; display: block; margin: 0 auto 10px auto; border-radius: 50%; box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
        .sidebar h2 { text-align: center; color: #d35400; font-size: 20px; }
        .sidebar a { display: block; color: white; padding: 15px 20px; text-decoration: none; border-bottom: 1px solid #34495e; transition: 0.3s; }
        .sidebar a:hover, .active { background-color: #d35400; padding-left: 25px; }
        .main-content { margin-left: 250px; padding: 40px; width: 100%; box-sizing: border-box; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 3px solid #d35400; padding-bottom: 10px; }
        .table-container { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .dt-button { background: #2c3e50 !important; color: white !important; border-radius: 5px !important; border: none !important; font-size: 12px !important; margin-bottom: 15px !important; padding: 8px 15px !important; }
        .dt-button:hover { background: #d35400 !important; }
        .btn-wa { background-color: #25D366; color: white !important; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 12px; display: inline-block; transition: 0.3s; }
        .btn-wa:hover { background-color: #128C7E; transform: scale(1.05); }
        .btn-ai { background-color: #8e44ad; color: white !important; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; border: none; cursor: pointer; transition: 0.3s; margin-left: 10px; }
        .btn-ai:hover { background-color: #9b59b6; transform: scale(1.05); }
        .badge { padding: 6px 12px; border-radius: 6px; color: white; font-size: 11px; font-weight: bold; text-transform: uppercase; display: inline-block; cursor: pointer; transition: 0.2s; }
        .badge:hover { opacity: 0.8; transform: scale(1.05); }
        .bg-lunas { background-color: #2ecc71; }
        .bg-pending { background-color: #f39c12; }
        .cell-jam { cursor: pointer; transition: 0.2s; border-radius: 4px; padding: 5px; }
        .cell-jam:hover { background-color: #f0f0f0; }
        tr:hover { background-color: #fff4ed; }

        .print-only { display: none; }

        @media print {
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .sidebar, .dt-buttons, .header a, .header button, .dataTables_filter, .dataTables_info, .dataTables_paginate, .btn-wa { display: none !important; }
            .main-content { margin: 0 !important; padding: 0 !important; width: 100% !important; }
            .table-container { box-shadow: none !important; background: transparent !important; padding: 0 !important; border-radius: 0 !important; }
            .print-only { display: block !important; text-align: center !important; margin-bottom: 20px !important; }
            .print-only img { width: 100px !important; border-radius: 50% !important; }
            .header h2 { text-align: center !important; width: 100% !important; color: #d35400 !important; font-size: 24px !important; border: none !important; }
            table#tabelBooking { width: 100% !important; border-collapse: collapse !important; border: 1px solid #444 !important; }
            table#tabelBooking thead th { background-color: #2c3e50 !important; color: white !important; border: 1px solid #444 !important; }
            table#tabelBooking tbody td { border: 1px solid #444 !important; }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <img src="image/logo.png" alt="Logo" class="logo-gor">
        <h2>LIVERPOOL GOR</h2>
        <a href="dashboard.php">📊 Dashboard</a>
        <a href="datalapangan.php">⚽ Data Lapangan</a>
        <a href="datapelanggan.php">👥 Data Pelanggan</a>
        <a href="databooking.php" class="active">📅 Data Booking</a>
        <a href="logout.php">🚪 Logout</a>
    </div>

    <div class="main-content">
        <div class="print-only">
            <img src="image/logo.png" alt="Logo">
        </div>

        <div class="header">
            <h2>Daftar Booking Lapangan</h2>
            <div>
                <button onclick="runAIOptimizer()" class="btn-ai">✨ AI Optimizer</button>
                <a href="tambahbooking.php" style="background:#d35400; color:white; padding:10px 20px; border-radius:8px; text-decoration:none; font-weight:bold;">+ Booking Baru</a>
            </div>
        </div>

        <div class="table-container">
            <table id="tabelBooking" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Tim</th>
                        <th>WhatsApp</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM booking ORDER BY id_booking DESC";
                    $query = mysqli_query($conn, $sql);
                    $no = 1;
                    while($row = mysqli_fetch_assoc($query)) {
                        $clean_wa = preg_replace('/[^0-9]/', '', $row['no_whatsapp']);
                        $wa_final = (substr($clean_wa, 0, 1) === '0') ? '62' . substr($clean_wa, 1) : $clean_wa;
                        $tgl = date('d/m/Y', strtotime($row['tgl_main']));
                        $jam_tampil = date('H.i', strtotime($row['jam_mulai'])) . " - " . date('H.i', strtotime($row['jam_selesai'])) . " WIB";
                        
                        $status_wa = ($row['status'] == 'Pending') ? "*BELUM LUNAS*" : "*LUNAS*";
                        $pesan_wa = "Halo *" . htmlspecialchars($row['nama_tim']) . "*,%0A%0AKami dari *Liverpool GOR* ingin menginformasikan jadwal booking Anda:%0A%0A" .
                                    "📅 Tanggal: *" . $tgl . "*%0A" .
                                    "⏰ Jam: *" . $jam_tampil . "*%0A" .
                                    "💰 Status: " . $status_wa . "%0A%0A" .
                                    "Harap datang 15 menit sebelum jam main ya. Sampai jumpa di lapangan! ⚽";
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><strong><?= htmlspecialchars($row['nama_tim']); ?></strong></td>
                        <td>
                            <?php if(!empty($row['no_whatsapp'])): ?>
                                <a href="https://api.whatsapp.com/send?phone=<?= $wa_final; ?>&text=<?= $pesan_wa; ?>" target="_blank" class="btn-wa">📩 Kirim Notif</a>
                            <?php else: ?>
                                <span style="color:#ccc;">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $tgl; ?></td>
                        <td class="cell-jam" data-id="<?= $row['id_booking']; ?>" title="Klik untuk pindah jam otomatis via AI">
                            <span class="text-jam"><?= $jam_tampil; ?></span>
                        </td>
                        <td>
                            <span class="badge toggle-status <?= ($row['status'] == 'Pending') ? 'bg-pending' : 'bg-lunas'; ?>" 
                                  data-id="<?= $row['id_booking']; ?>" 
                                  data-status="<?= $row['status']; ?>">
                                <?= $row['status']; ?>
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
    let saranJamAI = null;
    let idTimSaran = null;

    // --- FUNGSI TOGGLE STATUS VIA AJAX ---
    $(document).on('click', '.toggle-status', function() {
        let span = $(this);
        let id = span.data('id');
        let status = span.data('status');

        if(confirm('Ubah status booking ini menjadi ' + (status == 'Pending' ? 'Lunas' : 'Pending') + '?')) {
            $.ajax({
                url: 'databooking.php',
                method: 'POST',
                data: { update_status: true, id: id, status: status },
                success: function(response) {
                    if(response !== 'error') {
                        span.text(response);
                        span.data('status', response);
                        if(response == 'Lunas') {
                            span.removeClass('bg-pending').addClass('bg-lunas');
                        } else {
                            span.removeClass('bg-lunas').addClass('bg-pending');
                        }
                    } else {
                        alert('Gagal mengubah status.');
                    }
                }
            });
        }
    });

    // --- FUNGSI AGENTIC AI OPTIMIZER ---
async function runAIOptimizer() {
    let tanggalList = [];
    
    // Ambil semua baris dari body tabel booking
    $('#tabelBooking tbody tr').each(function() {
        // Ambil teks tanggal dari kolom ke-4 (indeks 3), bersihkan spasi di awal/akhir
        let tglStr = $(this).find('td').eq(3).text().trim(); 
        
        if(tglStr && tglStr !== "N/A") {
            // Deteksi tanda pemisah secara dinamis (bisa berupa '/' atau '-')
            let separator = tglStr.includes('/') ? '/' : '-';
            let parts = tglStr.split(separator);
            
            if(parts.length === 3) {
                let d = parts[0].trim();
                let m = parts[1].trim();
                let y = parts[2].trim();
                
                // Pastikan format keluaran selalu YYYY-MM-DD standar Internasional
                let formattedTgl = y + '-' + m + '-' + d; 
                
                if (!tanggalList.includes(formattedTgl)) {
                    tanggalList.push(formattedTgl);
                }
            }
        }
    });

    if (tanggalList.length === 0) {
        alert('Tidak ada data tanggal booking yang valid untuk dianalisis.');
        return;
    }

    let ditemukan = false;
    
    // Iterasi setiap tanggal unik yang terkumpul
    for (let tgl of tanggalList) {
        try {
            let response = await fetch('http://127.0.0.1:5000/detect-gap', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ tanggal: tgl })
            });
            
            if (!response.ok) continue;
            let data = await response.json();
            
            if (data.status === 'proactive') {
                ditemukan = true;
                
                let jamLamaFormat = data.jam_lama.replace(/:/g, '.');
                let jamBaruFormat = data.jam_baru.replace(/:/g, '.');
                
                // Ubah kembali format tanggal YYYY-MM-DD ke DD/MM/YYYY untuk teks pesan pesan WA
                let tglSplit = tgl.split('-');
                let tglTampil = tglSplit[2] + '/' + tglSplit[1] + '/' + tglSplit[0];

                let pesanWA = `Halo *${data.nama_tim}*, salam dari Liverpool GOR! ⚽\n\nMelihat jadwal main tim Anda pada tgl ${tglTampil} di jam *${jamLamaFormat}*, kami informasikan ada lapangan kosong di jam *${jamBaruFormat}*.\n\nApakah jadwal tim Anda bisa dimajukan ke jam *${jamBaruFormat}*? Kabari ya, terima kasih!`;

                let nomorTujuan = (data.no_wa && data.no_wa !== 'undefined') ? data.no_wa : '';
                let linkWA = `https://web.whatsapp.com/send?phone=${nomorTujuan}&text=${encodeURIComponent(pesanWA)}`;

                if (confirm('AI menemukan celah kosong untuk tim "' + data.nama_tim + '" pada tanggal ' + tglTampil + '.\n\nKlik OK untuk membuka WhatsApp.')) {
                    
                    let hasLeftTab = false;

                    const onVisibilityChange = () => {
                        if (document.hidden) {
                            hasLeftTab = true;
                        }
                        if (!document.hidden && hasLeftTab) {
                            document.removeEventListener('visibilitychange', onVisibilityChange);
                            
                            setTimeout(() => {
                                if (confirm('Anda telah kembali dari WhatsApp.\n\nApakah pelanggan "' + data.nama_tim + '" setuju untuk memajukan jadwal ke jam ' + jamBaruFormat + '? Klik OK untuk update sistem.')) {
                                    $.post('databooking.php', {
                                        update_jam_otomatis: true, 
                                        id: data.id_booking, 
                                        jam_baru: data.jam_baru + ":00"
                                    }, function(res) {
                                        if(res.trim() !== 'error') {
                                            alert('Jadwal berhasil diperbarui!');
                                            location.reload();
                                        } else {
                                            alert('Gagal update database.');
                                        }
                                    });
                                }
                            }, 400);
                        }
                    };

                    document.addEventListener('visibilitychange', onVisibilityChange);
                    window.open(linkWA, '_blank');
                }
                break; 
            }
        } catch (error) {
            console.error("Gagal menghubungi AI Server.", error);
        }
    }
    
    if (!ditemukan) {
        alert('Pesan dari Agentic AI: Semua jadwal yang tersedia sudah optimal.');
    }
}

    $(document).ready(function() {
        var logoBase64 = "<?= $base64; ?>";
        $('#tabelBooking').DataTable({
            dom: 'Bfrtip',
            buttons: [
                { extend: 'excelHtml5', text: '📊 Excel', exportOptions: { columns: [0, 1, 3, 4, 5] } },
                { extend: 'pdfHtml5', text: '📕 PDF', customize: function (doc) { if (logoBase64) { doc.content.splice(0, 0, { alignment: 'center', image: logoBase64, width: 50 }); } } },
                { extend: 'print', text: '🖨️ Cetak', exportOptions: { columns: [0, 1, 3, 4, 5] } }
            ]
        });
    });
    </script>
</body>
</html>