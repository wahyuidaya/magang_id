<?php
session_start();
include '../koneksi.php';

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login_admin/login_admin.php');
    exit();
}

$adminId = $_SESSION['admin_id'];

// Ambil email admin dari database
$sqlAdmin = "SELECT email FROM admin WHERE id = $adminId LIMIT 1";
$resultAdmin = mysqli_query($conn, $sqlAdmin);
$dataAdmin = mysqli_fetch_assoc($resultAdmin);
$emailAdmin = $dataAdmin['email'];

// Cek jika ID lowongan tersedia
if (isset($_GET['id'])) {
    $idLowongan = intval($_GET['id']);

    // Ambil info lowongan dan pemiliknya
    $queryLowongan = "
        SELECT l.judul_lowongan, p.nama, p.email 
        FROM lowongan l
        JOIN pengguna p ON l.id_pengguna = p.id
        WHERE l.id = $idLowongan
        LIMIT 1
    ";
    $resultLowongan = mysqli_query($conn, $queryLowongan);

    if ($row = mysqli_fetch_assoc($resultLowongan)) {
        $judulLowongan = $row['judul_lowongan'];
        $namaPerusahaan = $row['nama'];
        $emailPerusahaan = $row['email'];

        // Hapus lowongan
        $queryDelete = "DELETE FROM lowongan WHERE id = $idLowongan";
        if (mysqli_query($conn, $queryDelete)) {

            // Kirim email notifikasi ke perusahaan
            $to = $emailPerusahaan;
            $subject = "Pemberitahuan Penghapusan Lowongan - Magang ID";
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: Magang ID <{$emailAdmin}>" . "\r\n";

            $message = "
            <html>
            <head><title>Pemberitahuan Penghapusan Lowongan</title></head>
            <body style='font-family: Arial, sans-serif; color: #333;'>
            <p>Halo <strong>$nama_perusahaan</strong>,</p>
            <p>Lowongan berjudul <strong>\"$judul_lowongan\"</strong> yang sebelumnya dipublikasikan melalui <strong>Magang ID</strong> telah <strong>dihapus dari sistem</strong>.</p>
            <p>Penghapusan dilakukan karena pelanggaran terhadap ketentuan penggunaan platform.</p>
            <p>Lowongan ini tidak lagi dapat diakses atau dilamar oleh pengguna.</p>
            <br>
            <p>Terima kasih atas kontribusi Anda di platform Magang ID.</p>
            <p style='font-size: 0.9em; color: #777;'>Email ini dikirim otomatis oleh sistem Magang ID dan tidak dapat dibalas.</p>
            </body>
            </html>
            ";

            require 'kirim_email_hapus.php';
            kirimEmail($emailPerusahaan, $subject, $message, $emailAdmin);


            // Redirect kembali dengan pesan sukses
            header("Location: admin_lowongan.php?pesan=sukses_hapus");
            exit();
        } else {
            echo "Gagal menghapus lowongan.";
        }
    } else {
        echo "Data lowongan tidak ditemukan.";
    }
} else {
    echo "ID lowongan tidak ditemukan.";
}
?>