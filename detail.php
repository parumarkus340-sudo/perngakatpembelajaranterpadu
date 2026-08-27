<?php
// detail.php - Halaman Detail Perangkat Pembelajaran
session_start();
include_once 'config/database.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id == 0) {
    header('Location: katalog.php');
    exit;
}

// Ambil data perangkat
$perangkat = fetchOne("
    SELECT p.*, u.name as guru_name, u.email as guru_email, 
           mp.nama_mapel, k.nama_kelas, k.jenjang
    FROM perangkat p
    LEFT JOIN users u ON p.id_guru = u.id
    LEFT JOIN mata_pelajaran mp ON p.id_mapel = mp.id
    LEFT JOIN kelas k ON p.id_kelas = k.id
    WHERE p.id = $id
");

if (!$perangkat) {
    header('Location: katalog.php');
    exit;
}

// Update views
query("UPDATE perangkat SET views = views + 1 WHERE id = $id");

// Ambil komentar
$komentar = fetchAll("
    SELECT k.*, u.name as user_name, u.role as user_role
    FROM komentar k
    LEFT JOIN users u ON k.id_user = u.id
    WHERE k.id_perangkat = $id
    ORDER BY k.created_at DESC
");

// Proses tambah komentar
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $komentar_text = mysqli_real_escape_string($conn, $_POST['komentar']);
    $id_user = $_SESSION['user_id'];
    
    if (!empty($komentar_text)) {
        query("
            INSERT INTO komentar (id_perangkat, id_user, komentar) 
            VALUES ($id, $id_user, '$komentar_text')
        ");
        header('Location: detail.php?id=' . $id);
        exit;
    }
}

// Proses download
if (isset($_GET['download'])) {
    $file_path = $perangkat['file_path'];
    if (file_exists($file_path)) {
        query("UPDATE perangkat SET downloads = downloads + 1 WHERE id = $id");
        $id_user = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NULL';
        $ip = $_SERVER['REMOTE_ADDR'];
        query("
            INSERT INTO log_unduhan (id_perangkat, id_user, ip_address) 
            VALUES ($id, $id_user, '$ip')
        ");
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        readfile($file_path);
        exit;
    }
}

// Fungsi untuk menentukan tipe file
function getFileType($filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    // Excel
    if (in_array($ext, ['xls', 'xlsx', 'xlsm', 'xlsb'])) {
        return 'excel';
    }
    // Word
    if (in_array($ext, ['doc', 'docx'])) {
        return 'word';
    }
    // PDF
    if ($ext == 'pdf') {
        return 'pdf';
    }
    // Gambar
    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'])) {
        return 'image';
    }
    // Video
    if (in_array($ext, ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv'])) {
        return 'video';
    }
    // PPT
    if (in_array($ext, ['ppt', 'pptx'])) {
        return 'ppt';
    }
    // Lainnya
    return 'other';
}

// Fungsi untuk mendapatkan icon file
function getFileIcon($filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $icons = [
        'pdf' => 'fa-file-pdf',
        'doc' => 'fa-file-word',
        'docx' => 'fa-file-word',
        'xls' => 'fa-file-excel',
        'xlsx' => 'fa-file-excel',
        'ppt' => 'fa-file-powerpoint',
        'pptx' => 'fa-file-powerpoint',
        'mp4' => 'fa-file-video',
        'webm' => 'fa-file-video',
        'mov' => 'fa-file-video',
        'avi' => 'fa-file-video',
        'mkv' => 'fa-file-video',
        'zip' => 'fa-file-archive',
        'rar' => 'fa-file-archive',
        'jpg' => 'fa-file-image',
        'jpeg' => 'fa-file-image',
        'png' => 'fa-file-image',
        'gif' => 'fa-file-image',
        'webp' => 'fa-file-image',
        'txt' => 'fa-file-alt',
        'link' => 'fa-link',
        'youtube' => 'fa-youtube',
        'drive' => 'fa-google-drive',
    ];
    return $icons[$ext] ?? 'fa-file';
}

// Fungsi untuk mendapatkan label file
function getFileLabel($filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $labels = [
        'pdf' => 'PDF Document',
        'doc' => 'Word Document',
        'docx' => 'Word Document',
        'xls' => 'Excel Spreadsheet',
        'xlsx' => 'Excel Spreadsheet',
        'ppt' => 'PowerPoint Presentation',
        'pptx' => 'PowerPoint Presentation',
        'mp4' => 'Video MP4',
        'webm' => 'Video WebM',
        'zip' => 'File ZIP',
        'rar' => 'File RAR',
        'jpg' => 'Gambar JPG',
        'jpeg' => 'Gambar JPEG',
        'png' => 'Gambar PNG',
        'gif' => 'Gambar GIF',
        'link' => 'Link',
        'youtube' => 'YouTube Video',
        'drive' => 'Google Drive',
    ];
    return $labels[$ext] ?? 'File';
}

// Fungsi untuk cek file bisa preview
function canPreview($filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $preview_ext = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'webm', 'ogg', 'xls', 'xlsx', 'doc', 'docx'];
    return in_array($ext, $preview_ext);
}

// Fungsi untuk mendapatkan Google Docs Viewer URL
function getGoogleDocsViewer($file_path) {
    $url = urlencode('http://' . $_SERVER['HTTP_HOST'] . '/' . $file_path);
    return 'https://docs.google.com/viewer?embedded=true&url=' . $url;
}

// Fungsi untuk ekstrak YouTube ID
function getYoutubeId($url) {
    preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\s?]+)/', $url, $matches);
    return $matches[1] ?? null;
}

