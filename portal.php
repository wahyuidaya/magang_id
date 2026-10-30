<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal Sistem Informasi — Magang ID</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      background-color: #F8FAFC;
      color: #1E293B;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    header {
      background-color: #1A365D;
      border-bottom: 1px solid rgba(144, 205, 244, 0.2);
      padding: 14px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 100;
    }
    .header-brand {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .brand-icon {
      width: 38px;
      height: 38px;
      border-radius: 9px;
      background-color: rgba(66, 153, 225, 0.2);
      color: #90CDF4;
      border: 1px solid rgba(144, 205, 244, 0.35);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
    }
    .brand-text h1 {
      color: #FFFFFF;
      font-size: 17px;
      font-weight: 700;
      letter-spacing: 0.5px;
      line-height: 1.1;
    }
    .brand-text span {
      color: #CBD5E1;
      font-size: 11px;
      display: block;
      margin-top: 3px;
    }
    .btn-portfolio {
      color: #90CDF4;
      background-color: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(144, 205, 244, 0.3);
      font-size: 12px;
      padding: 7px 14px;
      border-radius: 9999px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.2s;
    }
    .btn-portfolio:hover {
      background-color: rgba(255, 255, 255, 0.16);
      color: #FFFFFF;
    }
    .main-container {
      max-width: 1060px;
      margin: 0 auto;
      padding: 44px 20px;
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      width: 100%;
    }
    .hero-intro {
      text-align: center;
      max-width: 640px;
      margin: 0 auto 36px;
    }
    .badge-pill {
      display: inline-block;
      font-size: 12px;
      font-weight: 600;
      padding: 4px 14px;
      border-radius: 9999px;
      background-color: #EBF8FF;
      color: #2B6CB0;
      border: 1px solid #BEE3F8;
      margin-bottom: 12px;
      letter-spacing: 0.4px;
    }
    .hero-title {
      font-size: 28px;
      color: #0F172A;
      margin-bottom: 12px;
      font-weight: 700;
      line-height: 1.3;
    }
    .hero-subtitle {
      font-size: 14.5px;
      color: #475569;
      line-height: 1.6;
    }
    .portal-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(290px, 1fr));
      gap: 22px;
      margin-bottom: 24px;
    }
    .portal-card {
      background: #FFFFFF;
      border: 1px solid #E2E8F0;
      border-radius: 14px;
      padding: 24px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
      transition: all 0.2s ease;
    }
    .portal-card:hover {
      border-color: #3182CE;
      box-shadow: 0 8px 24px rgba(49, 130, 206, 0.12);
      transform: translateY(-2px);
    }
    .card-icon {
      width: 44px;
      height: 44px;
      border-radius: 10px;
      background: #EBF8FF;
      color: #2B6CB0;
      border: 1px solid #BEE3F8;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      margin-bottom: 16px;
    }
    .card-title {
      font-size: 18px;
      font-weight: 700;
      color: #0F172A;
      margin-bottom: 8px;
    }
    .card-desc {
      font-size: 13px;
      color: #475569;
      line-height: 1.5;
      margin-bottom: 16px;
    }
    .card-features {
      list-style: none;
      margin-bottom: 20px;
    }
    .card-features li {
      font-size: 12px;
      color: #334155;
      margin-bottom: 7px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .card-features li i {
      color: #3182CE;
      font-size: 11px;
    }
    .demo-box {
      background: #F8FAFC;
      border: 1px dashed #CBD5E1;
      border-radius: 8px;
      padding: 10px 12px;
      margin-bottom: 16px;
      font-size: 12px;
    }
    .demo-box-title {
      font-weight: 700;
      font-size: 11px;
      color: #2B6CB0;
      text-transform: uppercase;
      margin-bottom: 4px;
      display: flex;
      align-items: center;
      gap: 5px;
    }
    .demo-cred {
      font-family: monospace;
      color: #1E293B;
      font-size: 11.5px;
    }
    .btn-enter {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      width: 100%;
      padding: 11px;
      border-radius: 8px;
      background: #2563EB;
      color: #FFFFFF;
      font-weight: 600;
      font-size: 13px;
      text-decoration: none;
      transition: background 0.2s;
    }
    .btn-enter:hover {
      background: #1D4ED8;
    }
    .footer {
      border-top: 1px solid #E2E8F0;
      padding: 18px 24px;
      text-align: center;
      font-size: 12px;
      color: #64748B;
      background: #FFFFFF;
    }
  </style>
</head>
<body>

  <header>
    <div class="header-brand">
      <div class="brand-icon">
        <i class="fa-solid fa-graduation-cap"></i>
      </div>
      <div class="brand-text">
        <h1>Magang ID</h1>
        <span>Platform Portal Rekrutmen &amp; Seleksi Magang</span>
      </div>
    </div>
    <a href="https://wahyuidaya.github.io" target="_blank" rel="noopener noreferrer" class="btn-portfolio">
      <span>&larr; Portofolio Wahyu Idaya</span>
      <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 10px;"></i>
    </a>
  </header>

  <div class="main-container">
    <div class="hero-intro">
      <span class="badge-pill">🎓 PHP Native &amp; MySQL Fullstack Portfolio</span>
      <h2 class="hero-title">Selamat Datang di Portal Magang ID</h2>
      <p class="hero-subtitle">
        Platform rekrutmen interaktif 3 peran: eksplorasi dan pendaftaran lowongan bagi mahasiswa, publikasi &amp; seleksi pelamar oleh perusahaan mitra, serta moderasi terpadu oleh administrator.
      </p>
    </div>

    <div class="portal-grid">
      <!-- 1. Mahasiswa -->
      <div class="portal-card">
        <div>
          <div class="card-icon">
            <i class="fa-solid fa-user-graduate"></i>
          </div>
          <h3 class="card-title">Portal Mahasiswa</h3>
          <p class="card-desc">
            Cari posisi magang impian, daftarkan diri dengan unggah CV dokumen, dan lacak status lamaran secara langsung.
          </p>
          <ul class="card-features">
            <li><i class="fa-solid fa-check"></i> Eksplor katalog lowongan magang terbaru</li>
            <li><i class="fa-solid fa-check"></i> Unggah berkas CV &amp; data pelamar online</li>
            <li><i class="fa-solid fa-check"></i> Cetak kartu peserta pendaftaran digital</li>
            <li><i class="fa-solid fa-check"></i> Pantau pengumuman penerimaan mitra</li>
          </ul>
        </div>
        <div>
          <div class="demo-box">
            <div class="demo-box-title">
              <i class="fa-solid fa-key"></i> Kredensial Demo Mahasiswa
            </div>
            <div class="demo-cred">
              Email: <strong>mahasiswa@gmail.com</strong><br>
              Password: <strong>mahasiswa123</strong>
            </div>
          </div>
          <a href="login_user/login.php?demo=mahasiswa" class="btn-enter">
            <span>Masuk sebagai Mahasiswa</span>
            <i class="fa-solid fa-arrow-right"></i>
          </a>
        </div>
      </div>

      <!-- 2. Perusahaan -->
      <div class="portal-card">
        <div>
          <div class="card-icon" style="color: #059669; background: #ECFDF5; border-color: #A7F3D0;">
            <i class="fa-solid fa-building"></i>
          </div>
          <h3 class="card-title">Portal Perusahaan</h3>
          <p class="card-desc">
            Pasang lowongan magang baru, seleksi kualifikasi berkas pelamar, serta kirim status penerimaan resmi.
          </p>
          <ul class="card-features">
            <li><i class="fa-solid fa-check"></i> Pasang &amp; kelola lowongan magang perusahaan</li>
            <li><i class="fa-solid fa-check"></i> Tinjau daftar pelamar &amp; unduh CV pelamar</li>
            <li><i class="fa-solid fa-check"></i> Proses terima / tolak pelamar dengan notifikasi</li>
            <li><i class="fa-solid fa-check"></i> Cetak data rekapitulasi pelamar magang</li>
          </ul>
        </div>
        <div>
          <div class="demo-box">
            <div class="demo-box-title" style="color: #059669;">
              <i class="fa-solid fa-key"></i> Kredensial Demo Perusahaan
            </div>
            <div class="demo-cred">
              Email: <strong>codenix@gmail.com</strong><br>
              Password: <strong>perusahaan123</strong>
            </div>
          </div>
          <a href="login_user/login.php?demo=perusahaan" class="btn-enter" style="background: #059669;">
            <span>Masuk sebagai Perusahaan</span>
            <i class="fa-solid fa-arrow-right"></i>
          </a>
        </div>
      </div>

      <!-- 3. Admin -->
      <div class="portal-card">
        <div>
          <div class="card-icon" style="color: #4F46E5; background: #EEF2FF; border-color: #C7D2FE;">
            <i class="fa-solid fa-shield-halved"></i>
          </div>
          <h3 class="card-title">Portal Admin</h3>
          <p class="card-desc">
            Akses pengawasan menyeluruh terhadap ekosistem lowongan, moderasi instansi perusahaan, dan audit pengguna.
          </p>
          <ul class="card-features">
            <li><i class="fa-solid fa-check"></i> Pengawasan data master seluruh lowongan</li>
            <li><i class="fa-solid fa-check"></i> Verifikasi profil &amp; kredibilitas perusahaan</li>
            <li><i class="fa-solid fa-check"></i> Manajemen hak akses &amp; aktivasi pengguna</li>
            <li><i class="fa-solid fa-check"></i> Monitoring statistik lamaran aktif</li>
          </ul>
        </div>
        <div>
          <div class="demo-box">
            <div class="demo-box-title" style="color: #4F46E5;">
              <i class="fa-solid fa-key"></i> Kredensial Demo Admin
            </div>
            <div class="demo-cred">
              Username: <strong>Admin</strong><br>
              Password: <strong>admin123</strong>
            </div>
          </div>
          <a href="login_admin/login_admin.php?demo=admin" class="btn-enter" style="background: #4F46E5;">
            <span>Masuk sebagai Admin</span>
            <i class="fa-solid fa-arrow-right"></i>
          </a>
        </div>
      </div>
    </div>
  </div>

  <footer class="footer">
    &copy; 2025&ndash;2026 Magang ID &mdash; Portofolio Sistem Informasi Rekrutmen Magang oleh <strong>Wahyu Idaya</strong>.
  </footer>

</body>
</html>