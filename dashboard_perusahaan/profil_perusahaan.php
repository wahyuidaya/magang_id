<?php
session_start();
include '../koneksi.php';

// Pastikan pengguna sudah login dan memiliki role perusahaan
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'perusahaan') {
  header('Location: login_user/login.php');
  exit();
}

$userId = $_SESSION['user_id'];

$error = '';
$success = '';

// === HANDLE FORM ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // === 1) HANDLE UPLOAD FOTO PROFIL ===
  if (isset($_POST['submit_foto'])) {
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    if (!empty($_FILES['profile_foto']['name'])) {
      $uploadDir = '../uploads/profil/perusahaan/';
      if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
      }

      $fileTmpPath = $_FILES['profile_foto']['tmp_name'];
      $fileName = basename($_FILES['profile_foto']['name']);
      $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
      $checkMime = mime_content_type($fileTmpPath);
      $allowedMime = ['image/jpeg', 'image/png', 'image/gif'];

      if (!in_array($checkMime, $allowedMime)) {
        $error .= "Tipe file tidak valid.";
      } elseif (in_array($fileExtension, $allowedExtensions)) {
        $newFileName = $userId . '_' . time() . '.' . $fileExtension;
        $destPath = $uploadDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
          $updateSql = "UPDATE pengguna SET foto = '$newFileName' WHERE id = $userId";
          if (mysqli_query($conn, $updateSql)) {
            $success = "Foto profil berhasil diperbarui!";
          } else {
            $error .= "Gagal update foto di database: " . mysqli_error($conn);
          }
        } else {
          $error .= "Gagal memindahkan file ke folder upload.";
        }
      } else {
        $error .= "Format file tidak didukung. Gunakan JPG, PNG, atau GIF.";
      }
    } else {
      $error .= "Pilih file foto terlebih dahulu.";
    }
  }

  // === 2) HANDLE UPDATE DATA PROFIL ===
  elseif (isset($_POST['submit_data'])) {
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $deskripsi_perusahaan = mysqli_real_escape_string($conn, $_POST['deskripsi_perusahaan'] ?? '');
    $kata_sandi_baru = $_POST['kata_sandi_baru'] ?? '';
    $konfirmasi_kata_sandi = $_POST['konfirmasi_kata_sandi'] ?? '';
    $kataSandiUpdate = '';

    if (!empty($kata_sandi_baru) || !empty($konfirmasi_kata_sandi)) {
      if (strlen($kata_sandi_baru) < 6) {
        $error .= "Kata sandi minimal 6 karakter. ";
      } elseif ($kata_sandi_baru !== $konfirmasi_kata_sandi) {
        $error .= "Konfirmasi kata sandi tidak cocok. ";
      } else {
        $hashSandi = password_hash($kata_sandi_baru, PASSWORD_DEFAULT);
        $kataSandiUpdate = ", kata_sandi = '$hashSandi'";
      }
    }

    if (empty($error)) {
      $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
      $email = mysqli_real_escape_string($conn, $_POST['email']);
      $no_telepon = mysqli_real_escape_string($conn, $_POST['no_telepon']);

      $updateSql = "UPDATE pengguna SET 
                nama = '$nama_lengkap',
                email = '$email',
                no_telepon = '$no_telepon',
                alamat = '$alamat',
                deskripsi_perusahaan = '$deskripsi_perusahaan'
                $kataSandiUpdate
                WHERE id = $userId";

      if (mysqli_query($conn, $updateSql)) {
        $success = "Profil berhasil diperbarui!";
      } else {
        $error .= "Gagal memperbarui profil: " . mysqli_error($conn);
      }
    }
  }
}

