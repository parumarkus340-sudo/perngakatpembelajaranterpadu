<?php
// perangkat_upload.php - Upload Dokumen Perangkat Pembelajaran (Google Drive Link)
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'guru') {
    header('Location: login.php');
    exit;
}

include_once 'config/database.php';

$id_guru = $_SESSION['user_id'];
$sekolah_id = $_SESSION['sekolah_id'] ?? 0;

// Ambil data
$kelas = fetchAll("SELECT * FROM kelas ORDER BY jenjang, nama_kelas");
$mapel = fetchAll("SELECT * FROM mata_pelajaran ORDER BY nama_mapel");

$jenis_dokumen = [
    'cp' => '📋 CP (Capaian Pembelajaran)',
    'atp' => '📊 ATP (Alur Tujuan Pembelajaran)',
    'prota' => '📅 PROTA (Program Tahunan)',
    'promes' => '📆 PROMES (Program Semester)',
    'jurnal' => '📓 Jurnal Mengajar',
    'rpp' => '📄 RPP (Rencana Pelaksanaan Pembelajaran)',
    'modul' => '📘 Modul Ajar',
    'penilaian' => '📝 Penilaian',
    'album' => '🖼️ Album Kegiatan',
    'raport' => '📊 Raport'
];

// Proses upload
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jenis = mysqli_real_escape_string($conn, $_POST['jenis']);
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $id_kelas = !empty($_POST['id_kelas']) ? (int)$_POST['id_kelas'] : 'NULL';
    $id_mapel = !empty($_POST['id_mapel']) ? (int)$_POST['id_mapel'] : 'NULL';
    $semester = mysqli_real_escape_string($conn, $_POST['semester'] ?? '');
    $tahun_ajaran = mysqli_real_escape_string($conn, $_POST['tahun_ajaran'] ?? '');
    $drive_link = mysqli_real_escape_string($conn, $_POST['drive_link']);
    
    if (empty($drive_link)) {
        $error = "❌ Link Google Drive wajib diisi!";
    } elseif (strpos($drive_link, 'drive.google.com') === false && strpos($drive_link, 'docs.google.com') === false) {
        $error = "❌ Link harus berupa Google Drive!";
    } else {
        $sql = "INSERT INTO dokumen_perangkat (
            id_guru, id_sekolah, id_kelas, id_mapel, jenis, judul, deskripsi,
            drive_link, semester, tahun_ajaran, status, created_at
        ) VALUES (
            $id_guru, $sekolah_id, $id_kelas, $id_mapel, '$jenis', '$judul', '$deskripsi',
            '$drive_link', " . ($semester ? "'$semester'" : "NULL") . ",
            " . ($tahun_ajaran ? "'$tahun_ajaran'" : "NULL") . ",
            'pending_kepsek', NOW()
        )";
        
        if (query($sql)) {
            $success = "✅ Dokumen berhasil diupload! Menunggu persetujuan Kepala Sekolah.";
        } else {
            $error = "❌ Gagal upload dokumen!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Dokumen - Google Drive</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/website_perangkat/public/css/style.css">
    <style>
        .drive-info {
            background: #e8f0fe;
            padding: 15px 20px;
            border-radius: 8px;
            border-left: 4px solid #059669;
            margin-bottom: 20px;
        }
        .drive-info h4 { color: #059669; margin-bottom: 8px; }
        .drive-info p { font-size: 13px; color: #555; margin: 4px 0; }
        .drive-info ol { margin-left: 20px; font-size: 13px; color: #555; }
        
        .form-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.04);
        }
        
        .btn-submit {
            background: #059669;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
        }
        .btn-submit:hover { background: #047857; transform: scale(1.02); }
    </style>
</head>
<body>
    <div class="container">
        <header class="header header-perangkat">
            <div>
                <h1><i class="fas fa-upload"></i> Upload Dokumen Perangkat</h1>
                <p>Upload melalui Google Drive</p>
            </div>
            <div class="badge-user">
                <i class="fas fa-user"></i> <?php echo $_SESSION['name']; ?>
            </div>
        </header>

        <?php include_once 'navbar.php'; ?>

        <main>
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="drive-info">
                <h4><i class="fas fa-info-circle"></i> Panduan Upload via Google Drive</h4>
                <p>1. Upload file ke Google Drive</p>
                <p>2. Klik kanan file → "Bagikan" → "Siapa saja yang memiliki link dapat melihat"</p>
                <p>3. Salin link dan tempelkan di bawah</p>
                <p style="margin-top:8px; font-size:12px; color:#888;">
                    <i class="fas fa-link"></i> Contoh: <strong>https://drive.google.com/file/d/xxxxx/view?usp=sharing</strong>
                </p>
            </div>

            <div class="form-card">
                <form method="POST">
                    <div class="form-group">
                        <label>📋 Jenis Dokumen <span class="required">*</span></label>
                        <select name="jenis" required>
                            <option value="">-- Pilih Jenis --</option>
                            <?php foreach ($jenis_dokumen as $key => $label): ?>
                                <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>📝 Judul <span class="required">*</span></label>
                        <input type="text" name="judul" required placeholder="Masukkan judul dokumen">
                    </div>

                    <div class="form-group">
                        <label>📄 Deskripsi</label>
                        <textarea name="deskripsi" rows="3" placeholder="Deskripsi dokumen..."></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>🏫 Kelas</label>
                            <select name="id_kelas">
                                <option value="">-- Pilih Kelas --</option>
                                <?php foreach ($kelas as $k): ?>
                                    <option value="<?php echo $k['id']; ?>">
                                        Kelas <?php echo $k['nama_kelas']; ?> (<?php echo $k['jenjang']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>📚 Mata Pelajaran</label>
                            <select name="id_mapel">
                                <option value="">-- Pilih Mapel --</option>
                                <?php foreach ($mapel as $m): ?>
                                    <option value="<?php echo $m['id']; ?>">
                                        <?php echo $m['nama_mapel']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>📖 Semester</label>
                            <select name="semester">
                                <option value="">-- Pilih --</option>
                                <option value="1">Semester 1</option>
                                <option value="2">Semester 2</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>📅 Tahun Ajaran</label>
                            <input type="text" name="tahun_ajaran" placeholder="2025/2026">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fab fa-google-drive" style="color:#059669;"></i> Link Google Drive <span class="required">*</span></label>
                        <input type="url" name="drive_link" required placeholder="https://drive.google.com/file/d/xxxxx/view?usp=sharing">
                        <div class="help-text">
                            <i class="fas fa-info-circle"></i> Pastikan link sudah diatur "Siapa saja yang memiliki link dapat melihat"
                        </div>
                    </div>

                    <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:10px;">
                        <button type="submit" class="btn-submit"><i class="fas fa-upload"></i> Upload</button>
                        <a href="perangkat_dashboard.php" class="btn btn-secondary">🔙 Kembali</a>
                    </div>
                </form>
            </div>
        </main>

        <footer class="footer">
            <p>&copy; <?php echo date('Y'); ?> Pusat Perangkat Pembelajaran</p>
        </footer>
    </div>
</body>
</html>