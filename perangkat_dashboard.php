<?php
// perangkat_dashboard.php - Dashboard Manajemen Perangkat Pembelajaran
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];
$sekolah_id = $_SESSION['sekolah_id'] ?? 0;

include_once 'config/database.php';

// ============================================
// PROSES ARSIPKAN DOKUMEN
// ============================================
if (isset($_GET['arsipkan']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "UPDATE dokumen_perangkat SET status = 'draft' WHERE id = $id";
    if (query($sql)) {
        $success = "✅ Dokumen berhasil diarsipkan!";
    }
}

// ============================================
// PROSES HAPUS DOKUMEN
// ============================================
if (isset($_GET['hapus']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $dok = fetchOne("SELECT file_path FROM dokumen_perangkat WHERE id = $id");
    if ($dok && !empty($dok['file_path']) && file_exists($dok['file_path'])) {
        unlink($dok['file_path']);
    }
    $sql = "DELETE FROM dokumen_perangkat WHERE id = $id";
    if (query($sql)) {
        $success = "✅ Dokumen berhasil dihapus!";
    }
}

// ============================================
// PROSES APPROVE/REJECT (KEPSEK & PENGAWAS)
// ============================================
if (isset($_GET['approve']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($role == 'kepala_sekolah') {
        $sql = "UPDATE dokumen_perangkat SET status = 'pending_pengawas' WHERE id = $id";
    } elseif ($role == 'pengawas') {
        $sql = "UPDATE dokumen_perangkat SET status = 'terverifikasi' WHERE id = $id";
    }
    if (query($sql)) {
        $success = "✅ Dokumen berhasil disetujui!";
    }
}

if (isset($_GET['reject']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($role == 'kepala_sekolah') {
        $sql = "UPDATE dokumen_perangkat SET status = 'ditolak_kepsek' WHERE id = $id";
    } elseif ($role == 'pengawas') {
        $sql = "UPDATE dokumen_perangkat SET status = 'ditolak_pengawas' WHERE id = $id";
    }
    if (query($sql)) {
        $success = "❌ Dokumen ditolak!";
    }
}

// ============================================
// JENIS DOKUMEN
// ============================================
$jenis_dokumen = [
    'cp' => ['label' => '📋 CP', 'icon' => 'fa-file-alt', 'color' => '#4f46e5', 'desc' => 'Capaian Pembelajaran'],
    'atp' => ['label' => '📊 ATP', 'icon' => 'fa-chart-line', 'color' => '#0d9488', 'desc' => 'Alur Tujuan Pembelajaran'],
    'prota' => ['label' => '📅 PROTA', 'icon' => 'fa-calendar', 'color' => '#2563eb', 'desc' => 'Program Tahunan'],
    'promes' => ['label' => '📆 PROMES', 'icon' => 'fa-calendar-alt', 'color' => '#7c3aed', 'desc' => 'Program Semester'],
    'jurnal' => ['label' => '📓 Jurnal', 'icon' => 'fa-book', 'color' => '#d97706', 'desc' => 'Jurnal Mengajar'],
    'rpp' => ['label' => '📄 RPP', 'icon' => 'fa-file-pdf', 'color' => '#dc2626', 'desc' => 'Rencana Pelaksanaan Pembelajaran'],
    'modul' => ['label' => '📘 Modul', 'icon' => 'fa-book-open', 'color' => '#059669', 'desc' => 'Modul Ajar'],
    'penilaian' => ['label' => '📝 Penilaian', 'icon' => 'fa-check-double', 'color' => '#6f42c1', 'desc' => 'Penilaian'],
    'album' => ['label' => '🖼️ Album', 'icon' => 'fa-images', 'color' => '#ec4899', 'desc' => 'Album Kegiatan'],
    'catatan' => ['label' => '📒 Catatan', 'icon' => 'fa-sticky-note', 'color' => '#f59e0b', 'desc' => 'Catatan Harian'],
    'raport' => ['label' => '📊 Raport', 'icon' => 'fa-file-alt', 'color' => '#3b82f6', 'desc' => 'Raport']
];

// ============================================
// AMBIL DATA DOKUMEN BERDASARKAN ROLE
// ============================================
if ($role == 'guru') {
    $dokumen = fetchAll("
        SELECT d.*, k.nama_kelas, mp.nama_mapel, u.name as guru_name, s.nama_sekolah
        FROM dokumen_perangkat d
        LEFT JOIN kelas k ON d.id_kelas = k.id
        LEFT JOIN mata_pelajaran mp ON d.id_mapel = mp.id
        LEFT JOIN users u ON d.id_guru = u.id
        LEFT JOIN sekolah s ON d.id_sekolah = s.id
        WHERE d.id_guru = $user_id
        ORDER BY d.created_at DESC
    ");
} elseif ($role == 'kepala_sekolah') {
    $dokumen = fetchAll("
        SELECT d.*, k.nama_kelas, mp.nama_mapel, u.name as guru_name, s.nama_sekolah
        FROM dokumen_perangkat d
        LEFT JOIN kelas k ON d.id_kelas = k.id
        LEFT JOIN mata_pelajaran mp ON d.id_mapel = mp.id
        LEFT JOIN users u ON d.id_guru = u.id
        LEFT JOIN sekolah s ON d.id_sekolah = s.id
        WHERE d.id_sekolah = $sekolah_id
        ORDER BY d.created_at DESC
    ");
} elseif ($role == 'pengawas') {
    $sekolah_ids = getSekolahBinaanIds($user_id);
    $ids_str = !empty($sekolah_ids) ? implode(',', $sekolah_ids) : '0';
    $dokumen = fetchAll("
        SELECT d.*, k.nama_kelas, mp.nama_mapel, u.name as guru_name, s.nama_sekolah
        FROM dokumen_perangkat d
        LEFT JOIN kelas k ON d.id_kelas = k.id
        LEFT JOIN mata_pelajaran mp ON d.id_mapel = mp.id
        LEFT JOIN users u ON d.id_guru = u.id
        LEFT JOIN sekolah s ON d.id_sekolah = s.id
        WHERE d.id_sekolah IN ($ids_str)
        ORDER BY d.created_at DESC
    ");
} else {
    $dokumen = fetchAll("
        SELECT d.*, k.nama_kelas, mp.nama_mapel, u.name as guru_name, s.nama_sekolah
        FROM dokumen_perangkat d
        LEFT JOIN kelas k ON d.id_kelas = k.id
        LEFT JOIN mata_pelajaran mp ON d.id_mapel = mp.id
        LEFT JOIN users u ON d.id_guru = u.id
        LEFT JOIN sekolah s ON d.id_sekolah = s.id
        ORDER BY d.created_at DESC
    ");
}

// ============================================
// STATISTIK PER JENIS
// ============================================
$stats = [];
foreach ($jenis_dokumen as $key => $j) {
    $count = 0;
    foreach ($dokumen as $d) {
        if ($d['jenis'] == $key) $count++;
    }
    $stats[$key] = $count;
}

// ============================================
// FUNGSI STATUS
// ============================================
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

if (!function_exists('getStatusClass')) {
    function getStatusClass($status) {
        $classes = [
            'draft' => 'badge-draft',
            'pending_kepsek' => 'badge-pending',
            'ditolak_kepsek' => 'badge-ditolak',
            'pending_pengawas' => 'badge-pending',
            'ditolak_pengawas' => 'badge-ditolak',
            'terverifikasi' => 'badge-terverifikasi'
        ];
        return $classes[$status] ?? 'badge-draft';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perangkat Pembelajaran - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/website_perangkat/public/css/style.css">
</head>
<body>
    <div class="container">
        <!-- HEADER -->
        <header class="header header-perangkat">
            <div>
                <h1><i class="fas fa-folder-open"></i> Perangkat Pembelajaran</h1>
                <p>Kelola semua dokumen perangkat pembelajaran</p>
            </div>
            <div class="badge-user">
                <i class="fas fa-user"></i> <?php echo $_SESSION['name']; ?>
                <span class="role-label">(<?php echo $role; ?>)</span>
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

            <!-- MENU GRID -->
            <div class="perangkat-menu-grid">
                <?php foreach ($jenis_dokumen as $key => $j): 
                    $count = $stats[$key] ?? 0;
                ?>
                    <a href="perangkat_list.php?jenis=<?php echo $key; ?>" class="perangkat-menu-card">
                        <span class="icon"><i class="fas <?php echo $j['icon']; ?>" style="color:<?php echo $j['color']; ?>;"></i></span>
                        <span class="label"><?php echo $j['label']; ?></span>
                        <span class="count"><?php echo $count; ?> dokumen</span>
                        <?php if ($count > 0): ?>
                            <span class="count-badge"><?php echo $count; ?></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- TOMBOL UPLOAD -->
            <?php if ($role == 'guru'): ?>
                <a href="perangkat_upload.php" class="btn-upload">
                    <i class="fas fa-upload"></i> Upload Dokumen (Google Drive)
                </a>
            <?php endif; ?>

            <!-- DAFTAR DOKUMEN -->
            <div class="section-title">
                <span>📄 Daftar Dokumen</span>
                <span class="total-count">Total: <?php echo count($dokumen); ?> dokumen</span>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Jenis</th>
                            <th>Judul</th>
                            <th>Guru</th>
                            <th>Sekolah</th>
                            <th>Kelas</th>
                            <th>Mapel</th>
                            <th>Tahun Pelajaran</th>
                            <th>Tanggal Upload</th>
                            <th>Status</th>
                            <th style="text-align:center; width:140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($dokumen) > 0): ?>
                            <?php 
                            $tampil = array_slice($dokumen, 0, 20);
                            foreach ($tampil as $d): 
                                $j = $jenis_dokumen[$d['jenis']] ?? ['label' => $d['jenis'], 'icon' => 'fa-file', 'color' => '#888'];
                                $tahun_ajaran = $d['tahun_ajaran'] ?? '-';
                                $tanggal_upload = date('d/m/Y H:i', strtotime($d['created_at']));
                            ?>
                            <tr>
                                <td>
                                    <i class="fas <?php echo $j['icon'] ?? 'fa-file'; ?>" style="color:<?php echo $j['color'] ?? '#888'; ?>;"></i>
                                    <?php echo $j['label'] ?? $d['jenis']; ?>
                                </td>
                                <td><strong><?php echo $d['judul']; ?></strong></td>
                                <td><?php echo $d['guru_name'] ?? '-'; ?></td>
                                <td><?php echo $d['nama_sekolah'] ?? '-'; ?></td>
                                <td><?php echo $d['nama_kelas'] ?? '-'; ?></td>
                                <td><?php echo $d['nama_mapel'] ?? '-'; ?></td>
                                <td><?php echo $tahun_ajaran; ?></td>
                                <td class="text-small text-muted"><?php echo $tanggal_upload; ?></td>
                                <td>
                                    <span class="<?php echo getStatusClass($d['status']); ?>">
                                        <?php echo getStatusLabel($d['status']); ?>
                                    </span>
                                </td>
                                <td style="text-align:center;">
                                    <div class="action-dropdown">
                                        <button class="dropdown-btn" onclick="toggleDropdown(this)">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="dropdown-content">
                                            <!-- Lihat -->
                                            <a href="perangkat_detail.php?id=<?php echo $d['id']; ?>" class="view">
                                                <i class="fas fa-eye"></i> Lihat
                                            </a>
                                            
                                            <!-- Edit (untuk guru, semua status kecuali terverifikasi) -->
                                            <?php if ($role == 'guru' && $d['status'] != 'terverifikasi'): ?>
                                                <a href="perangkat_edit.php?id=<?php echo $d['id']; ?>" class="edit">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            <?php endif; ?>
                                            
                                            <!-- Arsipkan (untuk guru) -->
                                            <?php if ($role == 'guru'): ?>
                                                <a href="?arsipkan=1&id=<?php echo $d['id']; ?>" class="archive" onclick="return confirm('Arsipkan dokumen ini?')">
                                                    <i class="fas fa-archive"></i> Arsipkan
                                                </a>
                                            <?php endif; ?>
                                            
                                            <!-- Hapus (untuk guru, semua status kecuali terverifikasi) -->
                                            <?php if ($role == 'guru' && $d['status'] != 'terverifikasi'): ?>
                                                <div class="divider"></div>
                                                <a href="?hapus=1&id=<?php echo $d['id']; ?>" class="delete" onclick="return confirm('Hapus dokumen ini? Tindakan ini tidak dapat dibatalkan!')">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </a>
                                            <?php endif; ?>
                                            
                                            <!-- Approve (untuk Kepala Sekolah) -->
                                            <?php if ($role == 'kepala_sekolah' && $d['status'] == 'pending_kepsek'): ?>
                                                <div class="divider"></div>
                                                <a href="?approve=1&id=<?php echo $d['id']; ?>" class="view" onclick="return confirm('Setujui dokumen ini?')">
                                                    <i class="fas fa-check-circle"></i> Setujui
                                                </a>
                                                <a href="?reject=1&id=<?php echo $d['id']; ?>" class="delete" onclick="return confirm('Tolak dokumen ini?')">
                                                    <i class="fas fa-times-circle"></i> Tolak
                                                </a>
                                            <?php endif; ?>
                                            
                                            <!-- Approve (untuk Pengawas) -->
                                            <?php if ($role == 'pengawas' && $d['status'] == 'pending_pengawas'): ?>
                                                <div class="divider"></div>
                                                <a href="?approve=1&id=<?php echo $d['id']; ?>" class="view" onclick="return confirm('Setujui dokumen ini?')">
                                                    <i class="fas fa-check-circle"></i> Setujui
                                                </a>
                                                <a href="?reject=1&id=<?php echo $d['id']; ?>" class="delete" onclick="return confirm('Tolak dokumen ini?')">
                                                    <i class="fas fa-times-circle"></i> Tolak
                                                </a>
                                            <?php endif; ?>
                                            
                                            <!-- Buka Google Drive -->
                                            <?php if (!empty($d['drive_link'])): ?>
                                                <div class="divider"></div>
                                                <a href="<?php echo $d['drive_link']; ?>" target="_blank" class="drive">
                                                    <i class="fab fa-google-drive"></i> Buka Drive
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10">
                                    <div class="empty-state">
                                        <i class="fas fa-folder-open"></i>
                                        <h3>Belum Ada Dokumen</h3>
                                        <p>Belum ada dokumen perangkat pembelajaran yang tersedia.</p>
                                        <?php if ($role == 'guru'): ?>
                                            <p style="margin-top:10px;">
                                                <a href="perangkat_upload.php" class="btn-upload" style="display:inline-block;">
                                                    <i class="fas fa-upload"></i> Upload Sekarang
                                                </a>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>

        <!-- FOOTER -->
        <footer class="footer">
            <p>&copy; <?php echo date('Y'); ?> Pusat Perangkat Pembelajaran</p>
        </footer>
    </div>

    <script>
        // ============================================
        // DROPDOWN TOGGLE
        // ============================================
        function toggleDropdown(btn) {
            // Tutup semua dropdown lain
            document.querySelectorAll('.dropdown-content').forEach(function(el) {
                if (el !== btn.nextElementSibling) {
                    el.classList.remove('show');
                }
            });
            // Toggle dropdown ini
            var dropdown = btn.nextElementSibling;
            dropdown.classList.toggle('show');
        }

        // Tutup dropdown saat klik di luar
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.action-dropdown')) {
                document.querySelectorAll('.dropdown-content').forEach(function(el) {
                    el.classList.remove('show');
                });
            }
        });
    </script>
</body>
</html>