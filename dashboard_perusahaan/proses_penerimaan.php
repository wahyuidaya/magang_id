<?php
session_start();
include '../koneksi.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'perusahaan') {
  header('Location: login_user/login_user/login.php');
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_pendaftaran = mysqli_real_escape_string($conn, $_POST['id_pendaftaran']);
  $aksi = mysqli_real_escape_string($conn, $_POST['aksi']);

  if (!ctype_digit($id_pendaftaran)) {
    header("Location: {$_SERVER['HTTP_REFERER']}");
    exit();
  }

  $cek = mysqli_query($conn, "SELECT status_lamaran FROM pendaftar WHERE id = '$id_pendaftaran'");
  $data = mysqli_fetch_assoc($cek);
  if ($data && $data['status_lamaran'] === 'diterima' && $aksi === 'terima') {
    header("Location: {$_SERVER['HTTP_REFERER']}");
    exit();
  }

  if ($aksi !== 'terima') {
    die('Aksi tidak valid.');
  }
  $status = 'diterima';

  $update = "
    UPDATE pendaftar
    SET status_lamaran = '$status',
        tanggal_diterima = " . ($status === 'diterima' ? "CURDATE()" : "NULL") . "
    WHERE id = '$id_pendaftaran'
  ";

  if (mysqli_query($conn, $update)) {
    if ($status === 'diterima') {
      $sql = "
        SELECT 
          mhs.email, 
          mhs.nama AS nama_mahasiswa, 
          lowongan.judul_lowongan, 
          perusahaan.nama AS nama_perusahaan,
          perusahaan.alamat AS alamat_perusahaan,
          perusahaan.no_telepon AS telepon_perusahaan
        FROM pendaftar
        JOIN pengguna AS mhs ON pendaftar.id_pengguna = mhs.id
        JOIN lowongan ON pendaftar.id_lowongan = lowongan.id
        JOIN pengguna AS perusahaan ON lowongan.id_pengguna = perusahaan.id
        WHERE pendaftar.id = '$id_pendaftaran'
        LIMIT 1
      ";
      $result = mysqli_query($conn, $sql);

      if ($row = mysqli_fetch_assoc($result)) {
        $to = $row['email'];
        $nama = $row['nama_mahasiswa'];
        $judulLowongan = $row['judul_lowongan'];
        $alamatKantor = $row['alamat_perusahaan'] ?? 'Segera update alamat kantor di profil perusahaan Anda.';
        $teleponHR = $row['telepon_perusahaan'] ?? 'Segera lengkapi nomor HR di profil.';

        $subject = "Selamat! Lamaran Kamu Diterima";

        $message = "Halo $nama,\n\n";
        $message .= "Selamat! Lamaran kamu untuk posisi '$judulLowongan' telah DITERIMA.\n\n";

        $message .= "Silakan datang ke kantor kami untuk melanjutkan proses magang.\n";
        $message .= "Kehadiran dapat dilakukan pada jam kerja:\n";
        $message .= "Senin–Jumat, pukul 08:00–16:00 WIB.\n\n";

        $message .= "Alamat Kantor:\n";
        $message .= "$alamatKantor\n\n";

        $message .= "Jangan lupa membawa dokumen berikut:\n";
        $message .= "- Kartu Pendaftaran (dapat dicetak melalui akun MAGANG ID)\n";
        $message .= "- CV terbaru\n";
        $message .= "- Fotokopi KTP\n\n";

        $message .= "Jika ada pertanyaan, silakan hubungi HR di:\n";
        $message .= "$teleponHR\n\n";

        $message .= "Terima kasih,\n";
        $message .= "Tim MAGANG ID";

        // Ganti mail() dengan PHPMailer
        $mail = new PHPMailer(true);
        try {
          $mail->isSMTP();
          $mail->Host = 'smtp.gmail.com';
          $mail->SMTPAuth = true;
          $mail->Username = 'magangid666@gmail.com';
          $mail->Password = 'vvyh kfiz jsfd twuy';
          $mail->SMTPSecure = 'tls';
          $mail->Port = 587;

          $mail->setFrom('magangid666@gmail.com', 'MAGANG ID'); // GANTI
          $mail->addAddress($to, $nama);

          $mail->isHTML(false); // biar tetap format plain text
          $mail->Subject = $subject;
          $mail->Body = $message;

          $mail->send();
        } catch (Exception $e) {
          error_log("Gagal kirim email ke $to. Error: " . $mail->ErrorInfo);
        }
      }
    }

    header("Location: {$_SERVER['HTTP_REFERER']}");
    exit();
  } else {
    echo "Gagal memperbarui status: " . mysqli_error($conn);
  }

} else {
  echo "Metode tidak diizinkan.";
}
