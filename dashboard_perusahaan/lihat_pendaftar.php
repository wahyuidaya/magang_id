<?php
session_start();
include '../koneksi.php';


// Cek login & role
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'perusahaan') {
  header('Location: login_user/login_user/login.php');
  exit();
}

$userId = $_SESSION['user_id'];
$backUrl = isset($_GET['back']) ? $_GET['back'] : 'dashboard_perusahaan/../pendaftar.php';

// Ambil data user
$sqlUser = "SELECT nama, foto FROM pengguna WHERE id = $userId LIMIT 1";
$resultUser = mysqli_query($conn, $sqlUser);
$user = mysqli_fetch_assoc($resultUser);

// Cek ID lowongan
if (!isset($_GET['id'])) {
  die("<p>ID lowongan tidak valid.</p>");
}

$id_lowongan = mysqli_real_escape_string($conn, $_GET['id']);

// Validasi kepemilikan lowongan
$cekLowongan = "SELECT * FROM lowongan WHERE id = $id_lowongan AND id_pengguna = $userId";
$cekResult = mysqli_query($conn, $cekLowongan);
if (mysqli_num_rows($cekResult) === 0) {
  die("<p>Akses ditolak. Lowongan tidak ditemukan atau bukan milik Anda.</p>");
}

// === CETAK SEMUA ===
if (isset($_GET['cetak_semua'])) {
  $sqlCetak = "SELECT * FROM pendaftar WHERE id_lowongan = '$id_lowongan' AND status_lamaran = 'diterima'";
  $resultCetak = mysqli_query($conn, $sqlCetak);

  $sqlLowongan = "SELECT judul_lowongan, nama_perusahaan FROM lowongan WHERE id = '$id_lowongan'";
  $resultLowongan = mysqli_query($conn, $sqlLowongan);
  $lowongan = mysqli_fetch_assoc($resultLowongan);

  echo "<!DOCTYPE html>
    <html lang='id'>
    <head>
      <meta charset='UTF-8'>
      <meta name='viewport' content='width=device-width, initial-scale=1.0'>
      <title>Data Pendaftar Diterima</title>
      <link rel='stylesheet' href='../style.css?v=" . time() . "'>
    </head>
    <body class='body-cetak_data'>
      <div class='header-cetak_data'>
        <h1>MAGANG ID</h1>
        <h2>Data Pendaftar Diterima</h2>
        <div class='company-info-cetak_data'>
          <strong>Lowongan:</strong> " . htmlspecialchars($lowongan['judul_lowongan']) . "<br>
          <strong>Perusahaan:</strong> " . htmlspecialchars($lowongan['nama_perusahaan']) . "
        </div>
      </div>

      <div class='print-date-cetak_data'>
        Dicetak pada: " . date('d/m/Y H:i:s') . "
      </div>";

  $totalDiterima = mysqli_num_rows($resultCetak);

  echo "<div class='summary-cetak_data'>
      <h3>Ringkasan Data</h3>
      <p><strong>Total Pendaftar Diterima:</strong> {$totalDiterima} orang</p>
    </div>

    <div class='table-container-cetak_data'>
      <table class='table-cetak_data'>
        <thead>
          <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Kode Pendaftaran</th>
            <th>Jurusan</th>
            <th>Jenis Kelamin</th>
            <th>Perguruan Tinggi</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>";

  $no = 1;
  if ($totalDiterima > 0) {
    while ($row = mysqli_fetch_assoc($resultCetak)) {
      echo "<tr>
              <td>{$no}</td>
              <td>" . htmlspecialchars($row['nama_lengkap']) . "</td>
              <td>" . htmlspecialchars($row['kode_pendaftaran']) . "</td>
              <td>" . htmlspecialchars($row['jurusan']) . "</td>
              <td>" . htmlspecialchars($row['jenis_kelamin']) . "</td>
              <td>" . htmlspecialchars($row['perguruan_tinggi']) . "</td>
              <td><span class='status-badge-cetak_data'>DITERIMA</span></td>
            </tr>";
      $no++;
    }
  } else {
    echo "<tr><td colspan='7'>Belum ada pendaftar diterima.</td></tr>";
  }

  echo "</tbody>
      </table>
    </div>

    <div class='footer-cetak_data clearfix-cetak_data'>
      <div class='footer-left-cetak_data'>MAGANG ID</div>
      <div class='footer-right-cetak_data'>Halaman 1 dari 1</div>
    </div>

    <script>
      window.onload = function() { window.print(); };
      window.onafterprint = function() { setTimeout(function() { window.close(); }, 1000); };
    </script>
    </body>
    </html>";
  exit();
}

