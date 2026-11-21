<?php
include 'koneksi.php'; 
session_start();

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query  = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        
        // Kunci session harus sama dengan yang dicek di dashboard
        $_SESSION['admin'] = $data['username']; 

        echo "<script>
                alert('Login Berhasil!');
                window.location.href = 'dashboard.php';
              </script>";
        exit(); 
    } else {
        echo "<script>
                alert('Username atau Password Salah!');
                window.location.href = 'login.php';
              </script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Liverpool Futsal</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #2c3e50; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: white; padding: 40px; border-radius: 12px; width: 350px; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
        img { width: 100px; margin-bottom: 20px; }
        h2 { margin-bottom: 25px; color: #333; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; background: #f9f9f9; }
        button { width: 100%; padding: 12px; background: #e74c3c; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 16px; margin-top: 10px; transition: 0.3s; }
        button:hover { background: #c0392b; }
        .footer-text { margin-top: 30px; color: #7f8c8d; font-size: 14px; font-weight: 500; }
    </style>
</head>
<body>

<div class="login-box">
    <img src="image/logo.png" alt="Logo" onerror="this.src='https://via.placeholder.com/100?text=Logo'">
    <h2>Admin Login</h2>
    
    <form action="" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">LOGIN</button>
    </form>

    <div class="footer-text">
        Gor Liverpool - Margonda Depok
    </div>
</div>

</body>
</html>