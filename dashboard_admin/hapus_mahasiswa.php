<?php
session_start();
include '../koneksi.php';

// Pastikan user admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login_admin/login_admin.php');
    exit();
}

// Ambil email admin
$admin_id = $_SESSION['admin_id'];
$query_admin = "SELECT email FROM admin WHERE id = $admin_id LIMIT 1";
$result_admin = mysqli_query($conn, $query_admin);
$admin = mysqli_fetch_assoc($result_admin);
$email_admin = $admin['email'];

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Ambil data mahasiswa sebelum dihapus
    $query_user = "SELECT email, nama FROM pengguna WHERE id = $id AND peran = 'mahasiswa' LIMIT 1";
    $result_user = mysqli_query($conn, $query_user);

    if ($result_user && mysqli_num_rows($result_user) > 0) {
        $user = mysqli_fetch_assoc($result_user);
        $email_mahasiswa = $user['email'];
        $nama_mahasiswa = $user['nama'];

        // Hapus mahasiswa
        $query = "DELETE FROM pengguna WHERE id = $id AND peran = 'mahasiswa'";
        if (mysqli_query($conn, $query)) {

            // Kirim email notifikasi
            $subject = "Pemberitahuan Penghapusan Akun";
            $message = "
            <html>
            <head><title>Pemberitahuan Penghapusan Akun</title></head>
            <body style='font-family: Arial, sans-serif; color: #333;'>
            <p>Halo <strong>$nama_mahasiswa</strong>,</p>
            <p>Email ini dikirim untuk memberitahukan bahwa akun Anda sebagai <strong>mahasiswa</strong> di <strong>Magang ID</strong> telah <strong>dinonaktifkan dan dihapus secara permanen</strong> dari sistem.</p>
            <p>Penghapusan dilakukan karena pelanggaran terhadap ketentuan penggunaan platform.</p>
            <p>Seluruh informasi terkait akun Anda telah dihapus dan tidak dapat dipulihkan.</p>
            <br>
            <p>Terima kasih atas partisipasi Anda selama menggunakan layanan Magang ID.</p>
            <p style='font-size: 0.9em; color: #777;'>Email ini dikirim secara otomatis oleh sistem Magang ID. Harap tidak membalas pesan ini.</p>
            </body>
            </html>
            ";

            // Header email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: Magang ID Admin <$email_admin>" . "\r\n";

            require 'kirim_email_hapus.php';
            kirimEmail($email_mahasiswa, $subject, $message, $email_admin);

            header("Location: admin_mahasiswa.php?pesan=sukses_hapus");
            exit();
        } else {
            echo "Gagal menghapus akun mahasiswa.";
        }
    } else {
        echo "Data mahasiswa tidak ditemukan.";
    }
} else {
    echo "ID mahasiswa tidak ditemukan.";
}
?>