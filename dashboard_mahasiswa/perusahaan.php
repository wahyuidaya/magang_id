<?php
session_start();
include '../koneksi.php';


// Pastikan pengguna sudah login dan memiliki role mahasiswa
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'mahasiswa') {
  header('Location: login_user/login_user/login.php');
  exit();
}

$userId = $_SESSION['user_id'];

// Ambil informasi pengguna terbaru dari database untuk foto dan nama
$sqlUser = "SELECT nama, foto FROM pengguna WHERE id = $userId LIMIT 1";
$resultUser = mysqli_query($conn, $sqlUser);
$user = mysqli_fetch_assoc($resultUser);

// Update status lowongan otomatis jika batas waktu sudah lewat
$sqlUpdateStatus = "UPDATE lowongan SET status = 'nonaktif'
                    WHERE batas_waktu < CURDATE() AND status = 'aktif'";
mysqli_query($conn, $sqlUpdateStatus);

// === Filter Pencarian ===
$cari = '';
if (isset($_GET['cari_perusahaan'])) {
  $cari = mysqli_real_escape_string($conn, $_GET['cari_perusahaan']);
}

// === Jumlah tampil per halaman ===
$jumlahTampil = 10; // Default
if (isset($_GET['jumlah_tampil'])) {
  $jumlahTampil = intval($_GET['jumlah_tampil']);
  if (!in_array($jumlahTampil, [10, 25, 50, 100])) {
    $jumlahTampil = 10; // fallback ke default jika input tidak valid
  }
}

// === Hitung total data ===
$sqlCount = "SELECT COUNT(*) AS total FROM lowongan WHERE 1";
if (!empty($cari)) {
  $sqlCount .= " AND (nama_perusahaan LIKE '%$cari%' OR posisi LIKE '%$cari%' OR judul_lowongan LIKE '%$cari%')";
}
$resultCount = mysqli_query($conn, $sqlCount);
$rowCount = mysqli_fetch_assoc($resultCount);
$totalData = $rowCount['total'];

// === Hitung total halaman ===
$totalHalaman = ($jumlahTampil > 0) ? ceil($totalData / $jumlahTampil) : 1;
$totalHalaman = max($totalHalaman, 1); // Pastikan minimal 1 agar offset tidak negatif

// === Halaman aktif ===
$halamanAktif = isset($_GET['halaman']) ? intval($_GET['halaman']) : 1;
$halamanAktif = max($halamanAktif, 1);
$halamanAktif = min($halamanAktif, $totalHalaman);

// === OFFSET ===
$offset = max(($halamanAktif - 1) * $jumlahTampil, 0);

