<?php
session_start();
include 'koneksi.php';

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    echo "ID lowongan tidak valid.";
    exit;
}

// Query untuk mendapatkan detail lowongan
$query = "SELECT * FROM lowongan WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Lowongan tidak ditemukan.";
    exit;
}

$row = $result->fetch_assoc();

// Escape data untuk keamanan
$judul = htmlspecialchars($row['judul_lowongan']);
$nama_perusahaan = htmlspecialchars($row['nama_perusahaan']);
$posisi = htmlspecialchars($row['posisi']);
$persyaratan = nl2br(htmlspecialchars($row['persyaratan'])); // Preserve line breaks
$gambar = !empty($row['gambar']) ? 'uploads/lowongan/' . $row['gambar'] : "assets/default.jpg";
$status = $row['status'];

// Hitung sisa waktu berdasarkan batas_waktu
$sisa_waktu = "Sudah berakhir";
$batas_waktu_timestamp = strtotime($row['batas_waktu']);
$sekarang = time();

if ($batas_waktu_timestamp > $sekarang) {
    $selisih = $batas_waktu_timestamp - $sekarang;
    $hari = floor($selisih / (60 * 60 * 24));
    $jam = floor(($selisih % (60 * 60 * 24)) / (60 * 60));
    $sisa_waktu = $hari . " hari " . $jam . " jam";
    $is_active = true;
} else {
    $is_active = false;
}

// Cek apakah lowongan masih aktif (berdasarkan waktu DAN status)
$lowongan_aktif = $is_active && $status == 'aktif';

// Format tanggal batas waktu untuk tampilan
$batas_waktu_format = date('d F Y, H:i', strtotime($row['batas_waktu']));
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $judul; ?> - Detail Lowongan</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
</head>

<body>
    <div class="detail-page-wrapper">
        <div class="container-detail-lowongan">
            <!-- Header dengan navigasi kembali -->
            <div class="detail-nav">
                <a href="javascript:history.back()" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <!-- Image di paling atas -->
            <?php if (!empty($row['gambar'])): ?>
                <div class="job-image-header">
                    <img src="<?php echo $gambar; ?>" alt="<?php echo $judul; ?>" class="gambar-header"
                        onclick="openImageModal(this.src)" />
                </div>
            <?php endif; ?>

            <!-- Title -->
            <h1><?php echo $judul; ?></h1>

            <!-- Company badge dan tombol dalam satu baris -->
            <div class="company-header">
                <div class="company-badge">
                    <i class="fas fa-building"></i>
                    <?php echo $nama_perusahaan; ?>
                </div>
                <div class="button-detail-top">
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <?php if ($lowongan_aktif): ?>
                            <!-- User belum login dan lowongan masih aktif -->
                            <a href="#" class="btn-daftar btn-login" onclick="showLoginAlert(); return false;">
                                <i class="fas fa-user-plus"></i> Daftar
                            </a>
                        <?php else: ?>
                            <!-- User belum login dan lowongan sudah ditutup -->
                            <button class="btn-daftar btn-disabled" disabled>
                                <i class="fas fa-lock"></i> Ditutup
                            </button>
                        <?php endif; ?>
                    <?php elseif (!$lowongan_aktif): ?>
                        <!-- User sudah login tapi lowongan sudah ditutup -->
                        <button class="btn-daftar btn-disabled" disabled>
                            <i class="fas fa-lock"></i> Ditutup
                        </button>
                    <?php else: ?>
                        <!-- User sudah login dan lowongan masih aktif -->
                        <a href="dashboard_mahasiswa/form_pendaftaran.php?id=<?php echo $id; ?>"
                            class="btn-daftar btn-login"
                            onclick="return confirm('Apakah Anda yakin ingin mendaftar untuk lowongan ini?')">
                            <i class="fas fa-user-plus"></i> Daftar
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Detail Information - Full Width -->
            <div class="detail-header">
                <div class="detail-info">
                    <div class="info-grid-detail">
                        <div class="info-item">
                            <i class="fas fa-briefcase"></i>
                            <div>
                                <span class="label">Posisi</span>
                                <span class="value"><?php echo $posisi; ?></span>
                            </div>
                        </div>

                        <div class="info-item">
                            <i class="fas fa-calendar"></i>
                            <div>
                                <span class="label">Batas Waktu</span>
                                <span class="value"><?php echo $batas_waktu_format; ?></span>
                            </div>
                        </div>

                        <div class="info-item">
                            <i class="fas fa-hourglass-half"></i>
                            <div>
                                <span class="label">Sisa Waktu</span>
                                <span class="value <?php echo $lowongan_aktif ? 'active' : 'expired'; ?>">
                                    <?php echo $sisa_waktu; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Job Description -->
            <div class="job-description">
                <h3><i class="fas fa-list-ul"></i> DESKRIPSI LOWONGAN</h3>
                <div class="requirements-content">
                    <?php echo $persyaratan; ?>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="additional-info">
                <div class="info-footer">
                    <small>
                        <i class="fas fa-calendar-plus"></i>
                        Diposting pada: <?php echo date('d F Y, H:i', strtotime($row['dibuat_pada'])); ?>
                    </small>
                </div>
            </div>
        </div>
    </div>
    <script>
        function showLoginAlert() {
            if (confirm('Anda harus login terlebih dahulu untuk mendaftar. Login sekarang?')) {
                window.location.href = 'login_user/login.php';
            }
        }

        function openImageModal(src) {
            document.getElementById('imageModal').style.display = 'block';
            document.getElementById('modalImage').src = src;
        }

        function closeImageModal() {
            document.getElementById('imageModal').style.display = 'none';
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeImageModal();
            }
        });
    </script>
</body>

</html>