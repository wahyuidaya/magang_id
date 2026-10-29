<?php
session_start();
include 'koneksi.php';

$query = "SELECT * FROM lowongan WHERE status = 'aktif' ORDER BY dibuat_pada DESC LIMIT 3";
$result = mysqli_query($conn, $query);

$isLoggedIn = isset($_SESSION['user_id']);
$userRole = $_SESSION['user_role'] ?? '';

// Ambil perusahaan
$query2 = "SELECT * FROM pengguna WHERE peran = 'perusahaan' AND status_akun = 'aktif' ORDER BY tanggal_dibuat DESC LIMIT 3";
$result2 = mysqli_query($conn, $query2);
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Magang ID</title>
  <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&amp;display=swap" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
</head>

<body>
  <nav class="navbar">
    <div class="judul-navbar">MAGANG ID</div>

    <!-- Menu Desktop -->
    <div class="menu-dan-tombol">
      <ul class="daftar-menu">
        <li><a href="#Beranda-page">Beranda</a></li>
        <li><a href="#Lowongan_Terbaru-page">Lowongan</a></li>
        <li><a href="#Perusahaan-page">Perusahaan</a></li>
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
        <li><a href="#Beranda-page" onclick="document.getElementById('checkbox-menu').checked = false;">Home</a></li>
        <li><a href="#Lowongan_Terbaru-page"
            onclick="document.getElementById('checkbox-menu').checked = false;">Lowongan</a></li>
        <li><a href="#Perusahaan-page"
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

  <section id="Beranda-page">
    <div class="Beranda-page-text">
      <p class="tagline">PLATFORM MAGANG INDONESIA</p>
      <h1>Temukan Kesempatan Magang Terbaikmu di Sini!</h1>
      <p>Platform ini menghubungkan mahasiswa pencari magang dengan perusahaan penyedia program magang.</p>
      <div class="form-cari-lowongan">
        <a href="#Lowongan_Terbaru-page" class="ikon-cari-dengan-teks">
          <i class="fas fa-search"></i>
          <span>Cari Lowongan</span>
        </a>
      </div>
    </div>
    <div class="Beranda-page-image">
      <img src="assets/home.jpg" alt="Ilustrasi kerja tim" />
    </div>
  </section>

  <section id="Lowongan_Terbaru-page">
    <h1>Lowongan Terbaru</h1>
    <p>Temukan kesempatan magang terbaik sesuai bidangmu.</p>

    <div class="grid-lowongan">
      <?php while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['id'];
        $judul = htmlspecialchars($row['judul_lowongan']);
        $perusahaan = htmlspecialchars($row['nama_perusahaan']);
        $batas = strtotime($row['batas_waktu']);
        $sisa_waktu = $batas > time() ? floor(($batas - time()) / 86400) . " hari" : "Pendaftaran Di Tutup";
        $gambar = !empty($row['gambar']) ? 'uploads/lowongan/' . $row['gambar'] : "assets/default.jpg";
        ?>
        <div class="card-lowongan">
          <img src="<?php echo $gambar; ?>" alt="<?php echo $judul; ?>" class="gambar-lowongan" />
          <div class="info-lowongan">
            <h2 class="judul-lowongan"><?php echo $judul . " di " . $perusahaan; ?></h2>
            <p class="waktu-sisa">
              <?php echo $sisa_waktu == "Pendaftaran Di Tutup" ? "Pendaftaran Di Tutup" : "Sisa Waktu: $sisa_waktu"; ?>
            </p>
          </div>

          <div class="tombol-kontainer">
            <a href="detail_lowongan.php?id=<?php echo urlencode($id); ?>" class="detail-lowongan">
              <button class="tombol tombol-detail" type="button">Detail</button>
            </a>

            <?php if ($sisa_waktu !== "Pendaftaran Di Tutup") { ?>
              <?php if ($isLoggedIn && $userRole === 'mahasiswa') { ?>
                <a href="dashboard_mahasiswa/form_pendaftaran.php?id=<?php echo $row['id']; ?>">
                  <button class="tombol tombol-daftar" type="button">Daftar</button>
                </a>
              <?php } else { ?>
                <a href="login_user/login.php"
                  onclick="return confirm('Anda harus login terlebih dahulu untuk mendaftar. Login sekarang?');">
                  <button class="tombol tombol-daftar" type="button">Daftar</button>
                </a>
              <?php } ?>
            <?php } else { ?>
              <button class="tombol tombol-ditutup" type="button">Lowongan Ditutup</button>
            <?php } ?>
          </div>
        </div>
      <?php } ?>

    </div>
    <div class="lihat-semua">
      <?php if ($isLoggedIn): ?>
        <a href="#Lowongan_Terbaru-page" class="tombol-lebih-banyak">Lihat Semua Lowongan</a>
      <?php else: ?>
        <a href="login_user/login.php" class="tombol-lebih-banyak"
          onclick="return confirm('Anda harus login terlebih dahulu untuk melihat semua lowongan. Login sekarang?');">Lihat
          Semua Lowongan</a>
      <?php endif; ?>
    </div>
  </section>

  <section id="Perusahaan-page">
    <h1>Daftar Perusahaan</h1>
    <p>Berikut adalah perusahaan-perusahaan yang telah bekerja sama dengan Magang ID.</p>

    <div class="grid-perusahaan">
      <?php while ($c = mysqli_fetch_assoc($result2)): ?>
        <?php
        $foto = !empty($c['foto']) ? 'uploads/profil/perusahaan/' . $c['foto'] : 'assets/default-company.jpg';
        $tgl = date('d M Y', strtotime($c['tanggal_dibuat']));
        ?>
        <div class="card-perusahaan">
          <?php if (!empty($c['foto'])): ?>
            <img src="uploads/profil/perusahaan/<?php echo $c['foto']; ?>" alt="<?php echo htmlspecialchars($c['nama']); ?>"
              class="logo-perusahaan" />
          <?php else: ?>
            <div class="logo-perusahaan default-fa">
              <i class="fas fa-building"></i>
            </div>
          <?php endif; ?>

          <div class="info-perusahaan">
            <h2><?php echo htmlspecialchars($c['nama']); ?></h2>
            <p class="alamat"><?php echo htmlspecialchars($c['alamat']); ?></p>
            <p class="stat">Bergabung sejak <?php echo $tgl; ?></p>
          </div>
          <a href="lihat_profil.php?id=<?php echo $c['id']; ?>" class="btn-lihat">Lihat Profil</a>
        </div>
      <?php endwhile; ?>
    </div>

    <div class="lihat-semua">
      <a href="daftar_perusahaan.php" class="tombol-lebih-banyak">Lihat Semua Perusahaan</a>
    </div>
  </section>

  <footer class="footer">
    <div class="footer-container">
      <div class="footer-column">
        <h4>Magang ID</h4>
        <p>Platform yang menghubungkan mahasiswa pencari magang dengan perusahaan penyedia program magang.</p>
        <div class="social-icons">
          <a href="#"><i class="fab fa-facebook-f"></i></a>
          <a href="#"><i class="fab fa-twitter"></i></a>
          <a href="#"><i class="fab fa-instagram"></i></a>
          <a href="#"><i class="fab fa-linkedin-in"></i></a>
          <a href="#"><i class="fab fa-youtube"></i></a>
        </div>
      </div>

      <div class="footer-column">
        <h4>Menu</h4>
        <ul>
          <li><a href="#Beranda-page">Beranda</a></li>
          <li><a href="#Lowongan_Terbaru-page">Lowongan</a></li>
          <li><a href="#Perusahaan-page">Perusahaan</a></li>
        </ul>
      </div>

      <div class="footer-column">
        <h4>Kontak Kami</h4>
        <p><i class="fas fa-map-marker-alt"></i> Kelapa Gading, Jakarta Utara, Indonesia</p>
        <p><i class="fas fa-phone-alt"></i> +6287737658258</p>
        <p><i class="fas fa-envelope"></i> magangid666@gmail.com</p>
      </div>
    </div>

    <div class="footer-bottom">
      <p>© 2025 Magang ID. Seluruh Hak Cipta.</p>
    </div>
  </footer>


</body>

</html>