// === FILTER NORMAL ===
$jumlahTampil = 10;
if (isset($_GET['jumlah_tampil'])) {
  $jumlahTampil = intval($_GET['jumlah_tampil']);
  if (!in_array($jumlahTampil, [10, 25, 50, 100])) {
    $jumlahTampil = 10;
  }
}

$cari = '';
$whereCari = '';
if (isset($_GET['cari'])) {
  $cari = mysqli_real_escape_string($conn, $_GET['cari']);
  if (!empty($cari)) {
    $whereCari = " AND (nama_lengkap LIKE '%$cari%' OR kode_pendaftaran LIKE '%$cari%')";
  }
}

// === Hitung total data ===
$sqlCountPendaftar = "SELECT COUNT(*) AS total FROM pendaftar WHERE id_lowongan = '$id_lowongan' $whereCari";
$resultCountPendaftar = mysqli_query($conn, $sqlCountPendaftar);
$rowCountPendaftar = mysqli_fetch_assoc($resultCountPendaftar);
$totalData = $rowCountPendaftar['total'];

// === Hitung total halaman ===
$totalHalaman = ($jumlahTampil > 0) ? ceil($totalData / $jumlahTampil) : 1;
$totalHalaman = max($totalHalaman, 1);

// === Halaman aktif & offset ===
$halamanAktif = isset($_GET['halaman']) ? intval($_GET['halaman']) : 1;
$halamanAktif = max($halamanAktif, 1);
$halamanAktif = min($halamanAktif, $totalHalaman);

$offset = max(($halamanAktif - 1) * $jumlahTampil, 0);

// === Query pendaftar dengan LIMIT dan OFFSET ===
$sqlPendaftar = "SELECT * FROM pendaftar WHERE id_lowongan = '$id_lowongan' $whereCari ORDER BY id DESC LIMIT $jumlahTampil OFFSET $offset";
$resultPendaftar = mysqli_query($conn, $sqlPendaftar);

