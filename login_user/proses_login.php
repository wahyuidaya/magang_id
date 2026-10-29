<?php
include '../koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['kata_sandi']);

    // Cek input kosong
    if (empty($email) || empty($password)) {
        header("Location: ../login_user/login.php?pesan=Harap isi semua kolom terlebih dahulu!");
        exit();
    }

    // Tidak ada lagi filter_var di sini
    $email = mysqli_real_escape_string($conn, $email);

    $sql = "SELECT * FROM pengguna WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // Cek aktivasi akun
        if ($row['status_akun'] !== 'aktif') {
            header("Location: ../login_user/login.php?pesan=Akun belum diaktivasi! Silakan cek email Anda.");
            exit();
        }

        // Verifikasi password
        if (password_verify($password, $row['kata_sandi'])) {
            session_start();
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['nama'];
            $_SESSION['user_email'] = $row['email'];
            $_SESSION['user_role'] = $row['peran'];

            // Arahkan ke dashboard sesuai peran
            if ($row['peran'] === 'mahasiswa') {
                header('Location: ../dashboard_mahasiswa/dashboard_mahasiswa.php');
                exit();
            } elseif ($row['peran'] === 'perusahaan') {
                header('Location: ../dashboard_perusahaan/dashboard_perusahaan.php');
                exit();
            }
        } else {
            header("Location: ../login_user/login.php?pesan=Kata sandi salah!");
            exit();
        }
    } else {
        header("Location: ../login_user/login.php?pesan=Email tidak terdaftar!");
        exit();
    }
}
?>