// === Query final data lowongan ===
$sql = "SELECT * FROM lowongan WHERE 1";
if (!empty($cari)) {
  $sql .= " AND (nama_perusahaan LIKE '%$cari%' OR posisi LIKE '%$cari%' OR judul_lowongan LIKE '%$cari%')";
}
$sql .= " ORDER BY dibuat_pada DESC LIMIT $jumlahTampil OFFSET $offset";

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
        <a href="../dashboard_mahasiswa/dashboard_mahasiswa.php" class="dashboard-link">
          <i class="fas fa-tachometer-alt"></i>
          <span>Dashboard</span>
        </a>
        <div class="dashboard-judul-seksi">Menu Utama</div>
        <a href="perusahaan.php" class="dashboard-link" style="color: white">
          <i class="fas fa-building"></i>
          <span>Perusahaan</span>
        </a>
        <a href="dashboard_mahasiswa/../lamaran_anda.php" class="dashboard-link">
          <i class="fas fa-file-alt"></i>
          <span>Lamaran Anda</span>
        </a>
        <div class="dashboard-judul-seksi">Lainnya</div>
        <a href="dashboard_mahasiswa/../profil_mahasiswa.php" class="dashboard-link">
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
          <h3>Daftar Perusahaan</h3>
        </div>
        <details class="profil-dropdown">
          <summary class="profil-trigger">
            <?php if (!empty($user['foto'])): ?>
              <img src="../uploads/profil/mahasiswa/<?= htmlspecialchars($user['foto']) ?>" alt="Foto Profil"
                class="profil-foto" />
            <?php else: ?>
              <i class="fas fa-user-circle dashboard-ikon-profil"></i>
            <?php endif; ?>
            <span><?= htmlspecialchars($user['nama']) ?></span>
            <!-- Ganti dengan 2 ikon -->
            <i class="fas fa-caret-up caret-up"></i>
            <i class="fas fa-caret-down caret-down"></i>
          </summary>

          <div class="profil-menu">
            <a href="dashboard_mahasiswa/../profil_mahasiswa.php">
              <i class="fas fa-user"></i> Lihat Profil
            </a>
            <a href="dashboard_mahasiswa/../profil_mahasiswa.php">
              <i class="fas fa-lock"></i> Ubah Password
            </a>
            <a href="../logout.php" onclick="return confirm('Yakin ingin keluar dari halaman dashboard??');">
              <i class="fas fa-sign-out-alt"></i> Logout
            </a>
          </div>
        </details>
      </header>
      <main class="konten-utama-perusahaan" role="main">
        <section class="konten" aria-label="Daftar perusahaan">
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
                    <input type="text" name="cari_perusahaan" value="<?= htmlspecialchars($cari ?? '') ?>"
                      placeholder="Cari..." aria-label="Cari perusahaan">
                    <button type="submit"><i class="fas fa-search"></i></button>
                  </div>
                </div>
              </form>
            </div>

            <!-- TAMBAHKAN WRAPPER INI UNTUK HORIZONTAL SCROLL -->
            <div class="table-wrapper">
              <table class="tabel dashboard_mahasiswa_perusahaan_tabel" role="table">
                <thead>
                  <tr>
                    <th scope="col" class="nomor">No</th>
                    <th scope="col">Perusahaan</th>
                    <th scope="col" class="gambar">Gambar</th>
                    <th scope="col">Judul</th>
                    <th scope="col">Posisi</th>
                    <th scope="col">Batas Waktu</th>
                    <th scope="col" class="tombol_daftar">Opsi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no = $offset + 1;

                  if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                      $batasWaktu = date('d-m-Y', strtotime($row['batas_waktu']));
                      $statusKadaluarsa = ($row['status'] != 'aktif' || $row['batas_waktu'] < date('Y-m-d'));
                      $rowClass = $statusKadaluarsa ? ' class="baris-kadaluarsa"' : '';

                      echo "<tr{$rowClass}>
              <td class='nomor'>{$no}</td>
              <td>{$row['nama_perusahaan']}</td>
              <td class='gambar'>";

                      if (!empty($row['gambar'])) {
                        echo "<img src='../uploads/lowongan/{$row['gambar']}' alt='Lowongan {$row['judul_lowongan']}' style='max-width: 100px; height: auto;' />";
                      } else {
                        echo "<span>-</span>";
                      }

                      echo "</td>
              <td>{$row['judul_lowongan']}</td>
              <td>{$row['posisi']}</td>
              <td>{$batasWaktu}</td>
              <td class='tombol_daftar'>
                <div class='wadah-tombol-aksi'>
                  <a href='../detail_lowongan.php?id={$row['id']}' class='button-detail'>
                    <i class='fas fa-info-circle'></i> Detail
                  </a>";

                      if (!$statusKadaluarsa) {
                        echo "<a href='form_pendaftaran.php?id={$row['id']}' class='button-daftar'>
                  <i class='fas fa-user-plus'></i> Daftar
                </a>";
                      } else {
                        echo "<button class='tombol_tutup' disabled>
                  <i class='fas fa-lock'></i> Ditutup
                </button>";
                      }

                      echo "    </div>
              </td>
            </tr>";
                      $no++;
                    }
                  } else {
                    echo "<tr><td colspan='7' class='text-tengah'>Belum ada lowongan.</td></tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>
            <!-- AKHIR WRAPPER -->

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