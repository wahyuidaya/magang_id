<?php
session_start();
include 'koneksi.php';

// Ambil semua perusahaan aktif
$query = "SELECT * FROM pengguna WHERE peran = 'perusahaan' AND status_akun = 'aktif' ORDER BY tanggal_dibuat DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Daftar Perusahaan - Magang ID</title>
  <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&amp;display=swap" rel="stylesheet" />
</head>

<body>
  <nav class="navbar">
    <div class="judul-navbar">MAGANG ID</div>

    <!-- Menu Desktop -->
    <div class="menu-dan-tombol">
      <ul class="daftar-menu">
        <li><a href="index.php#Beranda-page">Beranda</a></li>
        <li><a href="index.php#Lowongan_Terbaru-page">Lowongan</a></li>
        <li><a href="index.php#Perusahaan-page">Perusahaan</a></li>
      </ul>
      <a href="login_user/login.php"><button class="tombol-masuk-daftar">Login</button></a>
    </div>

    <!-- Checkbox tersembunyi untuk toggle menu -->
    <input type="checkbox" id="checkbox-menu" class="checkbox-menu">

    <!-- Label hamburger yang berfungsi sebagai tombol -->
    <label for="checkbox-menu" class="label-hamburger">
      <span class="garis-hamburger"></span>
      <span class="garis-hamburger"></span>
      <span class="garis-hamburger"></span>
    </label>

    <!-- Menu Mobile -->
    <div class="menu-mobile">
      <ul class="daftar-menu-mobile">
        <li><a href="index.php#Beranda-page"
            onclick="document.getElementById('checkbox-menu').checked = false;">Home</a></li>
        <li><a href="index.php#Lowongan_Terbaru-page"
            onclick="document.getElementById('checkbox-menu').checked = false;">Lowongan</a></li>
        <li><a href="index.php#Perusahaan-page"
            onclick="document.getElementById('checkbox-menu').checked = false;">Perusahaan</a></li>
      </ul>
      <div style="text-align: center;">
        <a href="login_user/login.php" class="tombol-login"
          onclick="document.getElementById('checkbox-menu').checked = false;">Login</a>
      </div>
    </div>

    <!-- Overlay untuk menutup menu ketika diklik di luar -->
    <label for="checkbox-menu" class="overlay-menu"></label>
  </nav>
  <section id="daftar-perusahaan-page">
    <h1>Daftar Perusahaan</h1>
    <p>Berikut adalah perusahaan-perusahaan yang telah bekerja sama dengan Magang ID.</p>

    <div class="grid-daftar-perusahaan">
      <?php while ($c = mysqli_fetch_assoc($result)): ?>
        <?php
        $foto = !empty($c['foto']) ? 'uploads/profil/perusahaan/' . $c['foto'] : 'assets/default-company.jpg';
        $tgl = date('d M Y', strtotime($c['tanggal_dibuat']));
        ?>
        <div class="card-daftar-perusahaan">
          <?php if (!empty($c['foto'])): ?>
            <img src="<?php echo $foto; ?>" alt="<?php echo htmlspecialchars($c['nama']); ?>" class="logo-perusahaan" />
          <?php else: ?>
            <div class="logo-daftar-perusahaan default-fa">
              <i class="fas fa-building"></i>
            </div>
          <?php endif; ?>

          <div class="info-daftar-perusahaan">
            <h2><?php echo htmlspecialchars($c['nama']); ?></h2>
            <p class="alamat"><?php echo htmlspecialchars($c['alamat']); ?></p>
            <p class="stat">Bergabung sejak <?php echo $tgl; ?></p>
          </div>
          <a href="lihat_profil.php?id=<?php echo $c['id']; ?>" class="btn-lihat">Lihat Profil</a>
        </div>
      <?php endwhile; ?>
    </div>
  </section>
</body>

</html>