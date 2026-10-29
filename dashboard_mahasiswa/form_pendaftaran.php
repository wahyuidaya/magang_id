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
$sql = "SELECT nama, foto, jenis_kelamin FROM pengguna WHERE id = $userId LIMIT 1";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Cek apakah user sudah login dan perannya mahasiswa
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'mahasiswa') {
    // Jika belum login, arahkan ke halaman login
    header("Location: login_user/login_user/login.php?pesan=login_dulu");
    exit();
}

// Cek parameter id dari GET (untuk navigasi) atau POST (untuk form submission)
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = (int) $_GET['id']; // Cast ke integer untuk keamanan
} elseif (isset($_POST['id']) && !empty($_POST['id'])) {
    $id = (int) $_POST['id']; // Cast ke integer untuk keamanan
} else {
    echo "<script>
        alert('ID lowongan tidak ditemukan!');
        window.location.href='perusahaan.php';
    </script>";
    exit();
}

// Validasi tambahan - pastikan ID adalah angka positif
if ($id <= 0) {
    echo "<script>
        alert('ID lowongan tidak valid!');
        window.location.href='perusahaan.php';
    </script>";
    exit();
}

// Optional: Cek apakah lowongan dengan ID tersebut ada di database
include '../koneksi.php';

$check_sql = "SELECT id, judul_lowongan, nama_perusahaan FROM lowongan WHERE id = $id";
$check_result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($check_result) == 0) {
    echo "<script>
        alert('Lowongan tidak ditemukan!');
        window.location.href='perusahaan.php';
    </script>";
    exit();
}

$lowongan_data = mysqli_fetch_assoc($check_result);
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
                    <h3>Form Pendaftaran</h3>
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
                        <a href="../logout.php"
                            onclick="return confirm('Yakin ingin keluar dari halaman dashboard??');">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </details>
            </header>
            <main class="konten-utama-form_pendaftaran" role="main">
                <section class="area-konten" aria-labelledby="judul-form-pendaftaran">
                    <form class="form-pendaftaran" method="post" action="dashboard_mahasiswa/../proses_pendaftaran.php"
                        enctype="multipart/form-data" autocomplete="off" novalidate>
                        <input type="hidden" name="id" value="<?php echo $id; ?>" />
                        <a href="perusahaan.php" class="tombol-kembali">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <div class="grup-input">
                            <label for="nama_lengkap">Nama</label>
                            <input type="text" id="nama_lengkap" name="nama_lengkap" tabindex="1"
                                value="<?= htmlspecialchars($user['nama']) ?>" readonly />
                        </div>

                        <div class="grup-input">
                            <label for="jurusan">Jurusan</label>
                            <input type="text" id="jurusan" name="jurusan" tabindex="2" required />
                        </div>

                        <div class="grup-input">
                            <label for="perguruan_tinggi">Perguruan Tinggi</label>
                            <input type="text" id="perguruan_tinggi" name="perguruan_tinggi" tabindex="3" required />
                        </div>

                        <div class="grup-input">
                            <label for="jenis_kelamin">Jenis Kelamin</label>
                            <input type="text" id="jenis_kelamin" name="jenis_kelamin" tabindex="4"
                                value="<?= htmlspecialchars($user['jenis_kelamin']) ?>" readonly />
                        </div>

                        <div class="grup-input">
                            <label for="cv">Upload CV (PDF)</label>
                            <input type="file" id="cv" name="cv" accept=".pdf" tabindex="5" required />
                        </div>

                        <div class="grup-tombol">
                            <button type="submit" tabindex="6" class="tombol-daftar">
                                <i class='fas fa-user-plus'></i> Daftar
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