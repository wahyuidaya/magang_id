<?php
session_start();
include '../koneksi.php';


// Cek login
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'perusahaan') {
  header('Location: login_user/login_user/login.php');
  exit();
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
  $id = intval($_POST['id']);

  // Validasi milik perusahaan
  $cek = mysqli_query($conn, "SELECT * FROM lowongan WHERE id = $id AND id_pengguna = $userId");
  if (mysqli_num_rows($cek) > 0) {
    $hapus = mysqli_query($conn, "DELETE FROM lowongan WHERE id = $id");
    if ($hapus) {
      header('Location: dashboard_perusahaan/../lowongan.php');
      exit();
    } else {
      echo "Gagal hapus: " . mysqli_error($conn);
    }
  } else {
    echo "Lowongan tidak ditemukan atau bukan milik Anda.";
  }

} else {
  echo "Permintaan tidak valid.";
}
?>