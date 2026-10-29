<?php
session_start();
include '../koneksi.php';


// Pastikan user login
if (!isset($_SESSION['user_id'])) {
  header('Location: login_user/login_user/login.php');
  exit();
}

// Ambil ID pendaftaran dari URL
$id_pendaftaran = isset($_GET['id_pendaftaran']) ? intval($_GET['id_pendaftaran']) : 0;

// Query data pendaftar JOIN user
$sql = "SELECT 
            p.kode_pendaftaran,
            p.jurusan,
            p.perguruan_tinggi,
            p.jenis_kelamin,
            u.nama AS nama_pengguna,
            u.foto AS foto_pengguna,
            l.nama_perusahaan,
            l.judul_lowongan
        FROM pendaftar p
        JOIN pengguna u ON p.id_pengguna = u.id
        JOIN lowongan l ON p.id_lowongan = l.id
        WHERE p.id = '$id_pendaftaran'
        LIMIT 1";

$result = mysqli_query($conn, $sql);
$pendaftar = mysqli_fetch_assoc($result);

if (!$pendaftar) {
  die("Pendaftar tidak ditemukan.");
}

// Data dari DB
$kode_pendaftaran = $pendaftar['kode_pendaftaran'];
$nama = $pendaftar['nama_pengguna'];
$jurusan = $pendaftar['jurusan'] ?: '-';
$asal_sekolah = $pendaftar['perguruan_tinggi'] ?: '-';
$jenis_kelamin = $pendaftar['jenis_kelamin'] ?: '-';
$nama_perusahaan = $pendaftar['nama_perusahaan'] ?: '-';
$judul_lowongan = $pendaftar['judul_lowongan'] ?: '-';

$foto = !empty($pendaftar['foto_pengguna']) ? $pendaftar['foto_pengguna'] : 'default.jpg';
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kartu Peserta | MAGANG ID</title>
  <link rel="stylesheet" href="../style.css?v=<?= time(); ?>">
</head>

<body class="body-cetak_data">
  <div class="header-cetak_data">
    <h1>MAGANG ID</h1>
    <h2>Kartu Peserta</h2>
    <p class="sub-info">Silakan cetak & bawa kartu ini saat melakukan daftar ulang</p>
  </div>

  <div class="kartu-peserta-container">
    <div class="keterangan-lowongan">
      <p><strong><?= htmlspecialchars($nama_perusahaan) ?></strong></p>
      <p><strong><?= htmlspecialchars($judul_lowongan) ?></strong></p>
    </div>
    <div class="kartu-peserta-header">
      <div class="kode-pendaftaran">
        Kode Pendaftaran<br>
        <?= htmlspecialchars($kode_pendaftaran) ?>
      </div>
      <div class="foto-peserta">
        <img src="../uploads/profil/mahasiswa/<?= htmlspecialchars($foto) ?>" alt="Foto Profil">
      </div>
    </div>

    <div class="biodata-peserta">
      <p><strong>Nama</strong> : <?= htmlspecialchars($nama) ?></p>
      <p><strong>Jurusan</strong> : <?= htmlspecialchars($jurusan) ?></p>
      <p><strong>Perguruan Tinggi</strong> : <?= htmlspecialchars($asal_sekolah) ?></p>
      <p><strong>Jenis Kelamin</strong> : <?= htmlspecialchars($jenis_kelamin) ?></p>
    </div>

    <div class="catatan-peserta">
      Kartu ini wajib dibawa saat melakukan daftar ulang
    </div>
  </div>

  <script>
    window.onload = function () {
      window.print();
    };

    window.onafterprint = function () {
      // Redirect saja, tanpa close
      setTimeout(function () {
        window.location.href = 'dashboard_mahasiswa/../lamaran_anda.php';
      }, 1000);
    };
  </script>
</body>

</html>