<?php
require '../koneksi.php';

$email = $_GET['email'] ?? '';
$token = $_GET['token'] ?? '';

$stmt = $conn->prepare("SELECT id FROM pengguna WHERE email = ? AND token_verifikasi = ?");
$stmt->bind_param("ss", $email, $token);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows !== 1) {
  echo "<!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Redirect</title>
        <script>
            alert('Link tidak valid atau sudah kadaluarsa.');
            window.location.href = '../reset_password/reset_password.php';
        </script>
    </head>
    <body></body>
    </html>";
  exit;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ubah Kata Sandi - Magang ID</title>
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
        <img src="../assets/home.jpg" alt="Ilustrasi Ubah Password">
      </div>

      <div class="login-container">
        <h1>Ubah Kata Sandi</h1>
        <p class="login-subtitle">Masukkan kata sandi baru</p>

        <form action="proses_ubah_password.php" method="post" class="login-form" onsubmit="return validatePasswords()">
          <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
          <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

          <input type="password" id="password" name="password" placeholder="Kata Sandi Baru" required minlength="8"
            class="input-login">
          <input type="password" id="confirm_password" name="confirm_password" placeholder="Konfirmasi Kata Sandi Baru"
            required minlength="8" class="input-login">

          <button type="submit" class="btn-login">Simpan Kata Sandi</button>
        </form>

        <p class="login-footer">Ingat kata sandi? <a href="../login_user/login.php">Login di sini</a>
      </div>
    </div>
  </section>

  <script>
    function validatePasswords() {
      const pass = document.getElementById('password').value;
      const confirm = document.getElementById('confirm_password').value;

      if (pass !== confirm) {
        alert("Konfirmasi kata sandi tidak cocok");
        return false;
      }
      return true;
    }

    document.getElementById('password').addEventListener('keydown', function (event) {
      if (event.key === 'Enter') {
        event.preventDefault();
        document.getElementById('confirm_password').focus();
      }
    });
  </script>
</body>

</html>