<?php
session_start();
include '../koneksi.php';

// Pastikan pengguna sudah login dan memiliki role perusahaan
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'perusahaan') {
  header('Location: login_user/login.php');
  exit();
}

$userId = $_SESSION['user_id'];

// Ambil informasi pengguna terbaru dari database
$sqlUser = "SELECT nama, foto FROM pengguna WHERE id = $userId LIMIT 1";
$resultUser = mysqli_query($conn, $sqlUser);
$user = mysqli_fetch_assoc($resultUser);

// === Ambil jumlah tampil ===
$jumlahTampil = 10; // Default
if (isset($_GET['jumlah_tampil'])) {
  $jumlahTampil = intval($_GET['jumlah_tampil']);
  if (!in_array($jumlahTampil, [10, 25, 50, 100])) {
    $jumlahTampil = 10; // fallback
  }
}

// === Ambil kata kunci cari ===
$cari = '';
$whereCari = '';
if (isset($_GET['cari_judul'])) {
  $cari = mysqli_real_escape_string($conn, $_GET['cari_judul']);
  if (!empty($cari)) {
    $whereCari = " AND judul_lowongan LIKE '%$cari%'";
  }
}

// === Hitung total data ===
$sqlCount = "SELECT COUNT(*) AS total FROM lowongan WHERE id_pengguna = $userId $whereCari";
$resultCount = mysqli_query($conn, $sqlCount);
$rowCount = mysqli_fetch_assoc($resultCount);
$totalData = $rowCount['total'];

// === Hitung total halaman, minimal 1 ===
$totalHalaman = ($jumlahTampil > 0) ? ceil($totalData / $jumlahTampil) : 1;
$totalHalaman = max($totalHalaman, 1);

// === Halaman aktif & offset ===
$halamanAktif = isset($_GET['halaman']) ? intval($_GET['halaman']) : 1;
$halamanAktif = max($halamanAktif, 1);               // tidak boleh < 1
$halamanAktif = min($halamanAktif, $totalHalaman);   // tidak boleh melebihi total halaman

$offset = max(($halamanAktif - 1) * $jumlahTampil, 0); // tidak boleh negatif

// === Ambil data lowongan milik perusahaan ===
// === Ambil data lowongan milik perusahaan ===
$sql = "SELECT *, 
        CASE 
            WHEN batas_waktu < NOW() THEN 'nonaktif' 
            ELSE status 
        END AS status_lowongan 
        FROM lowongan 
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
        <a href="dashboard_perusahaan/../lowongan.php" class="dashboard-link" style="color: white">
          <i class="fas fa-list-alt text-xs"></i>
          <span>Lowongan</span>
        </a>
        <a href="dashboard_perusahaan/../pendaftar.php" class="dashboard-link">
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
          <h3>Daftar Lowongan</h3>
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

      <main class="konten" role="main">
        <section class="konten-utama-lowongan" aria-label="Daftar Lowongan">
          <div class="kotak-tabel">
            <a href="dashboard_perusahaan/../posting_lowongan_baru.php">
              <button type="button" class="posting" aria-label="Posting Baru">
                <i class="fas fa-plus-circle"></i>
                <span>Posting Baru</span>
              </button>
            </a>
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
                    <input type="text" name="cari_judul" value="<?= htmlspecialchars($cari) ?>" placeholder="Cari..."
                      aria-label="Cari judul lowongan">
                    <button type="submit"><i class="fas fa-search"></i></button>
                  </div>
                </div>
              </form>
            </div>

            <div class="table-wrapper">
              <table class="tabel-lowongan" role="table">
                <thead>
                  <tr>
                    <th scope="col">No</th>
                    <th scope="col">Judul</th>
                    <th scope="col">Posisi</th>
                    <th scope="col">Batas Waktu</th>
                    <th scope="col">Status</th>
                    <th scope="col">Opsi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no = $offset + 1;
                  if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                      $status = ($row['status_lowongan'] === 'aktif') ? "<span class='badge badge-success'>Aktif</span>" : "<span class='badge badge-danger'>Nonaktif</span>";

                      echo "<tr>
                      <td>{$no}</td>
                      <td>" . htmlspecialchars($row['judul_lowongan']) . "</td>
                      <td>" . htmlspecialchars($row['posisi']) . "</td>
                      <td>" . htmlspecialchars($row['batas_waktu']) . "</td>
                      <td>{$status}</td>
                      <td class='aksi'>";

                      // Tombol LIHAT
                      echo "<form action='dashboard_perusahaan/../lihat_pendaftar.php' method='get' style='display:inline;'>
                            <input type='hidden' name='id' value='" . $row['id'] . "'>
                            <input type='hidden' name='back' value='dashboard_perusahaan/../lowongan.php'>
                            <button type='submit' class='lihat'><i class='fas fa-eye'></i></button>
                          </form>";

                      // Tombol EDIT
                      echo "<form action='dashboard_perusahaan/../edit_lowongan.php' method='get' style='display:inline;'>
                            <input type='hidden' name='id' value='" . $row['id'] . "'>
                            <button type='submit' class='edit'><i class='fas fa-edit'></i></button>
                          </form>";

                      // Tombol HAPUS
                      echo "<form action='dashboard_perusahaan/../hapus_lowongan.php' method='post' style='display:inline;'>
                            <input type='hidden' name='id' value='" . $row['id'] . "'>
                            <button type='submit' class='hapus' onclick=\"return confirm('Apakah Anda yakin ingin menghapus lowongan ini?');\">
                              <i class='fas fa-trash-alt'></i>
                            </button>
                          </form>";

                      echo "</td></tr>";
                      $no++;
                    }
                  } else {
                    echo "<tr><td colspan='6'>Belum ada lowongan.</td></tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>
            <div class="wadah-pagination-lowongan">
              <div class="info-pagination">
                <?php
                $mulai = $offset + 1;
                $akhir = $offset + mysqli_num_rows($result);
                echo "Menampilkan $mulai sampai $akhir dari total $totalData lowongan";
                ?>
              </div>

              <div class="kontrol-pagination">
                <?php if ($halamanAktif > 1): ?>
                  <a href="?halaman=<?= $halamanAktif - 1 ?>&jumlah_tampil=<?= $jumlahTampil ?>&cari_judul=<?= urlencode($cari) ?>"
                    class="link-pagination">Sebelumnya</a>
                <?php else: ?>
                  <span class="link-pagination non-aktif">Sebelumnya</span>
                <?php endif; ?>

                <span class="nomor-halaman"><?= $halamanAktif ?></span>

                <?php if ($halamanAktif < $totalHalaman): ?>
                  <a href="?halaman=<?= $halamanAktif + 1 ?>&jumlah_tampil=<?= $jumlahTampil ?>&cari_judul=<?= urlencode($cari) ?>"
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