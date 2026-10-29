<?php
session_start();
include '../koneksi.php';

// Cek login admin
if (!isset($_SESSION['admin_id'])) {
  header('Location: ../login_admin/login_admin.php');
  exit();
}

$userId = $_SESSION['admin_id'];

// Ambil nama admin
$sqlAdmin = "SELECT nama FROM admin WHERE id = $userId LIMIT 1";
$resultAdmin = mysqli_query($conn, $sqlAdmin);
$user = mysqli_fetch_assoc($resultAdmin);

// Ambil jumlah tampil & pencarian
$jumlahTampil = isset($_GET['jumlah_tampil']) ? (int) $_GET['jumlah_tampil'] : 10;
$halamanAktif = isset($_GET['halaman']) ? (int) $_GET['halaman'] : 1;
$offset = ($halamanAktif - 1) * $jumlahTampil;
$cari = isset($_GET['cari_perusahaan']) ? trim($_GET['cari_perusahaan']) : '';

// Query total data perusahaan
$sqlTotal = "SELECT COUNT(*) AS total FROM pengguna WHERE peran = 'perusahaan' AND (nama LIKE '%$cari%' OR email LIKE '%$cari%')";
$resultTotal = mysqli_query($conn, $sqlTotal);
$totalData = mysqli_fetch_assoc($resultTotal)['total'];
$totalHalaman = ceil($totalData / $jumlahTampil);

// Query data perusahaan
$sqlPerusahaan = "SELECT id, nama, email FROM pengguna WHERE peran = 'perusahaan' AND (nama LIKE '%$cari%' OR email LIKE '%$cari%') 
                  ORDER BY id DESC LIMIT $offset, $jumlahTampil";
$resultPerusahaan = mysqli_query($conn, $sqlPerusahaan);
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Perusahaan | Dashboard Admin - MAGANG ID</title>
  <link rel="stylesheet" href="../style.css?v=<?= time(); ?>">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
</head>

<body>

  <div class="dashboard-wadah">
    <div class="sidebar-overlay"></div>
    <aside class="dashboard-sidebar">
      <div class="dashboard-sidebar-header">MAGANG ID</div>
      <nav class="dashboard-navigasi">
        <a href="dashboard_admin.php" class="dashboard-link">
          <i class="fas fa-tachometer-alt"></i>
          <span>Dashboard</span>
        </a>
        <div class="dashboard-judul-seksi">Kelola Data</div>
        <a href="admin_perusahaan.php" class="dashboard-link" style="color: white;">
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
          <h3>Kelola Perusahaan</h3>
        </div>
        <details class="profil-dropdown">
          <summary class="profil-trigger">
            <i class="fas fa-user-circle dashboard-ikon-profil"></i>
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

      <main class="konten-utama-lamaran_anda">
        <section class="konten" aria-label="Kelola Perusahaan">
          <div class="kotak-tabel">
            <div class="dashboard_mahasiswa_perusahaan_filter">
              <form method="GET" class="form-cari">
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
                    <input type="text" name="cari_perusahaan" value="<?= htmlspecialchars($cari) ?>"
                      placeholder="Cari..." aria-label="Cari perusahaan">
                    <button type="submit"><i class="fas fa-search"></i></button>
                  </div>
                </div>
              </form>
            </div>

            <div class="table-wrapper">
              <table class="tabel-data">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (mysqli_num_rows($resultPerusahaan) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($resultPerusahaan)): ?>
                      <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td>
                          <button
                            onclick="if(confirm('Yakin ingin menghapus perusahaan ini')) { window.location.href='hapus_perusahaan.php?id=<?= $row['id'] ?>'; }"
                            class="hapus">
                            <i class="fas fa-trash-alt"></i> Hapus
                          </button>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="4" class="text-tengah">Tidak ada data ditemukan.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>

            <div class="wadah-pagination">
              <div class="info-pagination">
                <?php
                $mulai = $offset + 1;
                $akhir = $offset + mysqli_num_rows($resultPerusahaan);
                echo "Menampilkan $mulai sampai $akhir dari total $totalData data";
                ?>
              </div>

              <div class="kontrol-pagination">
                <?php if ($halamanAktif > 1): ?>
                  <a href="?halaman=<?= $halamanAktif - 1 ?>&jumlah_tampil=<?= $jumlahTampil ?>&cari_perusahaan=<?= urlencode($cari) ?>"
                    class="link-pagination">Sebelumnya</a>
                <?php else: ?>
                  <span class="link-pagination non-aktif">Sebelumnya</span>
                <?php endif; ?>

                <span class="nomor-halaman"><?= $halamanAktif ?></span>

                <?php if ($halamanAktif < $totalHalaman): ?>
                  <a href="?halaman=<?= $halamanAktif + 1 ?>&jumlah_tampil=<?= $jumlahTampil ?>&cari_perusahaan=<?= urlencode($cari) ?>"
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