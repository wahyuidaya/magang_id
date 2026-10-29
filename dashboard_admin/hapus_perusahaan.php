<?php
session_start();
include '../koneksi.php';

// Cek login admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login_admin/login_admin.php');
    exit();
}

$adminId = $_SESSION['admin_id'];

// Ambil email admin dari database
$sqlAdmin = "SELECT email FROM admin WHERE id = $adminId LIMIT 1";
$resultAdmin = mysqli_query($conn, $sqlAdmin);
$dataAdmin = mysqli_fetch_assoc($resultAdmin);
$emailAdmin = $dataAdmin['email']; // email pengirim & untuk dicantumkan

// Hapus perusahaan
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Ambil data perusahaan (untuk kirim email)
    $queryPerusahaan = "SELECT nama, email FROM pengguna WHERE id = $id AND peran = 'perusahaan'";
    $resultPerusahaan = mysqli_query($conn, $queryPerusahaan);
    $perusahaan = mysqli_fetch_assoc($resultPerusahaan);
    $namaPerusahaan = $perusahaan['nama'];
    $emailPerusahaan = $perusahaan['email'];

    // Hapus lowongan terkait terlebih dahulu
    mysqli_query($conn, "DELETE FROM lowongan WHERE id_pengguna = $id");

    // Hapus perusahaan
    mysqli_query($conn, "DELETE FROM pengguna WHERE id = $id AND peran = 'perusahaan'");

    // Kirim email notifikasi
    $to = $emailPerusahaan;
    $subject = "Pemberitahuan Penghapusan Akun - Magang ID";
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Magang ID <{$emailAdmin}>" . "\r\n";

    $message = "
    <html>
    <head><title>Pemberitahuan Penghapusan Akun</title></head>
    <body style='font-family: Arial, sans-serif; color: #333;'>
    <p>Halo <strong>$nama_perusahaan</strong>,</p>
    <p>Kami informasikan bahwa akun perusahaan Anda di <strong>Magang ID</strong> telah <strong>diarsipkan dan dihapus</strong> dari sistem.</p>
    <p>Penghapusan dilakukan karena pelanggaran terhadap ketentuan penggunaan platform.</p>
    <p>Data perusahaan yang terhubung dengan akun ini telah dinonaktifkan sepenuhnya.</p>
    <br>
    <p>Terima kasih atas kerja sama Anda selama menggunakan layanan Magang ID.</p>
    <p style='font-size: 0.9em; color: #777;'>Email ini dikirim secara otomatis oleh sistem Magang ID dan tidak dapat dibalas.</p>
    </body>
    </html>
    ";

    require 'kirim_email_hapus.php';
    kirimEmail($emailPerusahaan, $subject, $message, $emailAdmin);

    header("Location: admin_perusahaan.php?pesan=sukses_hapus");
    exit();
}
?>