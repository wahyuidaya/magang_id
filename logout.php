<?php
session_start();
session_destroy(); // Hapus semua data sesi
header('Location: index.php'); // Arahkan ke halaman utama
exit();
?>