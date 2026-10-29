<?php
include 'koneksi.php'; // pastikan koneksi.php sudah benar dan ada koneksi $conn

// Ambil parameter dari URL
$email = $_GET['email'] ?? '';
$token = $_GET['token'] ?? '';

// Cek apakah email dan token tersedia
if (empty($email) || empty($token)) {
    echo "<script>alert('Link aktivasi tidak valid.'); window.location.href='login_user/login.php';</script>";
    exit;
}

// Gunakan prepared statement untuk mencegah SQL Injection
$stmt = $conn->prepare("SELECT * FROM pengguna WHERE email = ? AND token_verifikasi = ? AND status_akun = 'belum_aktif' LIMIT 1");
$stmt->bind_param("ss", $email, $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // Update status akun menjadi aktif
    $updateStmt = $conn->prepare("UPDATE pengguna SET status_akun = 'aktif', token_verifikasi = NULL WHERE email = ?");
    $updateStmt->bind_param("s", $email);
    $updateStmt->execute();

    echo "<script>alert('Akun berhasil diaktifkan! Silakan login.'); window.location.href='login_user/login.php';</script>";
} else {
    echo "<script>alert('Link aktivasi tidak valid atau akun sudah aktif.'); window.location.href='login_user/login.php';</script>";
}
?>