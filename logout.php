<?php
session_start();
session_unset();
session_destroy();
echo "<script>
    alert('Logout Berhasil! Balik ke halaman login.');
    window.location.href = 'login.php'; 
</script>";
exit;
?>