// Fungsi untuk ekstrak Google Drive ID
function getGoogleDriveId($url) {
    preg_match('/\/d\/([^\/]+)/', $url, $matches);
    if (isset($matches[1])) return $matches[1];
    preg_match('/id=([^&]+)/', $url, $matches);
    return $matches[1] ?? null;
}

// Cek apakah file_path adalah link (YouTube/Drive)
function isLink($path) {
    if (empty($path)) return false;
    return filter_var($path, FILTER_VALIDATE_URL) !== false;
}

// Cek apakah link YouTube
function isYoutube($path) {
    return isLink($path) && (strpos($path, 'youtube.com') !== false || strpos($path, 'youtu.be') !== false);
}

// Cek apakah link Google Drive
function isGoogleDrive($path) {
    return isLink($path) && strpos($path, 'drive.google.com') !== false;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($perangkat['judul']); ?> - Pusat Perangkat Pembelajaran</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f0f4f8;
            color: #1a202c;
            line-height: 1.6;
        }
        .container { max-width: 1100px; margin: 0 auto; padding: 20px; }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 25px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        .header h1 { margin: 0; font-size: 1.5em; }
        .header h1 i { margin-right: 10px; }
        .header p { margin: 5px 0 0 0; opacity: 0.9; font-size: 14px; }
        .header .badge-user {
            background: rgba(255,255,255,0.2);
            padding: 6px 16px;
            border-radius: 30px;
            font-size: 13px;
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .nav {
            background: white;
            padding: 12px 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            align-items: center;
        }
        .nav a {
            color: #4a5568;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .nav a:hover { background: #eef2f7; color: #667eea; }
        .nav a.active { background: #667eea; color: white; }
        .nav .user-info {
            margin-left: auto;
            font-size: 13px;
            color: #718096;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .nav .user-info .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }
        
        .detail-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
            padding: 30px;
            margin-top: 20px;
            border: 1px solid rgba(0,0,0,0.04);
        }
        
        .badge {
            display: inline-block;
            padding: 4px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }
        .badge-rpp { background: #d4edda; color: #155724; }
        .badge-modul { background: #cce5ff; color: #004085; }
        .badge-ppt { background: #fff3cd; color: #856404; }
        .badge-video { background: #f8d7da; color: #721c24; }
        .badge-soal { background: #d1ecf1; color: #0c5460; }
        .badge-lainnya { background: #e2e3e5; color: #383d41; }
        
        .detail-container h1 {
            font-size: 1.8em;
            margin: 15px 0 10px 0;
            color: #0f2b5c;
        }
        .detail-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin: 15px 0;
            padding: 15px 0;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
        }
        .detail-meta .meta-item { font-size: 14px; color: #555; }
        .detail-meta .meta-item strong { color: #333; }
        
        .detail-description {
            margin: 20px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            line-height: 1.8;
        }
        
        /* ============================================
           FILE PREVIEW
        ============================================ */
        .file-preview-container {
            margin: 20px 0;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            background: #fafafa;
        }
        .file-preview-container .preview-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 20px;
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
            flex-wrap: wrap;
            gap: 10px;
        }
        .file-preview-container .preview-header .file-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .file-preview-container .preview-header .file-info i {
            font-size: 24px;
            color: #667eea;
        }
        .file-preview-container .preview-header .file-info .file-name {
            font-weight: 500;
            font-size: 14px;
            color: #333;
            word-break: break-all;
        }
        .file-preview-container .preview-header .file-info .file-size {
            font-size: 12px;
            color: #888;
        }
        .file-preview-container .preview-header .file-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .file-preview-container .preview-header .file-actions .btn-download {
            background: #28a745;
            color: white;
            padding: 6px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .file-preview-container .preview-header .file-actions .btn-download:hover {
            background: #218838;
        }
        .file-preview-container .preview-header .file-actions .btn-link {
            background: #667eea;
            color: white;
            padding: 6px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .file-preview-container .preview-header .file-actions .btn-link:hover {
            background: #5a67d8;
        }
        
        .file-preview-container .preview-body {
            padding: 20px;
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fafafa;
        }
        .file-preview-container .preview-body iframe {
            width: 100%;
            height: 550px;
            border: none;
            border-radius: 8px;
        }
        .file-preview-container .preview-body .preview-placeholder {
            text-align: center;
            padding: 40px;
            color: #888;
        }
        .file-preview-container .preview-body .preview-placeholder i {
            font-size: 64px;
            color: #d1d5db;
            margin-bottom: 16px;
        }
        .file-preview-container .preview-body .preview-placeholder h4 {
            color: #333;
            margin-bottom: 4px;
        }
        .file-preview-container .preview-body .preview-placeholder p {
            font-size: 13px;
        }
        
        /* Video Preview */
        .file-preview-container .preview-body video {
            width: 100%;
            max-height: 500px;
            border-radius: 8px;
            background: #000;
        }
        
        /* Image Preview */
        .file-preview-container .preview-body img {
            max-width: 100%;
            max-height: 500px;
            border-radius: 8px;
            object-fit: contain;
        }
        
        /* YouTube Embed */
        .file-preview-container .preview-body .youtube-wrapper {
            position: relative;
            width: 100%;
            padding-bottom: 56.25%;
            height: 0;
            border-radius: 8px;
            overflow: hidden;
        }
        .file-preview-container .preview-body .youtube-wrapper iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 8px;
        }
        
        /* Google Drive Embed */
        .file-preview-container .preview-body .drive-wrapper {
            width: 100%;
            height: 550px;
            border-radius: 8px;
            overflow: hidden;
        }
        .file-preview-container .preview-body .drive-wrapper iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        
        .detail-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin: 20px 0;
        }
        .btn-back {
            background: #6c757d;
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-weight: 500;
        }
        .btn-back:hover { background: #5a6268; }
        
        .status-info {
            display: inline-block;
            padding: 4px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            margin-left: 10px;
        }
        .status-terverifikasi { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-ditolak { background: #f8d7da; color: #721c24; }
        .status-draft { background: #e2e3e5; color: #383d41; }
        
        .stats-info {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            margin: 15px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .stats-info .stat-item { font-size: 14px; color: #555; }
        
        .komentar-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }
        .komentar-section h3 { margin-bottom: 20px; }
        .komentar-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .komentar-item .komentar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }
        .komentar-item .komentar-header .name { font-weight: bold; color: #333; }
        .komentar-item .komentar-header .date { font-size: 12px; color: #888; }
        .komentar-item .komentar-body { color: #555; margin-top: 5px; }
        .komentar-form textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            resize: vertical;
            min-height: 80px;
        }
        .komentar-form textarea:focus {
            border-color: #667eea;
            outline: none;
        }
        .komentar-form .btn-submit {
            background: #667eea;
            color: white;
            padding: 10px 30px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            margin-top: 10px;
        }
        .komentar-form .btn-submit:hover { background: #5a67d8; }
        .login-warning {
            background: #fff3cd;
            padding: 15px;
            border-radius: 8px;
            color: #856404;
            margin: 10px 0;
        }
        .login-warning a { color: #667eea; font-weight: bold; }
        
        .file-type-badge {
            display: inline-block;
            padding: 2px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 8px;
        }
        .file-type-badge.excel { background: #d1fae5; color: #059669; }
        .file-type-badge.word { background: #dbeafe; color: #2563eb; }
        .file-type-badge.pdf { background: #fee2e2; color: #dc2626; }
        .file-type-badge.video { background: #fef3c7; color: #d97706; }
        .file-type-badge.image { background: #ede9fe; color: #7c3aed; }
        .file-type-badge.link { background: #cce5ff; color: #004085; }
        .file-type-badge.youtube { background: #fee2e2; color: #dc2626; }
        .file-type-badge.drive { background: #d1fae5; color: #059669; }
        
        .footer { text-align: center; margin-top: 30px; padding: 20px; color: #888; font-size: 13px; border-top: 1px solid #e5e7eb; }
        
        @media (max-width: 768px) {
            .container { padding: 12px; }
            .header { flex-direction: column; text-align: center; gap: 10px; }
            .nav { padding: 10px 16px; justify-content: center; }
            .nav .user-info { margin-left: 0; }
            .detail-container { padding: 20px; }
            .detail-container h1 { font-size: 1.3em; }
            .detail-meta { flex-direction: column; gap: 5px; }
            .file-preview-container .preview-body iframe { height: 300px; }
            .file-preview-container .preview-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .file-preview-container .preview-header .file-actions { width: 100%; }
            .file-preview-container .preview-header .file-actions .btn-download,
            .file-preview-container .preview-header .file-actions .btn-link {
                width: 100%;
                justify-content: center;
            }
            .file-preview-container .preview-body .drive-wrapper { height: 300px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <div>
                <h1><i class="fas fa-file-alt"></i> Detail Perangkat Pembelajaran</h1>
                <p>Informasi lengkap perangkat pembelajaran</p>
            </div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="badge-user">
                    <i class="fas fa-user"></i> <?php echo $_SESSION['name']; ?>
                </div>
            <?php endif; ?>
        </header>

         <!-- ===== NAVIGASI PAKAI NAVBAR.PHP ===== -->
        <?php include_once 'navbar.php'; ?>
        <!-- ===================================== -->

        <main>
            <div class="detail-container">
                <!-- Badge & Status -->
                <div style="display:flex; align-items:center; flex-wrap:wrap; gap:10px;">
                    <span class="badge badge-<?php echo strtolower($perangkat['jenis']); ?>">
                        <?php echo $perangkat['jenis']; ?>
                    </span>
                    <span class="status-info status-<?php echo $perangkat['status']; ?>">
                        <?php 
                        $status_label = [
                            'draft' => '📝 Draft',
                            'pending_kepsek' => '⏳ Menunggu Kepsek',
                            'ditolak_kepsek' => '❌ Ditolak Kepsek',
                            'pending_pengawas' => '⏳ Menunggu Pengawas',
                            'ditolak_pengawas' => '❌ Ditolak Pengawas',
                            'terverifikasi' => '✅ Terverifikasi'
                        ];
                        echo $status_label[$perangkat['status']] ?? $perangkat['status'];
                        ?>
                    </span>
                </div>

                <h1><?php echo htmlspecialchars($perangkat['judul']); ?></h1>

                <div class="detail-meta">
                    <div class="meta-item"><strong>📚 Mata Pelajaran:</strong> <?php echo $perangkat['nama_mapel'] ?? '-'; ?></div>
                    <div class="meta-item"><strong>🏫 Kelas:</strong> Kelas <?php echo $perangkat['nama_kelas'] ?? '-'; ?> (<?php echo $perangkat['jenjang'] ?? '-'; ?>)</div>
                    <div class="meta-item"><strong>📖 Semester:</strong> <?php echo $perangkat['semester']; ?></div>
                    <div class="meta-item"><strong>📅 Tahun Ajaran:</strong> <?php echo $perangkat['tahun_ajaran']; ?></div>
                    <div class="meta-item"><strong>👨‍🏫 Guru:</strong> <?php echo $perangkat['guru_name'] ?? 'Unknown'; ?></div>
                </div>

                <?php if (!empty($perangkat['deskripsi'])): ?>
                    <div class="detail-description">
                        <strong>📝 Deskripsi:</strong>
                        <p style="margin-top:10px;"><?php echo nl2br(htmlspecialchars($perangkat['deskripsi'])); ?></p>
                    </div>
                <?php endif; ?>

                <!-- ========================================== -->
                <!-- FILE PREVIEW -->
                <!-- ========================================== -->
                <?php 
                $file_path = $perangkat['file_path'];
                $is_link = isLink($file_path);
                $is_youtube = isYoutube($file_path);
                $is_drive = isGoogleDrive($file_path);
                $youtube_id = $is_youtube ? getYoutubeId($file_path) : null;
                $drive_id = $is_drive ? getGoogleDriveId($file_path) : null;
                $file_type = $is_link ? 'link' : getFileType($file_path);
                $file_icon = $is_link ? ($is_youtube ? 'fa-youtube' : ($is_drive ? 'fa-google-drive' : 'fa-link')) : getFileIcon($file_path);
                $file_label = $is_link ? ($is_youtube ? 'YouTube Video' : ($is_drive ? 'Google Drive' : 'Link')) : getFileLabel($file_path);
                $file_ext = $is_link ? 'link' : strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
                ?>
                
                <?php if (!empty($file_path)): ?>
                    <div class="file-preview-container">
                        <div class="preview-header">
                            <div class="file-info">
                                <i class="fas <?php echo $file_icon; ?>"></i>
                                <div>
                                    <div class="file-name">
                                        <?php echo $is_link ? $file_path : basename($file_path); ?>
                                        <span class="file-type-badge <?php echo $file_type; ?>">
                                            <?php echo strtoupper($file_type); ?>
                                        </span>
                                    </div>
                                    <?php if (!$is_link && file_exists($file_path)): 
                                        $file_size = filesize($file_path);
                                        $file_size_formatted = number_format($file_size / 1024, 1) . ' KB';
                                        if ($file_size > 1024 * 1024) {
                                            $file_size_formatted = number_format($file_size / (1024 * 1024), 1) . ' MB';
                                        }
                                    ?>
                                        <div class="file-size"><?php echo $file_size_formatted; ?></div>
                                    <?php elseif ($is_link): ?>
                                        <div class="file-size"><i class="fas fa-external-link-alt"></i> Link Eksternal</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="file-actions">
                                <?php if ($is_link): ?>
                                    <a href="<?php echo $file_path; ?>" class="btn-link" target="_blank">
                                        <i class="fas fa-external-link-alt"></i> Buka Link
                                    </a>
                                <?php else: ?>
                                    <?php if (file_exists($file_path)): ?>
                                        <a href="?id=<?php echo $id; ?>&download=1" class="btn-download">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="preview-body">
                            <?php if ($is_youtube && $youtube_id): ?>
                                <!-- YouTube Embed -->
                                <div class="youtube-wrapper">
                                    <iframe src="https://www.youtube.com/embed/<?php echo $youtube_id; ?>" 
                                            allowfullscreen 
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
                                    </iframe>
                                </div>
                            <?php elseif ($is_drive && $drive_id): ?>
                                <!-- Google Drive Embed -->
                                <div class="drive-wrapper">
                                    <iframe src="https://drive.google.com/file/d/<?php echo $drive_id; ?>/preview" 
                                            allowfullscreen>
                                    </iframe>
                                </div>
                            <?php elseif ($is_link): ?>
                                <!-- Link Umum -->
                                <div class="preview-placeholder">
                                    <i class="fas fa-link"></i>
                                    <h4>Link Eksternal</h4>
                                    <p>Klik tombol "Buka Link" untuk membuka di tab baru.</p>
                                    <p style="font-size:12px; color:#aaa; margin-top:8px;">
                                        <?php echo $file_path; ?>
                                    </p>
                                    <a href="<?php echo $file_path; ?>" target="_blank" style="display:inline-block; margin-top:12px; background:#667eea; color:white; padding:10px 25px; border-radius:8px; text-decoration:none; font-weight:500;">
                                        <i class="fas fa-external-link-alt"></i> Buka Link
                                    </a>
                                </div>
                            <?php elseif (file_exists($file_path) && canPreview($file_path)): ?>
                                <!-- File yang bisa di-preview -->
                                <?php if ($file_type == 'image'): ?>
                                    <img src="<?php echo $file_path; ?>" alt="Preview Gambar">
                                <?php elseif ($file_type == 'pdf'): ?>
                                    <iframe src="<?php echo $file_path; ?>" title="Preview PDF"></iframe>
                                <?php elseif ($file_type == 'video'): ?>
                                    <video controls>
                                        <source src="<?php echo $file_path; ?>" type="video/<?php echo $file_ext; ?>">
                                        Browser Anda tidak mendukung video.
                                    </video>
                                <?php elseif ($file_type == 'excel' || $file_type == 'word'): ?>
                                    <!-- Preview Excel/Word menggunakan Google Docs Viewer -->
                                    <iframe src="<?php echo getGoogleDocsViewer($file_path); ?>" 
                                            title="Preview <?php echo strtoupper($file_type); ?>">
                                    </iframe>
                                <?php else: ?>
                                    <div class="preview-placeholder">
                                        <i class="fas <?php echo $file_icon; ?>"></i>
                                        <h4>File <?php echo strtoupper($file_ext); ?></h4>
                                        <p>File ini tidak dapat ditampilkan secara langsung di browser.</p>
                                        <p style="font-size:12px; color:#aaa;">Klik tombol Download untuk mengunduh file.</p>
                                    </div>
                                <?php endif; ?>
                            <?php elseif (file_exists($file_path)): ?>
                                <!-- File tidak bisa preview -->
                                <div class="preview-placeholder">
                                    <i class="fas <?php echo $file_icon; ?>"></i>
                                    <h4>File <?php echo strtoupper($file_ext); ?></h4>
                                    <p>File ini tidak dapat ditampilkan secara langsung di browser.</p>
                                    <p style="font-size:12px; color:#aaa;">Klik tombol Download untuk mengunduh file.</p>
                                </div>
                            <?php else: ?>
                                <div class="preview-placeholder" style="color:#dc3545;">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <h4>File Tidak Tersedia</h4>
                                    <p>File tidak ditemukan atau telah dihapus.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div style="background:#fef2f2; padding:15px; border-radius:8px; color:#dc3545; text-align:center; margin:20px 0;">
                        <i class="fas fa-exclamation-triangle"></i> File tidak tersedia atau telah dihapus.
                    </div>
                <?php endif; ?>

                <!-- Statistik -->
                <div class="stats-info">
                    <div class="stat-item">👁️ <strong><?php echo $perangkat['views']; ?></strong> dilihat</div>
                    <div class="stat-item">⬇️ <strong><?php echo $perangkat['downloads']; ?></strong> diunduh</div>
                    <div class="stat-item">📅 Upload: <strong><?php echo date('d/m/Y H:i', strtotime($perangkat['created_at'])); ?></strong></div>
                </div>

                <!-- Tanda Tangan -->
                <?php if ($perangkat['status'] == 'terverifikasi'): ?>
                    <div style="background:#f0f8ff; padding:15px; border-radius:8px; margin:15px 0; border-left:4px solid #667eea;">
                        <h4 style="color:#667eea;">📜 Legalitas Dokumen</h4>
                        <?php if (!empty($perangkat['ttd_kepsek'])): ?>
                            <p style="margin:5px 0; font-size:14px;">
                                ✅ <strong>Kepala Sekolah:</strong> Ditandatangani pada 
                                <?php echo date('d/m/Y H:i', strtotime($perangkat['ttd_kepsek_date'])); ?>
                            </p>
                        <?php endif; ?>
                        <?php if (!empty($perangkat['ttd_pengawas'])): ?>
                            <p style="margin:5px 0; font-size:14px;">
                                ✅ <strong>Pengawas:</strong> Ditandatangani pada 
                                <?php echo date('d/m/Y H:i', strtotime($perangkat['ttd_pengawas_date'])); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Tombol Aksi -->
                <div class="detail-actions">
                    <a href="/website_perangkat/katalog.php" class="btn-back">🔙 Kembali ke Katalog</a>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'guru' && $_SESSION['user_id'] == $perangkat['id_guru']): ?>
                        <a href="edit_perangkat.php?id=<?php echo $id; ?>" class="btn-back" style="background:#ffc107; color:#000;">✏️ Edit</a>
                    <?php endif; ?>
                </div>

                <!-- Komentar -->
                <div class="komentar-section">
                    <h3>💬 Komentar (<?php echo count($komentar); ?>)</h3>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form method="POST" class="komentar-form">
                            <textarea name="komentar" placeholder="Tulis komentar Anda..." required></textarea>
                            <button type="submit" class="btn-submit">💬 Kirim Komentar</button>
                        </form>
                    <?php else: ?>
                        <div class="login-warning">
                            🔐 Silahkan <a href="/website_perangkat/login.php">login</a> untuk memberikan komentar.
                        </div>
                    <?php endif; ?>

                    <?php if (count($komentar) > 0): ?>
                        <div style="margin-top:20px;">
                            <?php foreach ($komentar as $k): ?>
                                <div class="komentar-item">
                                    <div class="komentar-header">
                                        <span class="name"><?php echo htmlspecialchars($k['user_name'] ?? 'Unknown'); ?></span>
                                        <span class="date"><?php echo date('d/m/Y H:i', strtotime($k['created_at'])); ?></span>
                                    </div>
                                    <div class="komentar-body">
                                        <?php echo nl2br(htmlspecialchars($k['komentar'])); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p style="color:#888; margin-top:15px;">Belum ada komentar. Jadilah yang pertama!</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>

        <footer class="footer">
            <p>&copy; <?php echo date('Y'); ?> Pusat Perangkat Pembelajaran</p>
        </footer>
    </div>
</body>
</html>