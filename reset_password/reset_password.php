<?php
session_start();

if (isset($_SESSION['success'])) {
  echo "<script>alert('{$_SESSION['success']}');</script>";
  unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
  echo "<script>alert('{$_SESSION['error']}');</script>";
  unset($_SESSION['error']);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Kata Sandi - Magang ID</title>
  <link rel="stylesheet" href="../style.css?v=<?php echo time(); ?>">
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
        <img src="../assets/home.jpg" alt="Ilustrasi Reset Kata Sandi">
      </div>

      <div class="login-container">
        <h1>Reset Kata Sandi</h1>
        <p class="login-subtitle">Masukkan email Anda untuk mengatur ulang kata sandi</p>

        <form action="proses_reset_password.php" method="post" class="login-form">
          <input type="email" name="email" id="email" placeholder="Email" required class="input-login">
          <button type="submit" class="btn-login">Kirim Link Reset</button>
        </form>

        <p class="login-footer">Sudah ingat kata sandi? <a href="../login_user/login.php">Login di sini</a></p>
      </div>
    </div>
  </section>
</body>

</html>