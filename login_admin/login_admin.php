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
    window.addEventListener('DOMContentLoaded', function () {
      const p = new URLSearchParams(window.location.search);
      if (p.get('demo') === 'admin') fillDemo('Admin', 'admin123');
    });
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

<style>
.pw-eye-wrap{position:relative;width:100%;display:block;}
.pw-eye-wrap input{width:100% !important;padding-right:42px !important;box-sizing:border-box !important;}
.pw-eye-btn{position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:4px;display:flex;align-items:center;justify-content:center;color:#64748b;z-index:5;}
.pw-eye-btn:hover{color:#1e293b;}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var eyeOpen = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
  var eyeClosed = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>';
  document.querySelectorAll('input[type="password"]').forEach(function(inp) {
    if (inp.closest('.pw-eye-wrap')) return;
    var wrap = document.createElement('div');
    wrap.className = 'pw-eye-wrap';
    inp.parentNode.insertBefore(wrap, inp);
    wrap.appendChild(inp);
    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'pw-eye-btn';
    btn.setAttribute('aria-label', 'Tampilkan/Sembunyikan Password');
    btn.innerHTML = eyeOpen;
    btn.addEventListener('click', function() {
      var isPw = inp.type === 'password';
      inp.type = isPw ? 'text' : 'password';
      btn.innerHTML = isPw ? eyeClosed : eyeOpen;
    });
    wrap.appendChild(btn);
  });
});
</script>

</body>

</html>
