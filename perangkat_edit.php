<?php
// perangkat_edit.php - Edit Dokumen Perangkat Pembelajaran
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'guru') {
    header('Location: login.php');
    exit;
}

include_once 'config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id == 0) {
    header('Location: perangkat_dashboard.php');
    exit;
}

// Ambil data dokumen
$dokumen = fetchOne("
    SELECT d.*, k.nama_kelas, mp.nama_mapel
    FROM dokumen_perangkat d
    LEFT JOIN kelas k ON d.id_kelas = k.id
    LEFT JOIN mata_pelajaran mp ON d.id_mapel = mp.id
    WHERE d.id = $id AND d.id_guru = " . $_SESSION['user_id']
);

if (!$dokumen) {
    header('Location: perangkat_dashboard.php');
    exit;
}

// Ambil data untuk dropdown
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

// Proses update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jenis = mysqli_real_escape_string($conn, $_POST['jenis']);
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $id_kelas = !empty($_POST['id_kelas']) ? (int)$_POST['id_kelas'] : 'NULL';
    $id_mapel = !empty($_POST['id_mapel']) ? (int)$_POST['id_mapel'] : 'NULL';
    $semester = mysqli_real_escape_string($conn, $_POST['semester'] ?? '');
    $tahun_ajaran = mysqli_real_escape_string($conn, $_POST['tahun_ajaran'] ?? '');
    $drive_link = mysqli_real_escape_string($conn, $_POST['drive_link']);
    
    // Validasi link Google Drive
    if (empty($drive_link)) {
        $error = "❌ Link Google Drive wajib diisi!";
    } elseif (strpos($drive_link, 'drive.google.com') === false && strpos($drive_link, 'docs.google.com') === false) {
        $error = "❌ Link harus berupa Google Drive!";
    } else {
        $sql = "UPDATE dokumen_perangkat SET 
                    jenis = '$jenis',
                    judul = '$judul',
                    deskripsi = '$deskripsi',
                    id_kelas = $id_kelas,
                    id_mapel = $id_mapel,
                    semester = " . ($semester ? "'$semester'" : "NULL") . ",
                    tahun_ajaran = " . ($tahun_ajaran ? "'$tahun_ajaran'" : "NULL") . ",
                    drive_link = '$drive_link',
                    updated_at = NOW()
                WHERE id = $id AND id_guru = " . $_SESSION['user_id'];
        
        if (query($sql)) {
            $success = "✅ Dokumen berhasil diperbarui!";
            // Refresh data
            $dokumen = fetchOne("
                SELECT d.*, k.nama_kelas, mp.nama_mapel
                FROM dokumen_perangkat d
                LEFT JOIN kelas k ON d.id_kelas = k.id
                LEFT JOIN mata_pelajaran mp ON d.id_mapel = mp.id
                WHERE d.id = $id AND d.id_guru = " . $_SESSION['user_id']
            );
        } else {
            $error = "❌ Gagal memperbarui dokumen: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Dokumen - Perangkat Pembelajaran</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/website_perangkat/public/css/style.css">
    <style>
        .form-card {
            max-width: 800px;
            margin: 0 auto;
        }
        .drive-info {
            background: #e8f0fe;
            padding: 15px 20px;
            border-radius: 8px;
            border-left: 4px solid #059669;
            margin-bottom: 20px;
        }
        .drive-info h4 { color: #059669; margin-bottom: 8px; }
        .drive-info p { font-size: 13px; color: #555; margin: 4px 0; }
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
        .btn-back {
            background: #6c757d;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn-back:hover { background: #5a6268; transform: scale(1.02); }
        .status-badge-edit {
            display: inline-block;
            padding: 4px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }
        .status-badge-edit.draft { background: #e2e3e5; color: #383d41; }
        .status-badge-edit.pending { background: #fff3cd; color: #856404; }
        .status-badge-edit.terverifikasi { background: #d4edda; color: #155724; }
        .status-badge-edit.ditolak { background: #f8d7da; color: #721c24; }
        .info-status {
            background: #f8f9fa;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .info-status .label { font-weight: 600; color: #4a5568; }
    </style>
</head>
<body>
    <div class="container">
        <!-- HEADER -->
        <header class="header header-perangkat">
            <div>
                <h1><i class="fas fa-edit"></i> Edit Dokumen Perangkat</h1>
                <p>Perbarui informasi dokumen perangkat pembelajaran</p>
            </div>
            <div class="badge-user">
                <i class="fas fa-user"></i> <?php echo $_SESSION['name']; ?>
            </div>
        </header>

        <!-- NAVBAR -->
        <?php include_once 'navbar.php'; ?>

        <!-- MAIN -->
        <main>
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="form-card">
                <!-- Info Status -->
                <div class="info-status">
                    <span class="label">📌 Status Dokumen:</span>
                    <span class="status-badge-edit <?php echo $dokumen['status']; ?>">
                        <?php 
                        $status_label = [
                            'draft' => '📝 Draft',
                            'pending_kepsek' => '⏳ Menunggu Kepsek',
                            'ditolak_kepsek' => '❌ Ditolak Kepsek',
                            'pending_pengawas' => '⏳ Menunggu Pengawas',
                            'ditolak_pengawas' => '❌ Ditolak Pengawas',
                            'terverifikasi' => '✅ Terverifikasi'
                        ];
                        echo $status_label[$dokumen['status']] ?? $dokumen['status'];
                        ?>
                    </span>
                    <span style="margin-left:auto; font-size:12px; color:#888;">
                        <i class="far fa-calendar-alt"></i> Diupload: <?php echo date('d/m/Y H:i', strtotime($dokumen['created_at'])); ?>
                    </span>
                </div>

                <!-- Drive Info -->
                <div class="drive-info">
                    <h4><i class="fas fa-info-circle"></i> Panduan Edit</h4>
                    <p>1. Pastikan link Google Drive masih aktif dan dapat diakses</p>
                    <p>2. Link harus diatur "Siapa saja yang memiliki link dapat melihat"</p>
                    <p style="margin-top:8px; font-size:12px; color:#888;">
                        <i class="fas fa-link"></i> Contoh: <strong>https://drive.google.com/file/d/xxxxx/view?usp=sharing</strong>
                    </p>
                </div>

                <!-- Form Edit -->
                <div class="form-card" style="padding:25px 30px; background:white; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,0.06); border:1px solid rgba(0,0,0,0.04);">
                    <form method="POST">
                        <div class="form-group">
                            <label>📋 Jenis Dokumen <span class="required">*</span></label>
                            <select name="jenis" required>
                                <?php foreach ($jenis_dokumen as $key => $label): ?>
                                    <option value="<?php echo $key; ?>" <?php echo $dokumen['jenis'] == $key ? 'selected' : ''; ?>>
                                        <?php echo $label; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>📝 Judul <span class="required">*</span></label>
                            <input type="text" name="judul" required value="<?php echo htmlspecialchars($dokumen['judul']); ?>">
                        </div>

                        <div class="form-group">
                            <label>📄 Deskripsi</label>
                            <textarea name="deskripsi" rows="3"><?php echo htmlspecialchars($dokumen['deskripsi'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>🏫 Kelas</label>
                                <select name="id_kelas">
                                    <option value="">-- Pilih Kelas --</option>
                                    <?php foreach ($kelas as $k): ?>
                                        <option value="<?php echo $k['id']; ?>" <?php echo $dokumen['id_kelas'] == $k['id'] ? 'selected' : ''; ?>>
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
                                        <option value="<?php echo $m['id']; ?>" <?php echo $dokumen['id_mapel'] == $m['id'] ? 'selected' : ''; ?>>
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
                                    <option value="1" <?php echo $dokumen['semester'] == '1' ? 'selected' : ''; ?>>Semester 1</option>
                                    <option value="2" <?php echo $dokumen['semester'] == '2' ? 'selected' : ''; ?>>Semester 2</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>📅 Tahun Ajaran</label>
                                <input type="text" name="tahun_ajaran" value="<?php echo htmlspecialchars($dokumen['tahun_ajaran'] ?? ''); ?>" placeholder="2025/2026">
                            </div>
                        </div>

                        <div class="form-group">
                            <label><i class="fab fa-google-drive" style="color:#059669;"></i> Link Google Drive <span class="required">*</span></label>
                            <input type="url" name="drive_link" required value="<?php echo htmlspecialchars($dokumen['drive_link'] ?? ''); ?>" placeholder="https://drive.google.com/file/d/xxxxx/view?usp=sharing">
                            <div class="help-text">
                                <i class="fas fa-info-circle"></i> Pastikan link sudah diatur "Siapa saja yang memiliki link dapat melihat"
                            </div>
                        </div>

                        <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:10px;">
                            <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Simpan Perubahan</button>
                            <a href="perangkat_dashboard.php" class="btn-back">🔙 Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>

        <!-- FOOTER -->
        <footer class="footer">
            <p>&copy; <?php echo date('Y'); ?> Pusat Perangkat Pembelajaran</p>
        </footer>
    </div>
</body>
</html>