<?php
session_start();
include '../koneksi.php';

if (isset($_SESSION['user_id'])) {
  if ($_SESSION['user_role'] === 'mahasiswa') {
    header('Location: ../dashboard_mahasiswa/dashboard_mahasiswa.php');
  } elseif ($_SESSION['user_role'] === 'perusahaan') {
    header('Location: ../dashboard_perusahaan/dashboard_perusahaan.php');
  }
  exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email']);
  $kata_sandi = $_POST['kata_sandi'];

  if (empty($email) || empty($kata_sandi)) {
    $error = "Harap isi semua kolom terlebih dahulu.";
  } elseif (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-z]{2,}$/", $email)) {
    $error = "Format email tidak valid. Gunakan format seperti nama@gmail.com";
  } else {
    $query = "SELECT * FROM pengguna WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
      if ($user['status_akun'] !== 'aktif') {
        $error = "Akun belum diaktivasi. Cek email untuk aktivasi.";
      } elseif (!password_verify($kata_sandi, $user['kata_sandi'])) {
        $error = "Kata sandi salah.";
      } else {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['peran'];
        $_SESSION['user_name'] = $user['nama'];

        if ($user['peran'] === 'mahasiswa') {
          header('Location: ../dashboard_mahasiswa/dashboard_mahasiswa.php');
        } elseif ($user['peran'] === 'perusahaan') {
          header('Location: ../dashboard_perusahaan/dashboard_perusahaan.php');
        } else {
          $error = "Peran tidak valid.";
        }
        exit();
      }
    } else {
      $error = "Email tidak terdaftar.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Magang ID</title>
  <link rel="stylesheet" href="../style.css?v=<?php echo time(); ?>">
  <style>
    .demo-banner {
      background: #1e3a5f;
      color: #fff;
      padding: 14px 20px;
      text-align: center;
      font-size: 13.5px;
      line-height: 1.6;
    }
    .demo-banner a.back-link {
      display: inline-block;
      margin-bottom: 8px;
      color: #90cdf4;
      text-decoration: none;
      font-weight: 600;
      font-size: 13px;
    }
    .demo-banner a.back-link:hover { text-decoration: underline; }
    .demo-banner strong { color: #fbd38d; }
    .demo-accounts {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 10px;
      margin-top: 10px;
    }
    .demo-btn {
      background: rgba(255,255,255,0.12);
      border: 1px solid rgba(255,255,255,0.25);
      color: #fff;
      padding: 6px 14px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 12.5px;
      font-weight: 600;
      transition: background .15s;
    }
    .demo-btn:hover { background: rgba(255,255,255,0.22); }
  </style>
</head>

<body>

  <!-- Demo Banner -->
  <div class="demo-banner">
    <a href="https://wahyuidaya.github.io" class="back-link">← Kembali ke Portofolio Wahyu Idaya</a><br>
    <strong>🔐 Demo Akun — Klik untuk mengisi otomatis:</strong>
    <div class="demo-accounts">
      <button class="demo-btn" onclick="fillDemo('mahasiswa@gmail.com','mahasiswa123')">
        👨‍🎓 Mahasiswa
      </button>
      <button class="demo-btn" onclick="fillDemo('codenix@gmail.com','perusahaan123')">
        🏢 Perusahaan
      </button>
    </div>
    <div style="margin-top:8px; font-size:12px; opacity:.8;">
      Admin login terpisah — gunakan tombol "Login sebagai Admin" di bawah
    </div>
  </div>

  <nav class="navbar">
    <div class="judul-navbar">MAGANG ID</div>

    <div class="menu-dan-tombol">
      <ul class="daftar-menu">
        <li><a href="../#Beranda-page">Beranda</a></li>
        <li><a href="../#Lowongan_Terbaru-page">Lowongan</a></li>
        <li><a href="../#Perusahaan-page">Perusahaan</a></li>
      </ul>
      <a href="../register/register.php"><button class="tombol-masuk-daftar">Register</button></a>
    </div>

    <input type="checkbox" id="checkbox-menu" class="checkbox-menu">
    <label for="checkbox-menu" class="label-hamburger">
      <span class="garis-hamburger"></span>
      <span class="garis-hamburger"></span>
      <span class="garis-hamburger"></span>
    </label>

    <div class="menu-mobile">
      <ul class="daftar-menu-mobile">
        <li><a href="../#Beranda-page" onclick="document.getElementById('checkbox-menu').checked = false;">Beranda</a>
        </li>
        <li><a href="../#Lowongan_Terbaru-page"
            onclick="document.getElementById('checkbox-menu').checked = false;">Lowongan</a></li>
        <li><a href="../#Perusahaan-page"
            onclick="document.getElementById('checkbox-menu').checked = false;">Perusahaan</a></li>
      </ul>
      <div style="text-align: center;">
        <a href="../register/register.php" class="tombol-login"
          onclick="document.getElementById('checkbox-menu').checked = false;">Register</a>
      </div>
    </div>

    <label for="checkbox-menu" class="overlay-menu"></label>
  </nav>

  <section class="login-page">
    <div class="login-wrapper">
      <div class="login-image">
        <img src="../assets/home.jpg" alt="Ilustrasi Login">
      </div>

      <div class="login-container">
        <h1>Login Akun Magang ID</h1>
        <p class="login-subtitle">Silakan masuk untuk melanjutkan</p>

        <?php if ($error): ?>
          <script>alert("<?= htmlspecialchars($error) ?>");</script>
        <?php endif; ?>

        <?php if (isset($_GET['pesan'])): ?>
          <script>alert("<?= htmlspecialchars($_GET['pesan']) ?>");</script>
        <?php endif; ?>

        <form action="" method="POST" class="login-form" novalidate>
          <input type="email" name="email" id="email" placeholder="Email" required class="input-login">
          <input type="password" name="kata_sandi" id="kata_sandi" placeholder="Kata Sandi" required
            class="input-login">
          <button type="submit" class="btn-login">Login</button>
        </form>

        <p class="login-footer">Belum punya akun? <a href="../register/register.php">Daftar di sini</a></p>
        <p class="login-footer">Lupa kata sandi? <a href="../reset_password/reset_password.php">Reset di sini</a></p>
        <p class="login-footer">Login sebagai <a href="../login_admin/login_admin.php">Admin</a></p>
      </div>
    </div>
  </section>

  <script>
    function fillDemo(email, pass) {
      document.getElementById('email').value = email;
      document.getElementById('kata_sandi').value = pass;
    }
    document.getElementById('email').addEventListener('keydown', function (event) {
      if (event.key === 'Enter') {
        event.preventDefault();
        document.getElementById('kata_sandi').focus();
      }
    });
  </script>
</body>

</html>