// === REFRESH DATA TERBARU ===
$sql = "SELECT nama, email, foto, no_telepon, alamat, deskripsi_perusahaan FROM pengguna WHERE id = $userId LIMIT 1";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
$nama_lengkap = $user['nama'];
$email = $user['email'];
$no_telepon = $user['no_telepon'] ?? '';
$alamat = $user['alamat'] ?? '';
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
  <?php if ($success): ?>
    <script>alert("<?= addslashes($success) ?>");</script>
  <?php endif; ?>
  <?php if ($error): ?>
    <script>alert("<?= addslashes($error) ?>");</script>
  <?php endif; ?>

  <div class="dashboard-wadah">
    <div class="sidebar-overlay"></div>
    <aside class="dashboard-sidebar">
      <div class="dashboard-sidebar-header">MAGANG ID</div>
      <nav class="dashboard-navigasi">
        <a href="dashboard_perusahaan/../dashboard_perusahaan.php" class="dashboard-link">
          <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
        </a>
        <div class="dashboard-judul-seksi">Menu Utama</div>
        <a href="dashboard_perusahaan/../lowongan.php" class="dashboard-link">
          <i class="fas fa-list-alt"></i> <span>Lowongan</span>
        </a>
        <a href="dashboard_perusahaan/../pendaftar.php" class="dashboard-link">
          <i class="fas fa-user-friends"></i> <span>Pendaftar</span>
        </a>
        <div class="dashboard-judul-seksi">Lainnya</div>
        <a href="dashboard_perusahaan/../profil_perusahaan.php" class="dashboard-link" style="color: white">
          <i class="fas fa-user"></i> <span>Profil</span>
        </a>
        <a href="../logout.php" class="dashboard-link"
          onclick="return confirm('Yakin ingin keluar dari halaman dashboard??');">
          <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
        </a>
      </nav>
    </aside>

    <div class="dashboard-konten-utama">
      <header class="dashboard-header-atas">
        <button class="hamburger-menu">
          <i class="fas fa-bars"></i>
        </button>
        <div class="dashboard-judul_halaman">
          <h3>Profil Perusahaan</h3>
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
            <a href="../logout.php" onclick="return confirm('Yakin ingin keluar dari halaman dashboard??');">
              <i class="fas fa-sign-out-alt"></i> Logout
            </a>
          </div>
        </details>
      </header>

      <main class="konten" role="main">
        <div class="konten-grid">
          <!-- FORM FOTO PROFIL -->
          <section class="kartu-foto" aria-label="Foto Profil">
            <?php if (!empty($user['foto'])): ?>
              <img src="../uploads/profil/perusahaan/<?= htmlspecialchars($user['foto']) ?>" alt="Foto Profil"
                class="foto-profil" width="160" height="160" loading="lazy" />
            <?php else: ?>
              <img src="../assets/default_profil.jpg" alt="Default Avatar" class="foto-profil" width="160" height="160"
                loading="lazy" />
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
              <p class="peringatan-foto">Ubah foto profil dengan memilih file di bawah</p>
              <div class="row-upload">
                <input type="file" name="profile_foto" accept="image/*" required>
                <button type="submit" name="submit_foto" class="tombol-update">
                  <i class="fas fa-sync"></i> Update Foto
              </div>
            </form>
          </section>

          <form class="form-profil" method="post" novalidate>
            <div class="form-grup">
              <label for="nama_lengkap" class="label-form">Nama Perusahaan</label>
              <input type="text" id="nama_lengkap" name="nama_lengkap" class="input-teks"
                value="<?= htmlspecialchars($nama_lengkap) ?>" required>
            </div>
            <div class="form-grup">
              <label for="email" class="label-form">Email</label>
              <input type="email" id="email" name="email" class="input-teks" value="<?= htmlspecialchars($email) ?>"
                required>
            </div>
            <div class="form-grup">
              <label for="no_telepon" class="label-form">Nomor Telepon</label>
              <input type="text" id="no_telepon" name="no_telepon" class="input-teks"
                value="<?= htmlspecialchars($no_telepon) ?>">
            </div>
            <div class="form-grup">
              <label for="alamat" class="label-form">Alamat</label>
              <textarea id="alamat" name="alamat" class="input-teks" rows="3"
                required><?= htmlspecialchars($alamat) ?></textarea>
            </div>
            <div class="form-grup">
              <label for="deskripsi_perusahaan" class="label-form">Deskripsi Perusahaan</label>
              <textarea id="deskripsi_perusahaan" name="deskripsi_perusahaan" class="input-teks"
                rows="5"><?= htmlspecialchars($user['deskripsi_perusahaan'] ?? '') ?></textarea>
            </div>
            <div class="form-grup">
              <label for="kata_sandi_baru" class="label-form">Kata Sandi Baru</label>
              <input type="password" id="kata_sandi_baru" name="kata_sandi_baru" class="input-teks">
            </div>
            <div class="form-grup">
              <label for="konfirmasi_kata_sandi" class="label-form">Konfirmasi Kata Sandi Baru</label>
              <input type="password" id="konfirmasi_kata_sandi" name="konfirmasi_kata_sandi" class="input-teks">
            </div>
            <button type="submit" name="submit_data" class="tombol-update">
              <i class="fas fa-sync"></i> Update Profil
            </button>
          </form>
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