<?php
session_start();
include '../koneksi.php';

// Pastikan pengguna sudah login dan memiliki role perusahaan
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'perusahaan') {
  header('Location: login_user/login.php');
  exit();
}

$userId = $_SESSION['user_id'];

// Ambil informasi pengguna terbaru
$sqlUser = "SELECT nama, foto FROM pengguna WHERE id = $userId LIMIT 1";
$resultUser = mysqli_query($conn, $sqlUser);
$user = mysqli_fetch_assoc($resultUser);

// === Ambil jumlah tampil ===
$jumlahTampil = 10; // Default
if (isset($_GET['jumlah_tampil'])) {
  $jumlahTampil = intval($_GET['jumlah_tampil']);
  if (!in_array($jumlahTampil, [10, 25, 50, 100])) {
    $jumlahTampil = 10;
  }
}

// === Ambil kata kunci cari ===
// === Ambil kata kunci cari ===
$cari = '';
$whereCari = '';
if (isset($_GET['cari'])) {
  $cari = mysqli_real_escape_string($conn, $_GET['cari']);
  if (!empty($cari)) {
    // Cek apakah pencarian adalah kode pendaftaran
    $sqlCheck = "SELECT id_lowongan FROM pendaftar WHERE kode_pendaftaran = '$cari'";
    $resultCheck = mysqli_query($conn, $sqlCheck);
    if (mysqli_num_rows($resultCheck) > 0) {
      $rowCheck = mysqli_fetch_assoc($resultCheck);
      $id_lowongan = $rowCheck['id_lowongan'];
      header("Location: dashboard_perusahaan/../lihat_pendaftar.php?id=$id_lowongan&cari=$cari");
      exit();
    } else {
      // Jika tidak ditemukan, cari berdasarkan judul lowongan
      $whereCari = " AND judul_lowongan LIKE '%$cari%'";
    }
  }
}

// === Hitung total pendaftar berdasarkan lowongan ===
$sqlCount = "SELECT COUNT(*) AS total FROM lowongan 
              WHERE id_pengguna = $userId $whereCari";
$resultCount = mysqli_query($conn, $sqlCount);
$rowCount = mysqli_fetch_assoc($resultCount);
$totalData = $rowCount['total'];

// === Hitung total halaman ===
$totalHalaman = ($jumlahTampil > 0) ? ceil($totalData / $jumlahTampil) : 1;
$totalHalaman = max($totalHalaman, 1); // Pastikan minimal 1 halaman

// === Halaman aktif & offset ===
$halamanAktif = isset($_GET['halaman']) ? intval($_GET['halaman']) : 1;
$halamanAktif = max($halamanAktif, 1);               // minimal 1
$halamanAktif = min($halamanAktif, $totalHalaman);   // maksimal total halaman

$offset = max(($halamanAktif - 1) * $jumlahTampil, 0); // offset tidak boleh negatif

// === Query data lowongan milik perusahaan ini ===
$sql = "SELECT * FROM lowongan 
        WHERE id_pengguna = $userId $whereCari 
        ORDER BY dibuat_pada DESC
        LIMIT $jumlahTampil OFFSET $offset";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | MAGANG ID</title>
  <link rel="stylesheet" href="../style.css?v=<?php echo time(); ?>">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
</head>

