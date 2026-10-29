<?php
session_start();
include '../koneksi.php';

$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register - Magang ID</title>
  <link rel="stylesheet" href="../style.css?v=<?php echo time(); ?>">
</head>

<body>
  <script>
    // Alert atas halaman jika ada notifikasi
    window.addEventListener('DOMContentLoaded', () => {
      const success = <?= json_encode($success) ?>;
      const error = <?= json_encode($error) ?>;
      if (success) alert(success);
      if (error) alert(error);
    });
  </script>

  <nav class="navbar">
    <div class="judul-navbar">MAGANG ID</div>
    <div class="menu-dan-tombol">
      <ul class="daftar-menu">
        <li><a href="../#Beranda-page">Beranda</a></li>
        <li><a href="../#Lowongan_Terbaru-page">Lowongan</a></li>
        <li><a href="../#Perusahaan-page">Perusahaan</a></li>
      </ul>
      <a href="../login_user/login.php"><button class="tombol-masuk-daftar">Login</button></a>
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
          onclick="document.getElementById('checkbox-menu').checked = false;">Login</a>
      </div>
    </div>
    <label for="checkbox-menu" class="overlay-menu"></label>
  </nav>

  <section class="register-page">
    <div class="register-container">
      <h1>Daftar Akun Magang ID</h1>
      <p class="register-subtitle">Silakan isi data dengan benar</p>

      <form class="register-form" action="proses_register.php" method="POST" id="form-register" novalidate>
        <input name="nama" id="nama" placeholder="Nama" required class="input-register">
        <input type="email" name="email" id="email" placeholder="Email" required class="input-register">
        <input name="no_telepon" id="no_telepon" placeholder="Nomor Telepon" required pattern="[0-9]{10,15}"
          title="Nomor telepon harus angka 10-15 digit" class="input-register">
        <textarea name="alamat" id="alamat" placeholder="Alamat Lengkap" rows="3" required
          class="input-register"></textarea>

        <div class="select-group">
          <select name="peran" id="peran" required class="input-register">
            <option value="" disabled selected>Peran</option>
            <option value="mahasiswa">Mahasiswa</option>
            <option value="perusahaan">Perusahaan</option>
          </select>
        </div>

        <!-- Deskripsi hanya muncul jika perusahaan -->
        <textarea name="deskripsi_perusahaan" id="deskripsi_perusahaan" placeholder="Deskripsi Perusahaan" rows="4"
          class="input-register" style="display: none;"></textarea>

        <!-- Jenis kelamin hanya untuk mahasiswa -->
        <div class="select-group" id="group-jenis-kelamin">
          <select name="jenis_kelamin" id="jenis_kelamin" class="input-register">
            <option value="" disabled selected>Jenis Kelamin</option>
            <option value="Laki-laki">Laki-laki</option>
            <option value="Perempuan">Perempuan</option>
          </select>
        </div>

        <input type="password" name="kata_sandi" id="kata_sandi" placeholder="Kata Sandi (min 8 karakter)" required
          minlength="8" class="input-register">
        <input type="password" name="konfirmasi_kata_sandi" id="konfirmasi_kata_sandi"
          placeholder="Konfirmasi Kata Sandi" required class="input-register">

        <button type="submit" class="btn-register">Daftar</button>
      </form>

      <p class="register-footer">Sudah punya akun? <a href="../login_user/login.php">Login di sini</a></p>
    </div>
  </section>

  <script>
    const inputs = ['nama', 'email', 'no_telepon', 'alamat', 'peran', 'jenis_kelamin', 'kata_sandi', 'konfirmasi_kata_sandi'];

    inputs.forEach((id, index) => {
      const el = document.getElementById(id);
      if (el) {
        el.addEventListener('keydown', function (e) {
          if (e.key === 'Enter') {
            e.preventDefault();
            let nextIndex = index + 1;

            // Cari field berikutnya yang tidak hidden (display !== 'none')
            while (nextIndex < inputs.length) {
              const nextId = inputs[nextIndex];
              const nextEl = document.getElementById(nextId);

              if (nextEl && nextEl.offsetParent !== null) { // offsetParent === null artinya hidden
                nextEl.focus();
                return;
              }

              nextIndex++;
            }

            // Jika tidak ada field berikutnya yang aktif, submit form
            document.getElementById('form-register').submit();
          }
        });
      }
    });

    const peran = document.getElementById("peran");
    const deskripsi_perusahaan = document.getElementById("deskripsi_perusahaan");
    const jenisKelamin = document.getElementById("group-jenis-kelamin");
    const kataSandi = document.getElementById("kata_sandi");

    peran.addEventListener("change", function () {
      if (this.value === "perusahaan") {
        deskripsi_perusahaan.style.display = "block";
        deskripsi_perusahaan.required = true;
        jenisKelamin.style.display = "none";
        document.getElementById("jenis_kelamin").required = false;
      } else {
        deskripsi_perusahaan.style.display = "none";
        deskripsi_perusahaan.required = false;
        jenisKelamin.style.display = "block";
        document.getElementById("jenis_kelamin").required = true;
      }
    });

    // Saat enter di kolom "peran", langsung ke "deskripsi" atau "kata sandi"
    peran.addEventListener("keydown", function (e) {
      if (e.key === "Enter") {
        e.preventDefault();
        if (this.value === "perusahaan") {
          deskripsi_perusahaan.focus();
        } else {
          document.getElementById("jenis_kelamin").focus();
        }
        return;
      }
    });

    // Saat enter di textarea deskripsi, pindah ke kata sandi
    deskripsi_perusahaan.addEventListener("keydown", function (e) {
      if (e.key === "Enter" && !e.shiftKey) {
        e.preventDefault();
        kataSandi.focus();
      }
    });
  </script>
</body>

</html>