<?php
session_start();
include '../koneksi.php';

// Pastikan pengguna login sebagai admin
if (!isset($_SESSION['admin_id'])) {
  header('Location: ../login_admin/login_admin.php');
  exit();
}

$userId = $_SESSION['admin_id'];

$sql = "SELECT nama FROM admin WHERE id = $userId LIMIT 1";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Hitung statistik umum
$totalPerusahaan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM pengguna WHERE peran = 'perusahaan'"))['total'];
$totalMahasiswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM pengguna WHERE peran = 'mahasiswa'"))['total'];
$totalLowongan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM lowongan"))['total'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin | MAGANG ID</title>
  <link rel="stylesheet" href="../style.css?v=<?php echo time(); ?>">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
</head>

<body>

  <div class="dashboard-wadah">
    <div class="sidebar-overlay"></div>
    <aside class="dashboard-sidebar">
      <div class="dashboard-sidebar-header">MAGANG ID</div>
      <nav class="dashboard-navigasi">
        <a href="dashboard_admin/../dashboard_admin.php" class="dashboard-link" style="color: white">
          <i class="fas fa-tachometer-alt"></i>
          <span>Dashboard</span>
        </a>
        <div class="dashboard-judul-seksi">Kelola Data</div>
        <a href="admin_perusahaan.php" class="dashboard-link">
          <i class="fas fa-building"></i>
          <span>Perusahaan</span>
        </a>
        <a href="admin_mahasiswa.php" class="dashboard-link">
          <i class="fas fa-user-graduate"></i>
          <span>Mahasiswa</span>
        </a>
        <a href="admin_lowongan.php" class="dashboard-link">
          <i class="fas fa-briefcase"></i>
          <span>Lowongan</span>
        </a>
        <div class="dashboard-judul-seksi">Lainnya</div>
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
          <h3>Dashboard Admin</h3>
        </div>
        <details class="profil-dropdown">
          <summary class="profil-trigger">
            <?php if (!empty($user['foto'])): ?>
              <img src="../uploads/profil/admin/<?= htmlspecialchars($user['foto']) ?>" alt="Foto Profil"
                class="profil-foto" />
            <?php else: ?>
              <i class="fas fa-user-circle dashboard-ikon-profil"></i>
            <?php endif; ?>
            <span><?= htmlspecialchars($user['nama']) ?></span>
            <i class="fas fa-caret-up caret-up"></i>
            <i class="fas fa-caret-down caret-down"></i>
          </summary>
          <div class="profil-menu">
            <a href="../logout.php" onclick="return confirm('Yakin ingin keluar dari halaman dashboard??');">
              <i class="fas fa-sign-out-alt"></i> Logout
            </a>
          </div>
        </details>
      </header>

      <main class="dashboard-konten">
        <div class="dashboard-kotak-sambutan">
          <span>Selamat datang <?= htmlspecialchars($user['nama']) ?></span>
        </div>

        <div class="dashboard-grid-info">
          <a href="admin_perusahaan.php" class="dashboard-kotak-info-link">
            <div class="dashboard-kotak-info">
              <i class="fas fa-building dashboard-ikon-info"></i>
              <span><?= $totalPerusahaan ?> Perusahaan Terdaftar</span>
            </div>
          </a>
          <a href="admin_mahasiswa.php" class="dashboard-kotak-info-link">
            <div class="dashboard-kotak-info">
              <i class="fas fa-user-graduate dashboard-ikon-info"></i>
              <span><?= $totalMahasiswa ?> Mahasiswa Terdaftar</span>
            </div>
          </a>
          <a href="admin_lowongan.php" class="dashboard-kotak-info-link">
            <div class="dashboard-kotak-info">
              <i class="fas fa-briefcase dashboard-ikon-info"></i>
              <span><?= $totalLowongan ?> Total Lowongan</span>
            </div>
          </a>
        </div>
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