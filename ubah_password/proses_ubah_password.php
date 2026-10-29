<?php
require '../koneksi.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $token = $_POST['token'];
    $passwordBaru = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT id FROM pengguna WHERE email = ? AND token_verifikasi = ?");
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $update = $conn->prepare("UPDATE pengguna SET kata_sandi = ?, token_verifikasi = NULL WHERE email = ?");
        $update->bind_param("ss", $passwordBaru, $email);
        $update->execute();

        echo "<script>alert('Kata sandi berhasil diubah. Silakan login.'); window.location='../login_user/login.php';</script>";
    } else {
        echo "<script>alert('Link reset tidak valid.'); window.location='../login_user/login.php';</script>";
    }
}
?>