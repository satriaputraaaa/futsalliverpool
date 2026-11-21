<?php 
session_start();
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
    <title>Tambah Lapangan - Liverpool GOR</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 0; 
            background-color: #f4f7f6; 
            display: flex; 
        }

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
        .sidebar h2 { text-align: center; font-size: 18px; color: #d35400; margin-bottom: 30px; }
        .sidebar a { 
            display: block; 
            color: white; 
            padding: 15px 20px; 
            text-decoration: none; 
            border-bottom: 1px solid #34495e; 
            transition: 0.3s; 
        }
        .sidebar a:hover { background-color: #e67e22; padding-left: 25px; }

        .main-content { 
            margin-left: 250px; 
            padding: 40px; 
            width: 100%; 
            display: flex;
            justify-content: center;
        }

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
            margin-bottom: 20px;
        }

        .form-group { margin-bottom: 20px; }
        label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: 600; 
            color: #34495e; 
        }
        input[type="text"], textarea { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            box-sizing: border-box; 
            font-size: 15px;
            background-color: #f9f9f9;
            transition: 0.3s;
        }
        input:focus, textarea:focus {
            border-color: #e67e22;
            background-color: #fff;
            outline: none;
            box-shadow: 0 0 8px rgba(230, 126, 34, 0.2);
        }
        textarea { height: 120px; resize: none; }

        .btn-container { display: flex; gap: 10px; margin-top: 10px; }
        
        .btn-simpan { 
            background-color: #e67e22; 
            color: #ffffff !important; 
            border: none; 
            padding: 12px 25px; 
            border-radius: 8px; 
            cursor: pointer; 
            font-weight: bold; 
            font-size: 15px;
            flex: 2;
            transition: 0.3s;
            text-align: center;
        }
        .btn-simpan:hover { background-color: #d35400; transform: translateY(-2px); }
        
        /* TOMBOL BATAL */
        .btn-batal { 
            background-color: #c0392b;
            color: #ffffff; 
            text-decoration: none; 
            padding: 12px; 
            border-radius: 8px; 
            text-align: center;
            font-size: 15px;
            flex: 1;
            font-weight: bold;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-batal:hover { background-color: #a93226; transform: translateY(-2px); }

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
            <h2>Tambah Lapangan Baru</h2>
            
            <form action="prosestambahlapangan.php" method="POST">
                <div class="form-group">
                    <label>Jenis/Nama Lapangan:</label>
                    <input type="text" name="nama_lapangan" placeholder="Contoh: Lapangan Vinyl A" required>
                </div>
                
                <div class="form-group">
                    <label>Keterangan:</label>
                    <textarea name="keterangan" placeholder="Contoh: Cocok untuk 5v5, alas vinyl standar nasional..." required></textarea>
                </div>
                
                <div class="btn-container">
                    <button type="submit" name="submit" class="btn-simpan">Simpan Lapangan</button>
                    <a href="datalapangan.php" class="btn-batal">Batal</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>