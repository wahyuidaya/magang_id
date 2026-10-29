<?php
session_start();
include '../koneksi.php';


// [1] Validasi login dan role perusahaan
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'perusahaan') {
  header('Location: login_user/login_user/login.php');
  exit();
}

$userId = $_SESSION['user_id'];

// [2] Ambil data perusahaan untuk header (foto & nama)
$sql = "SELECT nama, foto FROM pengguna WHERE id = $userId LIMIT 1";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// [3] Handle form POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Bersihkan input & trim supaya tidak hanya spasi
  $judul = trim(mysqli_real_escape_string($conn, $_POST['judul_lowongan']));
  $posisi = trim(mysqli_real_escape_string($conn, $_POST['posisi']));
  $batas_waktu = trim(mysqli_real_escape_string($conn, $_POST['batas_waktu']));
  $persyaratan = trim(mysqli_real_escape_string($conn, $_POST['persyaratan']));
  $nama_perusahaan = mysqli_real_escape_string($conn, $user['nama']);
  $id_pengguna = $_SESSION['user_id'];

  // === VALIDASI WAJIB ISI termasuk gambar ===
  if (
    empty($judul) ||
    empty($posisi) ||
    empty($batas_waktu) ||
    empty($persyaratan) ||
    empty($_FILES['gambar']['name'])
  ) {
    echo "<script>
            alert('Gagal! Semua kolom wajib diisi termasuk gambar.');
            window.history.back();
        </script>";
    exit();
  }

  // Upload Gambar
  $gambar = '';
  if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $nama_file = time() . '_' . basename($_FILES['gambar']['name']);
    $tmp_file = $_FILES['gambar']['tmp_name'];
    $target_dir = "../uploads/lowongan/";

    if (!is_dir($target_dir)) {
      mkdir($target_dir, 0777, true);
    }

    if (move_uploaded_file($tmp_file, $target_dir . $nama_file)) {
      $gambar = $nama_file;
    } else {
      echo "<script>
                alert('Gagal mengupload gambar.');
                window.history.back();
            </script>";
      exit();
    }
  } else {
    echo "<script>
            alert('Gagal! File gambar tidak valid.');
            window.history.back();
        </script>";
    exit();
  }

  // Query INSERT
  $sql = "INSERT INTO lowongan 
      (judul_lowongan, nama_perusahaan, posisi, batas_waktu, persyaratan, gambar, id_pengguna) 
      VALUES 
      ('$judul', '$nama_perusahaan', '$posisi', '$batas_waktu', '$persyaratan', '$gambar', '$id_pengguna')";

  if (mysqli_query($conn, $sql)) {
    echo "<script>
            alert('Lowongan berhasil ditambahkan!');
            window.location.href='dashboard_perusahaan/../lowongan.php';
        </script>";
    exit();
  } else {
    echo "<script>
            alert('Gagal menambahkan lowongan: " . mysqli_error($conn) . "');
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
          <h3>Posting Lowongan Baru</h3>
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
            <!-- Ganti dengan 2 ikon -->
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
      <main class="konten-utama-posting_lowongan">
        <section class="area-konten" aria-label="Form posting lowongan baru">
          <form class="form-posting" method="post" enctype="multipart/form-data" action="" autocomplete="off"
            novalidate>
            <a href="dashboard_perusahaan/../lowongan.php" class="tombol-kembali">
              <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <div class="baris-form">
              <div class="kolom-form">
                <label for="judul_lowongan">Judul Lowongan</label>
                <input type="text" id="judul_lowongan" name="judul_lowongan" tabindex="1" required />
              </div>
              <div class="kolom-form gambar">
                <label for="gambar">Gambar</label>
                <input type="file" id="gambar" name="gambar" tabindex="2" accept="image/*" />
              </div>
            </div>
            <div class="baris-form">
              <div class="kolom-form">
                <label for="nama_perusahaan">Nama Perusahaan</label>
                <input type="text" id="nama_perusahaan" name="nama_perusahaan" tabindex="3"
                  value="<?= htmlspecialchars($user['nama']) ?>" readonly />
              </div>
            </div>
            <div class="baris-form">
              <div class="kolom-form">
                <label for="posisi">Posisi</label>
                <input type="text" id="posisi" name="posisi" tabindex="4" />
              </div>
            </div>
            <div class="baris-form">
              <div class="kolom-form">
                <label for="batas_waktu">Batas Waktu</label>
                <input type="date" id="batas_waktu" name="batas_waktu" tabindex="5" />
              </div>
            </div>
            <div class="baris-form">
              <div class="kolom-form">
                <label for="persyaratan">Persyaratan</label>
                <div style="border: 1px solid #d1d5db; border-radius: 0.25rem; font-size: 0.75rem; color: #374151;">
                  <textarea id="persyaratan" name="persyaratan" tabindex="6" rows="4"
                    style="width: 100%; border: none; padding: 0.5rem; resize: none;"></textarea>
                </div>
              </div>
            </div>
            <div>
              <button type="submit" tabindex="7" class="tombol-posting">
                <i class="fas fa-upload"></i>Posting Lowongan
              </button>
            </div>
          </form>
        </section>
      </main>
    </div>
  </div>

  <script>
    // Script untuk mencegah Enter submit kecuali di tombol submit
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' && e.target.type !== 'submit' && e.target.tagName !== 'BUTTON') {
        e.preventDefault();

        // Pindah ke elemen selanjutnya berdasarkan tabindex
        const currentTabIndex = parseInt(e.target.tabIndex);
        const nextElement = document.querySelector(`[tabindex="${currentTabIndex + 1}"]`);

        if (nextElement) {
          nextElement.focus();
        }
      }
    });

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