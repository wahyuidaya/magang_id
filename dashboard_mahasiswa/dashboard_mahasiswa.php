<?php
session_start();
include '../koneksi.php';

// Pastikan pengguna sudah login dan memiliki role mahasiswa
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'mahasiswa') {
    header('Location: login_user/login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Ambil informasi pengguna terbaru dari database untuk foto dan nama
$sql = "SELECT nama, foto FROM pengguna WHERE id = $userId LIMIT 1";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

/* === Update status lowongan otomatis === */
$sqlUpdateStatus = "UPDATE lowongan SET status = 'nonaktif' 
                    WHERE batas_waktu < CURDATE() AND status = 'aktif'";
mysqli_query($conn, $sqlUpdateStatus);

/* === Hitung lowongan tersedia === */
$sqlLowongan = "SELECT COUNT(*) as total 
                FROM lowongan 
                WHERE status = 'aktif'";
$resultLowongan = mysqli_query($conn, $sqlLowongan);
$rowLowongan = mysqli_fetch_assoc($resultLowongan);
$totalLowongan = $rowLowongan['total'];


/* === Hitung total lamaran mahasiswa === */
$sqlLamaran = "SELECT COUNT(*) as total FROM pendaftar WHERE id_pengguna = $userId";
$resultLamaran = mysqli_query($conn, $sqlLamaran);
$rowLamaran = mysqli_fetch_assoc($resultLamaran);
$totalLamaran = $rowLamaran['total'];

/* === Hitung total hasil lamaran === */
$sqlNotifikasi = "SELECT COUNT(*) as total 
                  FROM pendaftar 
                  WHERE id_pengguna = $userId 
                  AND status_lamaran IN ('diterima')";
$resultNotifikasi = mysqli_query($conn, $sqlNotifikasi);
$rowNotifikasi = mysqli_fetch_assoc($resultNotifikasi);
$totalNotifikasi = $rowNotifikasi['total'];

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
                <a href="dashboard_mahasiswa/../dashboard_mahasiswa.php" class="dashboard-link" style="color: white">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <div class="dashboard-judul-seksi">Menu Utama</div>
                <a href="perusahaan.php" class="dashboard-link">
                    <i class="fas fa-building"></i>
                    <span>Perusahaan</span>
                </a>
                <a href="dashboard_mahasisw/../lamaran_anda.php" class="dashboard-link">
                    <i class="fas fa-file-alt"></i>
                    <span>Lamaran Anda</span>
                </a>
                <div class="dashboard-judul-seksi">Lainnya</div>
                <a href="profil_mahasiswa.php" class="dashboard-link">
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
                    <h3>Dashboard Mahasiswa</h3>
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
                        <a href="profil_mahasiswa.php">
                            <i class="fas fa-user"></i> Lihat Profil
                        </a>
                        <a href="profil_mahasiswa.php">
                            <i class="fas fa-lock"></i> Ubah Password
                        </a>
                        <a href="../logout.php"
                            onclick="return confirm('Yakin ingin keluar dari halaman dashboard??');">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </details>
            </header>
            <main class="dashboard-konten">
                <div class="dashboard-kotak-sambutan">
                    <span>Selamat Datang, <?= htmlspecialchars($user['nama']) ?></span>
                    <a href="perusahaan.php">
                        <button class="dashboard-tombol-cek">
                            <i class='fas fa-search'></i> Cek Lowongan
                        </button>
                    </a>
                </div>
                <div class="dashboard-grid-info">
                    <a href="perusahaan.php" class="dashboard-kotak-info-link">
                        <div class="dashboard-kotak-info">
                            <i class="fas fa-briefcase dashboard-ikon-info"></i>
                            <span><?= $totalLowongan ?> Lowongan Tersedia</span>
                        </div>
                    </a>

                    <a href="dashboard_mahasisw/../lamaran_anda.php" class="dashboard-kotak-info-link">
                        <div class="dashboard-kotak-info">
                            <i class="fas fa-paper-plane dashboard-ikon-info"></i>
                            <span><?= $totalLamaran ?> Lamaran Terkirim</span>
                        </div>
                    </a>

                    <a href="info_penerimaan.php" class="dashboard-kotak-info-link">
                        <div class="dashboard-kotak-info">
                            <i class="fas fa-bell dashboard-ikon-info"></i>
                            <span><?= $totalNotifikasi ?> Info Penerimaan</span>
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