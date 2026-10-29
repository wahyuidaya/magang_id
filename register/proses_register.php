<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include '../koneksi.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $no_telepon = trim($_POST['no_telepon']);
    $alamat = trim($_POST['alamat']);
    $deskripsi_perusahaan = isset($_POST['deskripsi_perusahaan']) ? trim($_POST['deskripsi_perusahaan']) : null;
    $peran = trim($_POST['peran']);
    $jenis_kelamin = isset($_POST['jenis_kelamin']) ? trim($_POST['jenis_kelamin']) : '';
    $kata_sandi = $_POST['kata_sandi'];
    $konfirmasi_kata_sandi = $_POST['konfirmasi_kata_sandi'];

    // Validasi dasar
    if (empty($nama) || empty($email) || empty($no_telepon) || empty($alamat) || empty($peran) || empty($kata_sandi) || empty($konfirmasi_kata_sandi)) {
        echo "<script>alert('Semua kolom wajib diisi.'); window.history.back();</script>";
        exit();
    }

    if ($peran !== 'perusahaan' && empty($jenis_kelamin)) {
        echo "<script>alert('Jenis kelamin wajib diisi.'); window.history.back();</script>";
        exit();
    }

    if ($peran === 'perusahaan' && empty($deskripsi_perusahaan)) {
        echo "<script>alert('Deskripsi perusahaan wajib diisi.'); window.history.back();</script>";
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Format email tidak valid.'); window.history.back();</script>";
        exit();
    }

    if (stripos($email, '@gmail.com') === false) {
        echo "<script>alert('Harus menggunakan email @gmail.com.'); window.history.back();</script>";
        exit();
    }

    // Cek email sudah terdaftar
    $cek = $conn->prepare("SELECT id FROM pengguna WHERE email = ?");
    $cek->bind_param("s", $email);
    $cek->execute();
    $cek->store_result();
    if ($cek->num_rows > 0) {
        echo "<script>alert('Email sudah terdaftar.'); window.history.back();</script>";
        exit();
    }

    // Validasi nomor dan password
    if (!preg_match('/^[0-9]{10,15}$/', $no_telepon)) {
        echo "<script>alert('Nomor telepon harus 10-15 digit.'); window.history.back();</script>";
        exit();
    }

    if (strlen($kata_sandi) < 8) {
        echo "<script>alert('Kata sandi minimal 8 karakter.'); window.history.back();</script>";
        exit();
    }

    if ($kata_sandi !== $konfirmasi_kata_sandi) {
        echo "<script>alert('Konfirmasi kata sandi tidak cocok.'); window.history.back();</script>";
        exit();
    }

    // Simpan ke database
    $token = bin2hex(random_bytes(32));
    $hash = password_hash($kata_sandi, PASSWORD_DEFAULT);
    $jkValue = $peran !== 'perusahaan' ? $jenis_kelamin : null;

    $stmt = $conn->prepare("INSERT INTO pengguna (nama, email, no_telepon, alamat, jenis_kelamin, peran, kata_sandi, status_akun, token_verifikasi, tanggal_dibuat, deskripsi_perusahaan)
                            VALUES (?, ?, ?, ?, ?, ?, ?, 'belum_aktif', ?, NOW(), ?)");
    $stmt->bind_param("sssssssss", $nama, $email, $no_telepon, $alamat, $jkValue, $peran, $hash, $token, $deskripsi_perusahaan);

    if ($stmt->execute()) {
        // Kirim email aktivasi dengan PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'magangid666@gmail.com';
            $mail->Password = 'vvyh kfiz jsfd twuy';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('magangid666@gmail.com', 'Magang ID');
            $mail->addAddress($email, $nama);
            $mail->isHTML(true);

            // BUAT LINK AKTIVASI
            $activationLink = "https://magangid.my.id/aktivasi.php?email=" . urlencode($email) . "&token=" . urlencode($token);

            $mail->Subject = 'Aktivasi Akun Magang ID';
            $mail->Body = "Halo <b>$nama</b>,<br><br>
                              Terima kasih telah mendaftar di Magang ID.<br>
                              Klik link berikut untuk mengaktifkan akun kamu:<br><br>
                              <a href='$activationLink'>$activationLink</a><br><br>
                              Jika kamu tidak merasa mendaftar, abaikan email ini.<br><br>
                              Salam,<br>Tim Magang ID";

            $mail->send();
            echo "<script>
                alert('Pendaftaran berhasil! Silakan cek email kamu untuk aktivasi.');
                window.location.href = '../login_user/login.php';
            </script>";
        } catch (Exception $e) {
            echo "<script>alert('Pendaftaran berhasil, tapi gagal mengirim email: {$mail->ErrorInfo}'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Terjadi kesalahan saat menyimpan data.'); window.history.back();</script>";
    }
}
?>