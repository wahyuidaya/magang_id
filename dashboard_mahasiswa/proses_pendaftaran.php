<?php
session_start();
include '../koneksi.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ===== [1] Posting Lowongan oleh Perusahaan =====
    if (isset($_POST['judul_lowongan']) && isset($_FILES['gambar'])) {
        $judul_lowongan = mysqli_real_escape_string($conn, $_POST['judul_lowongan']);
        $nama_perusahaan = mysqli_real_escape_string($conn, $_POST['nama_perusahaan']);
        $posisi = mysqli_real_escape_string($conn, $_POST['posisi']);
        $batas_waktu = mysqli_real_escape_string($conn, $_POST['batas_waktu']);
        $persyaratan = mysqli_real_escape_string($conn, $_POST['persyaratan']);

        // Upload Gambar
        $uploadDir = 'uploads/lowongan/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileTmpPath = $_FILES['gambar']['tmp_name'];
        $fileName = basename($_FILES['gambar']['name']);
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = time() . '_' . $fileName;
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $sql = "INSERT INTO lowongan (judul_lowongan, nama_perusahaan, posisi, batas_waktu, persyaratan, gambar)
                VALUES ('$judul_lowongan', '$nama_perusahaan', '$posisi', '$batas_waktu', '$persyaratan', '$newFileName')";

                if (mysqli_query($conn, $sql)) {
                    header('Location: dashboard_perusahaan/../lowongan.php');
                    exit();
                } else {
                    echo "Gagal menambahkan lowongan: " . mysqli_error($conn);
                }
            } else {
                echo "Gagal mengupload gambar.";
            }
        } else {
            echo "Format gambar tidak didukung.";
        }
    }

    // ===== [2] Pendaftaran Mahasiswa ke Lowongan =====
    else if (isset($_POST['nama_lengkap']) && isset($_POST['id'])) {

        // === VALIDASI WAJIB ISI ===
        if (empty($_POST['nama_lengkap']) || empty($_POST['jurusan']) || empty($_POST['perguruan_tinggi']) || empty($_POST['jenis_kelamin']) || empty($_FILES['cv']['name'])) {
            echo "<script>
            alert('Gagal! Semua kolom wajib diisi termasuk upload CV.');
            window.history.back();
        </script>";
            exit();
        }
        $id_pengguna = $_SESSION['user_id'];
        $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
        $jurusan = mysqli_real_escape_string($conn, $_POST['jurusan']);
        $perguruan_tinggi = mysqli_real_escape_string($conn, $_POST['perguruan_tinggi']);
        $jenis_kelamin = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);
        $id = mysqli_real_escape_string($conn, $_POST['id']);

        // === CEK DUPLIKAT PENDAFTARAN ===
        $cek_sql = "SELECT COUNT(*) AS total FROM pendaftar 
                    WHERE id_pengguna = '$id_pengguna' AND id_lowongan = '$id'";
        $cek_result = mysqli_query($conn, $cek_sql);
        $cek_row = mysqli_fetch_assoc($cek_result);

        if ($cek_row['total'] > 0) {
            echo "<script>
                alert('Gagal! Kamu sudah mendaftar lowongan ini.');
                window.location.href='perusahaan.php';
            </script>";
            exit();
        }

        $kode_pendaftaran = strtoupper(substr(md5(time()), 0, 6));

        // === Proses upload CV ===
        $cvFileName = '';
        if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
            $cvTmpPath = $_FILES['cv']['tmp_name'];
            $cvOriginalName = basename($_FILES['cv']['name']);
            $cvExtension = strtolower(pathinfo($cvOriginalName, PATHINFO_EXTENSION));
            $allowedExtensions = ['pdf'];
            $allowedMime = ['application/pdf'];

            $fileMime = mime_content_type($cvTmpPath);

            if (!in_array($cvExtension, $allowedExtensions)) {
                echo "Format CV harus PDF saja.";
                exit();
            }

            if (!in_array($fileMime, $allowedMime)) {
                echo "Tipe file tidak valid. Hanya file PDF yang diperbolehkan.";
                exit();
            }

            $uploadDir = '../uploads/cv/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $cvFileName = time() . '_' . preg_replace('/[^a-zA-Z0-9_.]/', '', $cvOriginalName);
            $destPath = $uploadDir . $cvFileName;

            if (!move_uploaded_file($cvTmpPath, $destPath)) {
                echo "Gagal mengupload file CV.";
                exit();
            }
        } else {
            echo "File CV wajib diunggah.";
            exit();
        }

        $sql = "INSERT INTO pendaftar 
            (id_pengguna, nama_lengkap, jurusan, perguruan_tinggi, jenis_kelamin, kode_pendaftaran, id_lowongan, cv_file)
            VALUES 
            ('$id_pengguna', '$nama_lengkap', '$jurusan', '$perguruan_tinggi', '$jenis_kelamin', '$kode_pendaftaran', '$id', '$cvFileName')";

        if (mysqli_query($conn, $sql)) {
            echo "<script>
                alert('Pendaftaran berhasil!');
                window.location.href='perusahaan.php';
            </script>";
            exit();
        } else {
            echo "Gagal mendaftar: " . mysqli_error($conn);
        }
    }

}

mysqli_close($conn);
?>