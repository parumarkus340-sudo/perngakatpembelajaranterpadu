<?php
// upload.php - Halaman Upload Perangkat Pembelajaran
session_start();

// Cek login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'guru') {
    header('Location: login.php');
    exit;
}

include_once 'config/database.php';

// Ambil data mapel dan kelas
$mapel = fetchAll("SELECT * FROM mata_pelajaran ORDER BY nama_mapel");
$kelas = fetchAll("SELECT * FROM kelas ORDER BY jenjang, CAST(nama_kelas AS UNSIGNED)");

// Proses upload
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $jenis = mysqli_real_escape_string($conn, $_POST['jenis']);
    $id_mapel = (int)$_POST['id_mapel'];
    $id_kelas = (int)$_POST['id_kelas'];
    $semester = mysqli_real_escape_string($conn, $_POST['semester']);
    $tahun_ajaran = mysqli_real_escape_string($conn, $_POST['tahun_ajaran']);
    $id_guru = $_SESSION['user_id'];
    
    if ($_FILES['file_dokumen']['error'] != 0) {
        $error = "Gagal upload file. Error code: " . $_FILES['file_dokumen']['error'];
    } else {
        $file = $_FILES['file_dokumen'];
        $allowed_types = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'mp4', 'zip', 'rar'];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($file_ext, $allowed_types)) {
            $error = "Format file tidak didukung. Gunakan: " . implode(', ', $allowed_types);
        } elseif ($file['size'] > 50 * 1024 * 1024) {
            $error = "Ukuran file maksimal 50MB";
        } else {
            $upload_dir = "public/uploads/perangkat/$jenis/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $new_filename = date('Ymd_His') . '_' . uniqid() . '.' . $file_ext;
            $file_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($file['tmp_name'], $file_path)) {
                $sql = "INSERT INTO perangkat (
                    judul, deskripsi, jenis, file_path, 
                    semester, tahun_ajaran, id_mapel, id_kelas, id_guru,
                    status, created_at
                ) VALUES (
                    '$judul', '$deskripsi', '$jenis', '$file_path',
                    '$semester', '$tahun_ajaran', $id_mapel, $id_kelas, $id_guru,
                    'pending_kepsek', NOW()
                )";
                
                if (query($sql)) {
                    $id_perangkat = lastId();
                    query("
                        INSERT INTO riwayat_status (id_perangkat, id_user, status_awal, status_akhir, catatan) 
                        VALUES ($id_perangkat, $id_guru, 'draft', 'pending_kepsek', 'Guru mengirim dokumen ke Kepala Sekolah')
                    ");
                    $success = "✅ Dokumen berhasil diupload! Menunggu persetujuan Kepala Sekolah.";
                } else {
                    $error = "Gagal menyimpan ke database: " . mysqli_error($conn);
                }
            } else {
                $error = "Gagal upload file";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Perangkat Pembelajaran</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/website_perangkat/public/css/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <div>
                <h1><i class="fas fa-upload"></i> Upload Perangkat Pembelajaran</h1>
                <p>Bagikan perangkat pembelajaran Anda</p>
            </div>
            <div class="badge-user">
                <i class="fas fa-user"></i> <?php echo $_SESSION['name']; ?>
            </div>
        </header>

         <!-- ===== NAVIGASI PAKAI NAVBAR.PHP ===== -->
        <?php include_once 'navbar.php'; ?>
        <!-- ===================================== -->

        <main>
            <div class="upload-container">
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                <?php if (isset($error)): ?>
                    <div class="alert alert-error">❌ <?php echo $error; ?></div>
                <?php endif; ?>

                <div class="info-box">
                    <h4>📋 Alur Upload Dokumen</h4>
                    <p>1. Anda upload dokumen → Status: <strong>Draft</strong></p>
                    <p>2. Dokumen dikirim ke Kepala Sekolah → Status: <strong>Menunggu Persetujuan Kepsek</strong></p>
                    <p>3. Kepala Sekolah koreksi & tandatangan → Status: <strong>Menunggu Pengawas</strong></p>
                    <p>4. Pengawas supervisi & tandatangan → Status: <strong>Terverifikasi ✅</strong></p>
                </div>

                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>📝 Judul Perangkat <span class="required">*</span></label>
                        <input type="text" name="judul" required placeholder="Contoh: RPP Matematika Kelas 10 Semester 1">
                    </div>

                    <div class="form-group">
                        <label>📄 Deskripsi</label>
                        <textarea name="deskripsi" placeholder="Deskripsikan perangkat pembelajaran ini..."></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>📋 Jenis Perangkat <span class="required">*</span></label>
                            <select name="jenis" required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="RPP">RPP (Rencana Pelaksanaan Pembelajaran)</option>
                                <option value="Modul">Modul Pembelajaran</option>
                                <option value="PPT">PPT / Slide Presentasi</option>
                                <option value="Video">Video Pembelajaran</option>
                                <option value="Soal">Soal / Evaluasi</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>📚 Mata Pelajaran <span class="required">*</span></label>
                            <select name="id_mapel" required>
                                <option value="">-- Pilih Mapel --</option>
                                <?php foreach ($mapel as $m): ?>
                                    <option value="<?php echo $m['id']; ?>">
                                        <?php echo $m['kode_mapel']; ?> - <?php echo $m['nama_mapel']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>🏫 Kelas <span class="required">*</span></label>
                            <select name="id_kelas" required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php foreach ($kelas as $k): ?>
                                    <option value="<?php echo $k['id']; ?>">
                                        Kelas <?php echo $k['nama_kelas']; ?> (<?php echo $k['jenjang']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>📖 Semester <span class="required">*</span></label>
                            <select name="semester" required>
                                <option value="">-- Pilih Semester --</option>
                                <option value="1">Semester 1 (Ganjil)</option>
                                <option value="2">Semester 2 (Genap)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>📅 Tahun Ajaran <span class="required">*</span></label>
                        <input type="text" name="tahun_ajaran" required placeholder="Contoh: 2025/2026" value="2025/2026">
                    </div>

                    <div class="form-group">
                        <label>📎 File Perangkat <span class="required">*</span></label>
                        <input type="file" name="file_dokumen" required accept=".pdf,.doc,.docx,.ppt,.pptx,.mp4,.zip,.rar">
                        <div class="help-text">Format: PDF, DOC, PPT, MP4, ZIP, RAR. Maksimal: 50MB</div>
                    </div>

                    <button type="submit" class="btn-submit"><i class="fas fa-upload"></i> Upload & Kirim ke Kepala Sekolah</button>
                </form>
            </div>
        </main>

        <footer class="footer">
            <p>&copy; <?php echo date('Y'); ?> Pusat Perangkat Pembelajaran</p>
        </footer>
    </div>
</body>
</html>