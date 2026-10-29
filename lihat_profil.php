<?php
include 'koneksi.php';

// Ambil ID dari parameter dan validasi
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Ambil data perusahaan
$query = "SELECT * FROM pengguna WHERE id = ? AND peran = 'perusahaan'";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header('Location: index.php');
    exit;
}

$perusahaan = mysqli_fetch_assoc($result);

// Hitung lowongan aktif
$q1 = "SELECT COUNT(*) FROM lowongan WHERE id_pengguna = ? AND status = 'aktif'";
$stmt1 = mysqli_prepare($conn, $q1);
mysqli_stmt_bind_param($stmt1, "i", $id);
mysqli_stmt_execute($stmt1);
mysqli_stmt_bind_result($stmt1, $lowongan_aktif);
mysqli_stmt_fetch($stmt1);
mysqli_stmt_close($stmt1);

// Hitung total pelamar
$q2 = "SELECT COUNT(*) FROM pendaftar p 
        JOIN lowongan l ON p.id_lowongan = l.id 
        WHERE l.id_pengguna = ?";
$stmt2 = mysqli_prepare($conn, $q2);
mysqli_stmt_bind_param($stmt2, "i", $id);
mysqli_stmt_execute($stmt2);
mysqli_stmt_bind_result($stmt2, $total_pelamar);
mysqli_stmt_fetch($stmt2);
mysqli_stmt_close($stmt2);

// Hitung pelamar diterima
$q3 = "SELECT COUNT(*) FROM pendaftar p 
        JOIN lowongan l ON p.id_lowongan = l.id 
        WHERE l.id_pengguna = ? AND p.status_lamaran = 'diterima'";
$stmt3 = mysqli_prepare($conn, $q3);
mysqli_stmt_bind_param($stmt3, "i", $id);
mysqli_stmt_execute($stmt3);
mysqli_stmt_bind_result($stmt3, $magang_diterima);
mysqli_stmt_fetch($stmt3);
mysqli_stmt_close($stmt3);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil <?php echo htmlspecialchars($perusahaan['nama']); ?> - Magang ID</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <section id="profilPerusahaan">
        <div class="kontainer-lihat-profil">
            <div class="detail-nav">
                <a href="javascript:history.back()" class="btn-kembali">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="kartu-profil">
                <div class="foto-profil-perusahaan">
                    <?php if (!empty($perusahaan['foto'])): ?>
                        <img src="uploads/profil/perusahaan/<?php echo htmlspecialchars($perusahaan['foto']); ?>"
                            alt="Logo <?php echo htmlspecialchars($perusahaan['nama']); ?>">
                    <?php else: ?>
                        <i class="fas fa-building default-icon"></i>
                    <?php endif; ?>
                </div>

                <div class="info-profil">
                    <h2 class="nama-perusahaan"><?php echo htmlspecialchars($perusahaan['nama']); ?></h2>

                    <div class="info-grid">
                        <div class="info-item-profil">
                            <div class="info-label">
                                <i class="fas fa-map-marker-alt info-icon"></i>
                                Alamat
                            </div>
                            <div class="info-value">
                                <?php echo !empty($perusahaan['alamat']) ? htmlspecialchars($perusahaan['alamat']) : 'Tidak tersedia'; ?>
                            </div>
                        </div>
                        <div class="info-item-profil">
                            <div class="info-label">
                                <i class="fas fa-envelope info-icon"></i>
                                Email
                            </div>
                            <div class="info-value"><?php echo htmlspecialchars($perusahaan['email']); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deskripsi Perusahaan -->
            <?php if (!empty($perusahaan['deskripsi_perusahaan'])): ?>
                <div class="deskripsi-perusahaan">
                    <h2>
                        <i class="fas fa-info-circle"></i>
                        Tentang Perusahaan
                    </h2>
                    <div>
                        <?php echo nl2br(htmlspecialchars($perusahaan['deskripsi_perusahaan'])); ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Statistik Section -->
            <div class="statistik-section">
                <div class="kartu-statistik">
                    <div class="angka-statistik"><?php echo $lowongan_aktif; ?></div>
                    <div class="label-statistik">Lowongan Aktif</div>
                </div>
                <div class="kartu-statistik">
                    <div class="angka-statistik"><?php echo $total_pelamar; ?></div>
                    <div class="label-statistik">Total Pelamar</div>
                </div>
                <div class="kartu-statistik">
                    <div class="angka-statistik"><?php echo $magang_diterima; ?></div>
                    <div class="label-statistik">Magang Diterima</div>
                </div>
            </div>
            <div class="additional-info">
                <div class="info-footer">
                    <small class="profil-perusahaan-footer">
                        <i class="fas fa-calendar-alt"></i>
                        Bergabung pada : <?php echo date('d F Y', strtotime($perusahaan['tanggal_dibuat'])); ?>
                    </small>
                </div>
            </div>
        </div>
    </section>
</body>

</html>