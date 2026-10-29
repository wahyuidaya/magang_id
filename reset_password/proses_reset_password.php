<?php
require '../koneksi.php';
require 'kirim_email_reset.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT id FROM pengguna WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $token = bin2hex(random_bytes(16));
        $update = $conn->prepare("UPDATE pengguna SET token_verifikasi = ? WHERE email = ?");
        $update->bind_param("ss", $token, $email);
        $update->execute();

        if (kirimEmailReset($email, $token)) {
            $_SESSION['success'] = 'Link reset telah dikirim ke email Anda.';
            header("Location: reset_password.php");
            exit;
        } else {
            $_SESSION['error'] = 'Gagal mengirim email. Silakan coba lagi.';
            header("Location: reset_password.php");
            exit;
        }
    } else {
        $_SESSION['error'] = 'Email tidak ditemukan.';
        header("Location: reset_password.php");
        exit;
    }
}
?>