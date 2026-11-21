<?php 
session_start();
// Proteksi Satpam
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php'; 

// Ambil ID dari URL
$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM lapangan WHERE id_lapangan='$id'");
$data = mysqli_fetch_assoc($query);

// Proses Update saat tombol diklik
if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lapangan']);
    $ket = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $harga = mysqli_real_escape_string($conn, $_POST['harga']); // Tangkap input harga baru
    
    // Tambahkan harga='$harga' di dalam query UPDATE
    $update = mysqli_query($conn, "UPDATE lapangan SET nama_lapangan='$nama', keterangan='$ket', harga='$harga' WHERE id_lapangan='$id'");
    
    if($update) {
        echo "<script>alert('Data Berhasil Diperbarui!'); window.location.href='datalapangan.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Lapangan - Liverpool GOR</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 0; 
            background-color: #f4f7f6; 
            display: flex; 
        }

        /* Sidebar agar tetap sinkron */
        .sidebar { 
            width: 250px; 
            background-color: #2c3e50; 
            color: white; 
            height: 100vh; 
            padding: 20px 0; 
            position: fixed; 
        }
        .logo-gor { 
            width: 100px; 
            display: block; 
            margin: 0 auto 10px auto; 
            border-radius: 50%; 
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .sidebar h2 { text-align: center; font-size: 18px; color: #e74c3c; margin-bottom: 30px; }
        .sidebar a { 
            display: block; 
            color: white; 
            padding: 15px 20px; 
            text-decoration: none; 
            border-bottom: 1px solid #34495e; 
            transition: 0.3s; 
        }
        .sidebar a:hover { background-color: #e74c3c; padding-left: 25px; }

        /* Main Content */
        .main-content { 
            margin-left: 250px; 
            padding: 40px; 
            width: 100%; 
            display: flex;
            justify-content: center;
        }

        /* Card Form */
        .card { 
            background: white; 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.1); 
            width: 100%;
            max-width: 500px;
            border-top: 5px solid #e67e22; 
        }

        .card h2 { 
            margin-top: 0; 
            color: #2c3e50; 
            font-size: 24px;
            border-bottom: 2px solid #f4f7f6;
            padding-bottom: 10px;
            margin-bottom: 25px;
        }

        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #34495e; }
        
        input[type="text"], input[type="number"], textarea { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            box-sizing: border-box; 
            font-size: 15px;
            background-color: #f9f9f9;
            transition: 0.3s;
        }

        input[type="text"]:focus, input[type="number"]:focus, textarea:focus {
            border-color: #e67e22;
            outline: none;
            box-shadow: 0 0 8px rgba(230, 126, 34, 0.2);
            background-color: #fff;
        }
        
        textarea { height: 100px; resize: none; }

        /* Tombol */
        .btn-container { display: flex; gap: 10px; margin-top: 10px; }
        .btn-update { 
            background-color: #e67e22; 
            color: white; 
            border: none; 
            padding: 12px 25px; 
            border-radius: 8px; 
            cursor: pointer; 
            font-weight: bold; 
            flex: 2;
            transition: 0.3s;
        }
        .btn-update:hover { background-color: #d35400; transform: translateY(-2px); }
        
        .btn-batal { 
            background-color: #95a5a6; 
            color: white; 
            text-decoration: none; 
            padding: 12px; 
            border-radius: 8px; 
            text-align: center;
            flex: 1;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.3s;
        }
        .btn-batal:hover { background-color: #7f8c8d; }
    </style>
</head>
<body>

    <div class="sidebar">
        <img src="image/logo.png" alt="Logo" class="logo-gor">
        <h2>LIVERPOOL GOR</h2>
        <a href="dashboard.php">📊 Dashboard</a>
        <a href="datalapangan.php">⚽ Data Lapangan</a>
        <a href="datapelanggan.php">👥 Data Pelanggan</a>
        <a href="logout.php">🚪 Logout</a>
    </div>

    <div class="main-content">
        <div class="card">
            <h2>Edit Data Lapangan</h2>
            
            <form method="POST">
                <div class="form-group">
                    <label>Jenis/Nama Lapangan:</label>
                    <input type="text" name="nama_lapangan" value="<?= $data['nama_lapangan']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Keterangan:</label>
                    <textarea name="keterangan" required><?= $data['keterangan']; ?></textarea>
                </div>

                <div class="form-group">
                    <label>Harga Per Jam (Rp):</label>
                    <input type="number" name="harga" value="<?= $data['harga']; ?>" placeholder="Contoh: 150000" required>
                </div>
                
                <div class="btn-container">
                    <button type="submit" name="update" class="btn-update">Simpan Perubahan</button>
                    <a href="datalapangan.php" class="btn-batal">Batal</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>