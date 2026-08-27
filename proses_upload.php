<?php
// proses_upload.php - Proses Upload File
session_start();

// Cek login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'guru') {
    header('Location: login.php');
    exit;
}

include_once 'config/database.php';

// Cek apakah form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Ambil data dari form
    $judul = escape($_POST['judul']);
    $deskripsi = escape($_POST['deskripsi']);
    $jenis = escape($_POST['jenis']);
    $id_mapel = (int)$_POST['id_mapel'];
    $id_kelas = (int)$_POST['id_kelas'];
    $semester = escape($_POST['semester']);
    $tahun_ajaran = escape($_POST['tahun_ajaran']);
    $id_guru = $_SESSION['user_id'];

    // Validasi file
    if ($_FILES['file_dokumen']['error'] != 0) {
        header('Location: upload.php?error=Gagal upload file');
        exit;
    }

    $file = $_FILES['file_dokumen'];
    $allowed_types = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'mp4', 'zip', 'rar'];
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_ext, $allowed_types)) {
        header('Location: upload.php?error=Format file tidak didukung');
        exit;
    }

    // Cek ukuran file (max 50MB)
    if ($file['size'] > 50 * 1024 * 1024) {
        header('Location: upload.php?error=Ukuran file maksimal 50MB');
        exit;
    }

    // Buat folder berdasarkan jenis
    $upload_dir = "public/uploads/perangkat/$jenis/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Generate nama file unik
    $new_filename = date('Ymd_His') . '_' . uniqid() . '.' . $file_ext;
    $file_path = $upload_dir . $new_filename;

    // Pindahkan file
    if (move_uploaded_file($file['tmp_name'], $file_path)) {

        // Upload thumbnail (opsional)
        $thumbnail = null;
        if ($_FILES['thumbnail']['error'] == 0) {
            $thumb_ext = strtolower(pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION));
            if (in_array($thumb_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                $thumb_filename = date('Ymd_His') . '_thumb.' . $thumb_ext;
                $thumb_path = "public/uploads/thumbnails/$thumb_filename";
                if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $thumb_path)) {
                    $thumbnail = $thumb_path;
                }
            }
        }

        // Simpan ke database (status: draft)
        $sql = "INSERT INTO perangkat (
            judul, deskripsi, jenis, file_path, thumbnail,
            semester, tahun_ajaran, id_mapel, id_kelas, id_guru,
            status, created_at
        ) VALUES (
            '$judul', '$deskripsi', '$jenis', '$file_path', '$thumbnail',
            '$semester', '$tahun_ajaran', $id_mapel, $id_kelas, $id_guru,
            'draft', NOW()
        )";

        if (query($sql)) {
            $id_perangkat = lastId();
            
            // Kirim notifikasi ke Kepala Sekolah (bisa via email atau database)
            // Untuk sekarang, kita langsung ubah status jadi pending_kepsek
            $sql_update = "UPDATE perangkat SET status = 'pending_kepsek' WHERE id = $id_perangkat";
            query($sql_update);

            // Catat riwayat
            $sql_riwayat = "INSERT INTO riwayat_status (id_perangkat, id_user, status_awal, status_akhir, catatan) 
                            VALUES ($id_perangkat, $id_guru, 'draft', 'pending_kepsek', 'Guru mengirim dokumen ke Kepala Sekolah')";
            query($sql_riwayat);

            header('Location: upload.php?success=1');
        } else {
            header('Location: upload.php?error=Gagal menyimpan ke database');
        }
    } else {
        header('Location: upload.php?error=Gagal upload file');
    }
} else {
    header('Location: upload.php');
}
?>