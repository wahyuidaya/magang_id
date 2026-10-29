<?php
session_start();
include '../koneksi.php';


// [1] Validasi login
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'perusahaan') {
  header('Location: login_user/login_user/login.php');
  exit();
}

$userId = $_SESSION['user_id'];

// [2] Ambil info profil perusahaan
$sqlUser = "SELECT nama, foto FROM pengguna WHERE id = $userId LIMIT 1";
$resultUser = mysqli_query($conn, $sqlUser);
$user = mysqli_fetch_assoc($resultUser);

// [3] Ambil ID lowongan dari GET
if (!isset($_GET['id'])) {
  echo "ID lowongan tidak valid.";
  exit();
}
$id = intval($_GET['id']);

// [4] Ambil data lowongan untuk prefill
$sqlLowongan = "SELECT * FROM lowongan WHERE id = $id AND id_pengguna = $userId LIMIT 1";
$resultLowongan = mysqli_query($conn, $sqlLowongan);
$row = mysqli_fetch_assoc($resultLowongan);
if (!$row) {
  echo "Lowongan tidak ditemukan atau bukan milik Anda.";
  exit();
}

// [5] Jika form disubmit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $judul = mysqli_real_escape_string($conn, $_POST['judul_lowongan']);
  $nama_perusahaan = mysqli_real_escape_string($conn, $_POST['nama_perusahaan']);
  $posisi = mysqli_real_escape_string($conn, $_POST['posisi']);
  $batas_waktu = mysqli_real_escape_string($conn, $_POST['batas_waktu']);
  $persyaratan = mysqli_real_escape_string($conn, $_POST['persyaratan']);
  $status_manual = mysqli_real_escape_string($conn, $_POST['status']);

  // Upload gambar baru jika ada
  $gambar = $row['gambar'];
  if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $nama_file = time() . '_' . basename($_FILES['gambar']['name']);
    $tmp_file = $_FILES['gambar']['tmp_name'];
    $target_dir = "../uploads/lowongan/";

    if (!is_dir($target_dir)) {
      mkdir($target_dir, 0777, true);
    }

    if (move_uploaded_file($tmp_file, $target_dir . $nama_file)) {
      $gambar = $nama_file;
    }
  }

  // === Logika status: manual + otomatis ===
  $status = $status_manual;
  if (strtotime($batas_waktu) < strtotime(date('Y-m-d'))) {
    // Paksa nonaktif kalau sudah lewat batas waktu
    $status = 'nonaktif';
  }

  // Update query
  $sqlUpdate = "UPDATE lowongan SET 
    judul_lowongan = '$judul',
    nama_perusahaan = '$nama_perusahaan',
    posisi = '$posisi',
    batas_waktu = '$batas_waktu',
    persyaratan = '$persyaratan',
    gambar = '$gambar',
    status = '$status'
    WHERE id = $id AND id_pengguna = $userId";

  if (mysqli_query($conn, $sqlUpdate)) {
    echo "<script>
      alert('Perubahan berhasil disimpan!');
      window.location.href='dashboard_perusahaan/../lowongan.php';
    </script>";
    exit();
  } else {
    echo "<script>
      alert('Gagal menyimpan perubahan: " . mysqli_error($conn) . "');
      window.history.back();
    </script>";
    exit();
  }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Lowongan | MAGANG ID</title>
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
          <h3>Edit Lowongan</h3>
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
            <!-- Profil menu sama -->
          </div>
        </details>
      </header>

      <main class="konten-utama-posting_lowongan">
        <section class="area-konten">
          <form class="form-posting" method="post" enctype="multipart/form-data" autocomplete="off">
            <a href="dashboard_perusahaan/../lowongan.php" class="tombol-kembali">
              <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <div class="baris-form">
              <div class="kolom-form">
                <label for="judul_lowongan">Judul Lowongan</label>
                <input type="text" id="judul_lowongan" name="judul_lowongan"
                  value="<?= htmlspecialchars($row['judul_lowongan']) ?>" required />
              </div>
              <div class="kolom-form gambar">
                <label for="gambar">Gambar</label>
                <input type="file" id="gambar" name="gambar" accept="image/*" />
              </div>
            </div>

            <div class="baris-form">
              <div class="kolom-form">
                <label for="nama_perusahaan">Nama Perusahaan</label>
                <input type="text" id="nama_perusahaan" name="nama_perusahaan"
                  value="<?= htmlspecialchars($row['nama_perusahaan']) ?>" />
              </div>
            </div>

            <div class="baris-form">
              <div class="kolom-form">
                <label for="posisi">Posisi</label>
                <input type="text" id="posisi" name="posisi" value="<?= htmlspecialchars($row['posisi']) ?>" />
              </div>
            </div>

            <div class="baris-form">
              <div class="kolom-form">
                <label for="batas_waktu">Batas Waktu</label>
                <input type="date" id="batas_waktu" name="batas_waktu"
                  value="<?= htmlspecialchars($row['batas_waktu']) ?>" />
              </div>
            </div>

            <div class="baris-form">
              <div class="kolom-form">
                <label for="persyaratan">Persyaratan</label>
                <textarea id="persyaratan" name="persyaratan" rows="4"
                  style="width: 100%;"><?= htmlspecialchars($row['persyaratan']) ?></textarea>
              </div>
            </div>

            <div class="baris-form">
              <div class="kolom-form">
                <label for="status">Status Lowongan</label>
                <select id="status" name="status">
                  <option value="aktif" <?= $row['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                  <option value="nonaktif" <?= $row['status'] === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                </select>
              </div>
            </div>
            <div>
              <button type="submit" class="tombol-posting">
                <i class="fas fa-save"></i> Simpan Perubahan
              </button>
            </div>
          </form>
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