$sqlTotalDiterima = "SELECT COUNT(*) as total_diterima FROM pendaftar WHERE id_lowongan = '$id_lowongan' AND status_lamaran = 'diterima'";
$resultTotalDiterima = mysqli_query($conn, $sqlTotalDiterima);
$rowTotalDiterima = mysqli_fetch_assoc($resultTotalDiterima);
$totalDiterima = $rowTotalDiterima['total_diterima'];
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
        <a href="dashboard_perusahaan/../lowongan.php"
          class="dashboard-link <?= ($backUrl == 'dashboard_perusahaan/../lowongan.php') ? 'active' : '' ?>">
          <i class="fas fa-list-alt"></i>
          <span>Lowongan</span>
        </a>
        <a href="dashboard_perusahaan/../pendaftar.php"
          class="dashboard-link <?= ($backUrl != 'dashboard_perusahaan/../lowongan.php') ? 'active' : '' ?>"
          style="color: white">
          <i class="fas fa-user-friends"></i>
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
            <a href="dashboard_perusahaan/../profilperusahaan.php"><i class="fas fa-user"></i> Lihat Profil</a>
            <a href="dashboard_perusahaan/../profilperusahaan.php"><i class="fas fa-lock"></i> Ubah Password</a>
            <a href="../logout.php" onclick="return confirm('Yakin ingin keluar dari halaman dashboard??');"><i
                class="fas fa-sign-out-alt"></i> Logout</a>
          </div>
        </details>
      </header>

      <main class="konten-utama-lihat_pendaftaran" role="main">
        <section class="konten" aria-label="Lamaran Anda">
          <div class="kotak-tabel">
            <div class="lihat_pendaftaran-kembali_button">
              <a href="<?= htmlspecialchars($backUrl) ?>" class="tombol-kembali">
                <i class="fas fa-arrow-left"></i> Kembali
              </a>
              <?php if ($totalDiterima > 0): ?>
                <a href="?id=<?= htmlspecialchars($id_lowongan) ?>&cetak_semua=1" target="_blank">
                  <button type="button" class="tombol-cetak">
                    <i class="fas fa-print ikon"></i> Cetak Data
                  </button>
                </a>
              <?php endif; ?>
            </div>

            <div class="dashboard_mahasiswa_perusahaan_filter">
              <form method="GET" action="" class="form-cari">
                <input type="hidden" name="id" value="<?= htmlspecialchars($id_lowongan) ?>">
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
                    <input type="text" name="cari" value="<?= htmlspecialchars($cari) ?>" placeholder="Cari...">
                    <button type="submit"><i class="fas fa-search"></i></button>
                  </div>
                </div>
              </form>
            </div>

            <div class="wadah-tabel">
              <div class="table-wrapper">
                <table border="1" cellspacing="0" cellpadding="0">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Nama</th>
                      <th>Kode Pendaftaran</th>
                      <th>Jurusan</th>
                      <th>Jenis Kelamin</th>
                      <th>perguruan_tinggi</th>
                      <th>Lihat CV</th>
                      <th>Status</th>
                      <th>Opsi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $no = 1;
                    if (mysqli_num_rows($resultPendaftar) > 0) {
                      while ($row = mysqli_fetch_assoc($resultPendaftar)) {
                        echo "<tr>
                    <td>{$no}</td>
                    <td>{$row['nama_lengkap']}</td>
                    <td>{$row['kode_pendaftaran']}</td>
                    <td>{$row['jurusan']}</td>
                    <td>{$row['jenis_kelamin']}</td>
                    <td>{$row['perguruan_tinggi']}</td>";

                        echo "<td>";
                        if (!empty($row['cv_file'])) {
                          echo "
                    <form action='../uploads/cv/{$row['cv_file']}' method='get' target='_blank' class='wadah-cv'>
                      <button type='submit' class='lihat-pendaftar'>
                        <i class='fas fa-eye'></i>
                      </button>
                    </form>";
                        } else {
                          echo "-";
                        }
                        echo "</td>";

                        echo "<td>";
                        if ($row['status_lamaran'] === 'diterima') {
                          echo "<span class='badge badge-success'>Diterima</span>";
                        } else {
                          echo "<span class='badge badge-warning'>Diproses</span>";
                        }
                        echo "</td>";

                        echo "<td>";
                        $status = strtolower($row['status_lamaran']); // pastikan lowercase biar konsisten
                        if ($status === 'diproses' || $status === 'menunggu') {
                          echo "<div class='lihat_pendaftaran-button_opsi'>
                      <form method='POST' action='dashboard_perusahaan/../proses_penerimaan.php'>
                        <input type='hidden' name='id_pendaftaran' value='{$row['id']}'>
                        <input type='hidden' name='aksi' value='terima'>
                        <button type='submit' class='tombol-terima' onclick='return confirm(\"Yakin ingin menerima pendaftar ini?\")'>Terima</button>
                      </form>
                    </div>";
                        } else {
                          echo "-";
                        }
                        echo "</td>";

                        echo "</tr>";
                        $no++;
                      }
                    } else {
                      echo "<tr><td colspan='9' class='text-tengah'>Belum ada pendaftar.</td></tr>";
                    }
                    ?>
                  </tbody>
                </table>
              </div>
              <div class="wadah-pagination-lowongan">
                <div class="info-pagination">
                  <?php
                  $mulai = $offset + 1;
                  $akhir = $offset + mysqli_num_rows($resultPendaftar);
                  echo "Menampilkan $mulai sampai $akhir dari total $totalData pendaftar";
                  ?>
                </div>

                <div class="kontrol-pagination">
                  <?php if ($halamanAktif > 1): ?>
                    <a href="?id=<?= $id_lowongan ?>&halaman=<?= $halamanAktif - 1 ?>&jumlah_tampil=<?= $jumlahTampil ?>&cari_nama=<?= urlencode($cariNama) ?>"
                      class="link-pagination">Sebelumnya</a>
                  <?php else: ?>
                    <span class="link-pagination non-aktif">Sebelumnya</span>
                  <?php endif; ?>

                  <span class="nomor-halaman"><?= $halamanAktif ?></span>

                  <?php if ($halamanAktif < $totalHalaman): ?>
                    <a href="?id=<?= $id_lowongan ?>&halaman=<?= $halamanAktif + 1 ?>&jumlah_tampil=<?= $jumlahTampil ?>&cari_nama=<?= urlencode($cariNama) ?>"
                      class="link-pagination">Berikutnya</a>
                  <?php else: ?>
                    <span class="link-pagination non-aktif">Berikutnya</span>
                  <?php endif; ?>
                </div>
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