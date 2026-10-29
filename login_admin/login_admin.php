<?php
session_start();
include '../koneksi.php';

$error = '';
if (isset($_SESSION['error'])) {
  $error = $_SESSION['error'];
  unset($_SESSION['error']);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin - Magang ID</title>
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
    .demo-btn {
      background: rgba(255,255,255,0.12);
      border: 1px solid rgba(255,255,255,0.25);
      color: #fff;
      padding: 6px 14px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 12.5px;
      font-weight: 600;
      margin-top: 10px;
      transition: background .15s;
    }
    .demo-btn:hover { background: rgba(255,255,255,0.22); }
  </style>
</head>

<body>

  <!-- Demo Banner -->
  <div class="demo-banner">
    <a href="https://wahyuidaya.github.io" class="back-link">← Kembali ke Portofolio Wahyu Idaya</a><br>
    <strong>🔐 Demo Admin — Klik untuk mengisi otomatis:</strong><br>
    <button class="demo-btn" onclick="fillDemo('Admin','admin123')">
      👑 Login sebagai Admin
    </button>
  </div>

  <nav class="navbar">
    <div class="judul-navbar">MAGANG ID</div>

    <div class="menu-dan-tombol">
      <ul class="daftar-menu">
        <li><a href="../#Beranda-page">Beranda</a></li>
        <li><a href="../#Lowongan_Terbaru-page">Lowongan</a></li>
        <li><a href="../#Perusahaan-page">Perusahaan</a></li>
      </ul>
      <a href="../login_user/login.php"><button class="tombol-masuk-daftar">User</button></a>
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
        <a href="../login_user/login.php" class="tombol-login"
          onclick="document.getElementById('checkbox-menu').checked = false;">User</a>
      </div>
    </div>

    <label for="checkbox-menu" class="overlay-menu"></label>
  </nav>

  <?php if ($error): ?>
    <script>alert("<?= addslashes($error) ?>");</script>
  <?php endif; ?>

  <section class="login-page">
    <div class="login-wrapper">
      <div class="login-image">
        <img src="../assets/home.jpg" alt="Ilustrasi Login">
      </div>
      <div class="login-container">
        <h1>Login Admin Magang ID</h1>
        <p class="login-subtitle">Silakan masuk untuk melanjutkan</p>
        <form action="proses_login_admin.php" method="POST" class="login-form" novalidate>
          <input name="nama" id="nama" placeholder="Username" required class="input-login">
          <input type="password" name="kata_sandi" id="kata_sandi" placeholder="Kata Sandi" required
            class="input-login">
          <button type="submit" class="btn-login">Login</button>
        </form>
        <p class="login-footer">Login sebagai <a href="../login_user/login.php">Pengguna</a></p>
      </div>
    </div>
  </section>

  <script>
    function fillDemo(username, pass) {
      document.getElementById('nama').value = username;
      document.getElementById('kata_sandi').value = pass;
    }
    document.getElementById('nama').addEventListener('keydown', function (event) {
      if (event.key === 'Enter') {
        event.preventDefault();
        document.getElementById('kata_sandi').focus();
      }
    });
  </script>
</body>

</html>