<body>

  <div class="dashboard-wadah">
    <div class="sidebar-overlay"></div>
    <aside class="dashboard-sidebar">
      <div class="dashboard-sidebar-header">MAGANG ID</div>
      <nav class="dashboard-navigasi">
        <a href="dashboard_perusahaan/../dashboard_perusahaan.php" class="dashboard-link">
          <i class="fas fa-tachometer-alt"></i>
          <span>Dashboard</span>
        </a>
        <div class="dashboard-judul-seksi">Menu Utama</div>
        <a href="dashboard_perusahaan/../lowongan.php" class="dashboard-link">
          <i class="fas fa-list-alt text-xs"></i>
          <span>Lowongan</span>
        </a>
        <a href="dashboard_perusahaan/../pendaftar.php" class="dashboard-link" style="color: white">
          <i class="fas fa-user-friends text-xs"></i>
          <span>Pendaftar</span>
        </a>
        <div class="dashboard-judul-seksi">Lainnya</div>
        <a href="dashboard_perusahaan/../profil_perusahaan.php" class="dashboard-link">
          <i class="fas fa-user"></i>
          <span>Profil</span>
        </a>
        <a href="../logout.php" class="dashboard-link"
          onclick="return confirm('Yakin ingin keluar dari halaman dashboard??');">
          <i class="fas fa-sign-out-alt"></i>
          <span>Logout</span>
        </a>
      </nav>
    </aside>

    <div class="dashboard-konten-utama">
      <header class="dashboard-header-atas">
        <button class="hamburger-menu">
          <i class="fas fa-bars"></i>
        </button>
        <div class="dashboard-judul_halaman">
          <h3>Data Pendaftar</h3>
        </div>
        <details class="profil-dropdown">
          <summary class="profil-trigger">
            <?php if (!empty($user['foto'])): ?>
              <img src="../uploads/profil/perusahaan/<?= htmlspecialchars($user['foto']) ?>" alt="Foto Profil"
                class="profil-foto" />
            <?php else: ?>
              <i class="fas fa-user-circle dashboard-ikon-profil"></i>
            <?php endif; ?>
            <span><?= htmlspecialchars($user['nama']) ?></span>
            <i class="fas fa-caret-up caret-up"></i>
            <i class="fas fa-caret-down caret-down"></i>
          </summary>

          <div class="profil-menu">
            <a href="dashboard_perusahaan/../profil_perusahaan.php">
              <i class="fas fa-user"></i> Lihat Profil
            </a>
            <a href="dashboard_perusahaan/../profil_perusahaan.php">
              <i class="fas fa-lock"></i> Ubah Password
            </a>
            <a href="../logout.php" onclick="return confirm('Yakin ingin keluar dari halaman dashboard??');">
              <i class="fas fa-sign-out-alt"></i> Logout
            </a>
          </div>
        </details>
      </header>

      <main class="konten-utama-pendaftar">
        <section class="konten" aria-label="Data Pendaftar">
          <div class="kotak-tabel">
            <div class="dashboard_mahasiswa_perusahaan_filter">
              <form method="GET" action="" class="form-cari">
                <div class="tampil_berapa">
                  <label for="jumlah_tampil">Tampilkan</label>
                  <select id="jumlah_tampil" name="jumlah_tampil" onchange="this.form.submit()">
                    <option value="10" <?= ($jumlahTampil == 10) ? 'selected' : '' ?>>10</option>
                    <option value="25" <?= ($jumlahTampil == 25) ? 'selected' : '' ?>>25</option>
                    <option value="50" <?= ($jumlahTampil == 50) ? 'selected' : '' ?>>50</option>
                    <option value="100" <?= ($jumlahTampil == 100) ? 'selected' : '' ?>>100</option>
                  </select>
                </div>
                <div class="cari">
                  <div class="form-cari-input-group">
                    <input type="text" name="cari" value="<?= htmlspecialchars($cari) ?>" placeholder="Cari..."
                      aria-label="Cari...">
                    <button type="submit"><i class="fas fa-search"></i></button>
                  </div>
                </div>
              </form>
            </div>

            <div class="table-wrapper">
              <table class="tabel" role="table">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Gambar</th>
                    <th>Judul</th>
                    <th>Posisi</th>
                    <th>Total Pendaftar</th>
                    <th>Lihat Pendaftar</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no = $offset + 1;
                  if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                      $idLowongan = $row['id'];

                      // Hitung total pendaftar untuk lowongan ini
                      $sqlCountPendaftar = "SELECT COUNT(*) AS total_pendaftar FROM pendaftar WHERE id_lowongan = $idLowongan";
                      $resultCountPendaftar = mysqli_query($conn, $sqlCountPendaftar);
                      $rowCountPendaftar = mysqli_fetch_assoc($resultCountPendaftar);
                      $totalPendaftar = $rowCountPendaftar['total_pendaftar'];

                      echo "<tr>
                    <td>{$no}</td>";

                      // Kolom Gambar (cek ketersediaan gambar)
                      echo "<td class='gambar'>";
                      if (!empty($row['gambar'])) {
                        echo "<img src='../uploads/lowongan/{$row['gambar']}' alt='Gambar Lowongan' style='max-width: 80px; height: auto;'>";
                      } else {
                        echo "<span>-</span>";
                      }
                      echo "</td>";

                      echo "
                    <td>{$row['judul_lowongan']}</td>
                    <td>{$row['posisi']}</td>
                    <td>{$totalPendaftar} orang</td>
                    <td>
                      <a href='dashboard_perusahaan/../lihat_pendaftar.php?id={$row['id']}'>
                        <button class='lihat-pendaftar'><i class='fas fa-eye'></i></button>
                      </a>
                    </td>
                  </tr>";
                      $no++;
                    }
                  } else {
                    echo "<tr><td colspan='6'>Belum ada lowongan.</td></tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>
            <div class="wadah-pagination">
              <div class="info-pagination">
                <?php
                $mulai = $offset + 1;
                $akhir = $offset + mysqli_num_rows($result);
                echo "Menampilkan $mulai sampai $akhir dari total $totalData data";
                ?>
              </div>

              <div class="kontrol-pagination">
                <?php if ($halamanAktif > 1): ?>
                  <a href="?halaman=<?= $halamanAktif - 1 ?>&jumlah_tampil=<?= $jumlahTampil ?>&cari=<?= urlencode($cari) ?>"
                    class="link-pagination">Sebelumnya</a>
                <?php else: ?>
                  <span class="link-pagination non-aktif">Sebelumnya</span>
                <?php endif; ?>

                <span class="nomor-halaman"><?= $halamanAktif ?></span>

                <?php if ($halamanAktif < $totalHalaman): ?>
                  <a href="?halaman=<?= $halamanAktif + 1 ?>&jumlah_tampil=<?= $jumlahTampil ?>&cari=<?= urlencode($cari) ?>"
                    class="link-pagination">Berikutnya</a>
                <?php else: ?>
                  <span class="link-pagination non-aktif">Berikutnya</span>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </section>
      </main>
    </div>
  </div>

  <script>
    // Dashboard Hamburger Menu Controller
    document.addEventListener('DOMContentLoaded', function () {
      const hamburgerMenu = document.querySelector('.hamburger-menu');
      const sidebar = document.querySelector('.dashboard-sidebar');
      const overlay = document.querySelector('.sidebar-overlay');

      function openSidebar() {
        sidebar.classList.add('active');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
      }

      function closeSidebar() {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
      }

      if (hamburgerMenu) {
        hamburgerMenu.addEventListener('click', function (e) {
          e.stopPropagation();
          if (sidebar.classList.contains('active')) {
            closeSidebar();
          } else {
            openSidebar();
          }
        });
      }

      if (overlay) {
        overlay.addEventListener('click', closeSidebar);
      }

      document.addEventListener('click', function (e) {
        if (window.getComputedStyle(hamburgerMenu).display !== 'none') {
          if (!sidebar.contains(e.target) && !hamburgerMenu.contains(e.target)) {
            closeSidebar();
          }
        }
      });

      window.addEventListener('resize', function () {
        if (window.innerWidth > 991) {
          closeSidebar();
        }
      });

      if (sidebar) {
        sidebar.addEventListener('click', function (e) {
          e.stopPropagation();
        });
      }

      const navLinks = document.querySelectorAll('.dashboard-link');
      navLinks.forEach(link => {
        link.addEventListener('click', function () {
          if (window.getComputedStyle(hamburgerMenu).display !== 'none') {
            closeSidebar();
          }
        });
      });
    });
  </script>
</body>

</html>