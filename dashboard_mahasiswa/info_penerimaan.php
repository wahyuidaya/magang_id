<?php
session_start();
include '../koneksi.php';


// Cek login & role mahasiswa
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'mahasiswa') {
    header('Location: login_user/login_user/login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Ambil info pengguna
$sqlUser = "SELECT nama, foto FROM pengguna WHERE id = $userId LIMIT 1";
$resultUser = mysqli_query($conn, $sqlUser);
$user = mysqli_fetch_assoc($resultUser);

// === Pagination ===
$jumlahTampil = 10;
if (isset($_GET['jumlah_tampil'])) {
    $jumlahTampil = intval($_GET['jumlah_tampil']);
    if (!in_array($jumlahTampil, [10, 25, 50, 100])) {
        $jumlahTampil = 10;
    }
}

$cari = '';
$whereCari = '';
if (isset($_GET['cari_judul'])) {
    $cari = mysqli_real_escape_string($conn, $_GET['cari_judul']);
    if (!empty($cari)) {
        $whereCari = " AND l.judul_lowongan LIKE '%$cari%'";
    }
}

// Hitung total data
$sqlCount = "SELECT COUNT(*) AS total 
             FROM pendaftar p 
             JOIN lowongan l ON p.id_lowongan = l.id 
             WHERE p.id_pengguna = $userId AND p.status_lamaran = 'diterima'
             $whereCari";
$resultCount = mysqli_query($conn, $sqlCount);
$rowCount = mysqli_fetch_assoc($resultCount);
$totalData = $rowCount['total'];

// Halaman aktif
$totalHalaman = ($jumlahTampil > 0) ? ceil($totalData / $jumlahTampil) : 1;
$halamanAktif = isset($_GET['halaman']) ? intval($_GET['halaman']) : 1;
if ($halamanAktif < 1)
    $halamanAktif = 1;
if ($halamanAktif > $totalHalaman)
    $halamanAktif = $totalHalaman;

$offset = ($halamanAktif - 1) * $jumlahTampil;

// Ambil data (tambahkan posisi & tanggal diterima kalau ada di tabel lowongan)
$sql = "SELECT p.*, l.judul_lowongan, l.nama_perusahaan, l.posisi, p.tanggal_diterima
        FROM pendaftar p
        JOIN lowongan l ON p.id_lowongan = l.id
        WHERE p.id_pengguna = $userId 
        AND p.status_lamaran = 'diterima'
        $whereCari
        ORDER BY p.tanggal_daftar DESC
        LIMIT $jumlahTampil OFFSET $offset";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Hasil Lamaran | MAGANG ID</title>
    <link rel="stylesheet" href="../style.css?v=<?= time() ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
</head>

<body>
    <div class="dashboard-wadah">
        <div class="sidebar-overlay"></div>
        <aside class="dashboard-sidebar">
            <div class="dashboard-sidebar-header">MAGANG ID</div>
            <nav class="dashboard-navigasi">
                <a href="../dashboard_mahasiswa/dashboard_mahasiswa.php" class="dashboard-link" style="color: white">
                    <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
                </a>
                <div class="dashboard-judul-seksi">Menu Utama</div>
                <a href="perusahaan.php" class="dashboard-link">
                    <i class="fas fa-building"></i> <span>Perusahaan</span>
                </a>
                <a href="dashboard_mahasiswa/../lamaran_anda.php" class="dashboard-link">
                    <i class="fas fa-file-alt"></i> <span>Lamaran Anda</span>
                </a>
                <div class="dashboard-judul-seksi">Lainnya</div>
                <a href="dashboard_mahasiswa/../profil_mahasiswa.php" class="dashboard-link">
                    <i class="fas fa-user"></i> <span>Profil</span>
                </a>
                <a href="../logout.php" class="dashboard-link"
                    onclick="return confirm('Yakin ingin keluar dari halaman dashboard??');">
                    <i class="fas fa-sign-out-alt"></i> <span>Keluar</span>
                </a>
            </nav>
        </aside>

        <div class="dashboard-konten-utama">
            <header class="dashboard-header-atas">
                <button class="hamburger-menu">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="dashboard-judul_halaman">
                    <h3>Hasil Lamaran</h3>
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
            <main class="konten-utama-lamaran_anda">
                <section class="konten" aria-label="Data lamaran Anda">
                    <div class="kotak-tabel">
                        <a href="dashboard_mahasiswa.php" class="tombol-kembali">
                            <i class="fas fa-arrow-left"></i> Kembali
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
                                        <input type="text" name="cari_judul" value="<?= htmlspecialchars($cari) ?>"
                                            placeholder="Cari..." aria-label="Cari judul lowongan">
                                        <button type="submit"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="table-wrapper">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Judul Lowongan</th>
                                        <th>Perusahaan</th>
                                        <th>Posisi</th>
                                        <th>Tanggal Daftar</th>
                                        <th>Tanggal Diterima</th>
                                        <th>Opsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($result) > 0): ?>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($row['judul_lowongan']) ?></td>
                                                <td><?= htmlspecialchars($row['nama_perusahaan']) ?></td>
                                                <td><?= htmlspecialchars($row['posisi']) ?></td>
                                                <td><?= htmlspecialchars($row['tanggal_daftar']) ?></td>
                                                <td><?= htmlspecialchars($row['tanggal_diterima'] ?? $row['tanggal_daftar']) ?>
                                                </td>
                                                <td>
                                                    <a href="kartu_peserta.php?id_pendaftaran=<?= $row['id'] ?>" target="_blank"
                                                        class="tombol-cetak">
                                                        <i class='fas fa-print ikon'></i> Cetak Kartu
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6">Data tidak ditemukan.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="wadah-pagination">
                            <div class="info-pagination">
                                <?php
                                $mulai = ($totalData == 0) ? 0 : $offset + 1;
                                $akhir = $offset + mysqli_num_rows($result);
                                echo "Menampilkan $mulai sampai $akhir dari total $totalData lamaran";
                                ?>
                            </div>

                            <div class="kontrol-pagination">
                                <?php if ($halamanAktif > 1): ?>
                                    <a href="?halaman=<?= $halamanAktif - 1 ?>&jumlah_tampil=<?= $jumlahTampil ?>&cari_judul=<?= urlencode($cari) ?>"
                                        class="link-pagination">Sebelumnya</a>
                                <?php else: ?>
                                    <span class="link-pagination non-aktif">Sebelumnya</span>
                                <?php endif; ?>

                                <span class="nomor-halaman">
                                    <?= ($totalHalaman > 1) ? "$halamanAktif / $totalHalaman" : $halamanAktif ?>
                                </span>

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