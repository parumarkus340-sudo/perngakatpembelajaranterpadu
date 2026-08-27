<?php
// perangkat_detail.php - Detail Dokumen Perangkat Pembelajaran
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include_once 'config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id == 0) {
    header('Location: perangkat_dashboard.php');
    exit;
}

$dokumen = fetchOne("
    SELECT d.*, k.nama_kelas, mp.nama_mapel, u.name as guru_name, u.email as guru_email, s.nama_sekolah
    FROM dokumen_perangkat d
    LEFT JOIN kelas k ON d.id_kelas = k.id
    LEFT JOIN mata_pelajaran mp ON d.id_mapel = mp.id
    LEFT JOIN users u ON d.id_guru = u.id
    LEFT JOIN sekolah s ON d.id_sekolah = s.id
    WHERE d.id = $id
");

if (!$dokumen) {
    header('Location: perangkat_dashboard.php');
    exit;
}

query("UPDATE dokumen_perangkat SET views = views + 1 WHERE id = $id");

$jenis_dokumen = [
    'cp' => ['label' => '📋 CP', 'icon' => 'fa-file-alt', 'color' => '#4f46e5'],
    'atp' => ['label' => '📊 ATP', 'icon' => 'fa-chart-line', 'color' => '#0d9488'],
    'prota' => ['label' => '📅 PROTA', 'icon' => 'fa-calendar', 'color' => '#2563eb'],
    'promes' => ['label' => '📆 PROMES', 'icon' => 'fa-calendar-alt', 'color' => '#7c3aed'],
    'jurnal' => ['label' => '📓 Jurnal', 'icon' => 'fa-book', 'color' => '#d97706'],
    'rpp' => ['label' => '📄 RPP', 'icon' => 'fa-file-pdf', 'color' => '#dc2626'],
    'modul' => ['label' => '📘 Modul', 'icon' => 'fa-book-open', 'color' => '#059669'],
    'penilaian' => ['label' => '📝 Penilaian', 'icon' => 'fa-check-double', 'color' => '#6f42c1'],
    'album' => ['label' => '🖼️ Album', 'icon' => 'fa-images', 'color' => '#ec4899'],
    'catatan' => ['label' => '📒 Catatan', 'icon' => 'fa-sticky-note', 'color' => '#f59e0b'],
    'raport' => ['label' => '📊 Raport', 'icon' => 'fa-file-alt', 'color' => '#3b82f6']
];

$j_info = $jenis_dokumen[$dokumen['jenis']] ?? ['label' => $dokumen['jenis'], 'icon' => 'fa-file', 'color' => '#888'];

if (!function_exists('getStatusLabel')) {
    function getStatusLabel($status) {
        $labels = [
            'draft' => '📝 Draft',
            'pending_kepsek' => '⏳ Menunggu Kepsek',
            'ditolak_kepsek' => '❌ Ditolak Kepsek',
            'pending_pengawas' => '⏳ Menunggu Pengawas',
            'ditolak_pengawas' => '❌ Ditolak Pengawas',
            'terverifikasi' => '✅ Terverifikasi'
        ];
        return $labels[$status] ?? $status;
    }
}

// Fungsi ekstrak Google Drive ID
function getDriveFileId($link) {
    preg_match('/\/d\/([^\/]+)/', $link, $matches);
    if (isset($matches[1])) return $matches[1];
    preg_match('/\/d\/([^\/]+)/', $link, $matches);
    return $matches[1] ?? null;
}

$drive_file_id = $dokumen['drive_link'] ? getDriveFileId($dokumen['drive_link']) : null;
$drive_embed_link = $drive_file_id ? "https://drive.google.com/file/d/$drive_file_id/preview" : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $dokumen['judul']; ?> - Perangkat Pembelajaran</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/website_perangkat/public/css/style.css">
    <style>
        .header-detail {
            background: linear-gradient(135deg, <?php echo $j_info['color']; ?> 0%, <?php echo $j_info['color']; ?>aa 100%);
        }
        .status-badge {
            display: inline-block;
            padding: 4px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }
        .status-badge.draft { background: #e2e3e5; color: #383d41; }
        .status-badge.pending { background: #fff3cd; color: #856404; }
        .status-badge.ditolak { background: #f8d7da; color: #721c24; }
        .status-badge.terverifikasi { background: #d4edda; color: #155724; }
    </style>
</head>
<body>
    <div class="container">
        <header class="header header-detail">
            <div>
                <h1><i class="fas <?php echo $j_info['icon']; ?>"></i> Detail Dokumen</h1>
                <p><?php echo $j_info['label']; ?></p>
            </div>
            <div class="badge-user">
                <i class="fas fa-user"></i> <?php echo $_SESSION['name']; ?>
            </div>
        </header>

        <?php include_once 'navbar.php'; ?>

        <main>
            <div class="detail-card">
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                    <span class="status-badge <?php echo $dokumen['status']; ?>">
                        <?php echo getStatusLabel($dokumen['status']); ?>
                    </span>
                    <span style="font-size:13px; color:#888;">
                        <i class="far fa-calendar-alt"></i> <?php echo date('d F Y H:i', strtotime($dokumen['created_at'])); ?>
                    </span>
                </div>

                <h1 class="title"><?php echo $dokumen['judul']; ?></h1>

                <div class="meta">
                    <span><i class="fas fa-user-tie"></i> <?php echo $dokumen['guru_name'] ?? '-'; ?></span>
                    <span><i class="fas fa-school"></i> <?php echo $dokumen['nama_sekolah'] ?? '-'; ?></span>
                    <span><i class="fas fa-book"></i> <?php echo $dokumen['nama_mapel'] ?? '-'; ?></span>
                    <span><i class="fas fa-chalkboard"></i> <?php echo $dokumen['nama_kelas'] ?? '-'; ?></span>
                    <?php if ($dokumen['semester']): ?>
                        <span><i class="fas fa-calendar-alt"></i> Semester <?php echo $dokumen['semester']; ?></span>
                    <?php endif; ?>
                    <?php if ($dokumen['tahun_ajaran']): ?>
                        <span><i class="fas fa-calendar"></i> <?php echo $dokumen['tahun_ajaran']; ?></span>
                    <?php endif; ?>
                </div>

                <?php if (!empty($dokumen['deskripsi'])): ?>
                    <div class="deskripsi">
                        <strong>📝 Deskripsi:</strong>
                        <p style="margin-top:8px;"><?php echo nl2br(htmlspecialchars($dokumen['deskripsi'])); ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($dokumen['drive_link'])): ?>
                    <div class="drive-viewer">
                        <div class="viewer-header">
                            <div class="file-info">
                                <i class="fab fa-google-drive"></i>
                                <span style="font-weight:500;">Google Drive</span>
                                <span style="font-size:12px; color:#888;">File tersimpan di Google Drive</span>
                            </div>
                            <div>
                                <a href="<?php echo $dokumen['drive_link']; ?>" target="_blank" class="btn btn-google" style="padding:6px 16px; font-size:12px;">
                                    <i class="fas fa-external-link-alt"></i> Buka di Drive
                                </a>
                            </div>
                        </div>
                        <div class="viewer-body">
                            <?php if ($drive_embed_link): ?>
                                <iframe src="<?php echo $drive_embed_link; ?>" allowfullscreen></iframe>
                            <?php else: ?>
                                <div class="placeholder">
                                    <i class="fab fa-google-drive"></i>
                                    <h4>Preview tidak tersedia</h4>
                                    <p>Klik tombol "Buka di Drive" untuk melihat file.</p>
                                    <a href="<?php echo $dokumen['drive_link']; ?>" target="_blank" class="btn btn-google" style="margin-top:12px;">
                                        <i class="fas fa-external-link-alt"></i> Buka di Google Drive
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i> Dokumen tidak memiliki link Google Drive.
                    </div>
                <?php endif; ?>

                <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:20px;">
                    <a href="<?php echo $_SERVER['HTTP_REFERER'] ?? 'perangkat_dashboard.php'; ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <?php if (!empty($dokumen['drive_link'])): ?>
                        <a href="<?php echo $dokumen['drive_link']; ?>" target="_blank" class="btn btn-google">
                            <i class="fab fa-google-drive"></i> Buka di Google Drive
                        </a>
                    <?php endif; ?>
                    <?php if ($_SESSION['role'] == 'guru' && $dokumen['status'] == 'draft'): ?>
                        <a href="perangkat_edit.php?id=<?php echo $dokumen['id']; ?>" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
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