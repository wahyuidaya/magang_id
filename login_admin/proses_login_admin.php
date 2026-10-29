<?php
session_start();
include '../koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama = trim($_POST['nama']);
  $kata_sandi = $_POST['kata_sandi'];

  // Validasi input kosong
  if (empty($nama) || empty($kata_sandi)) {
    $error = "Harap isi semua kolom terlebih dahulu.";
  } else {
    // Gunakan prepared statement
    $stmt = $conn->prepare("SELECT * FROM admin WHERE nama = ? LIMIT 1");
    $stmt->bind_param("s", $nama);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin) {
      if (password_verify($kata_sandi, $admin['kata_sandi'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['nama'];
        header('Location: ../dashboard_admin/dashboard_admin.php');
        exit();
      } else {
        $error = "Kata sandi salah.";
      }
    } else {
      $error = "Username salah.";
    }
  }
}

// Redirect dengan notifikasi jika ada error
if ($error) {
  $_SESSION['error'] = $error;
  header('Location: login_admin.php');
  exit();
}
