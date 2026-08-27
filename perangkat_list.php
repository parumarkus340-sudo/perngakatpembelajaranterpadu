<?php
// perangkat_list.php - Daftar Dokumen Per Jenis
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include_once 'config/database.php';

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];
$sekolah_id = $_SESSION['sekolah_id'] ?? 0;

// Ambil jenis dari URL
$jenis = isset($_GET['jenis']) ? mysqli_real_escape_string($conn, $_GET['jenis']) : '';
$filter_mapel = isset($_GET['mapel']) ? (int)$_GET['mapel'] : 0;
$filter_kelas = isset($_GET['kelas']) ? (int)$_GET['kelas'] : 0;
$filter_semester = isset($_GET['semester']) ? mysqli_real_escape_string($conn, $_GET['semester']) : '';

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

// Jenis dokumen
$jenis_dokumen = [
    'cp' => ['label' => '📋 CP (Capaian Pembelajaran)', 'icon' => 'fa-file-alt', 'color' => '#4f46e5', 'desc' => 'Capaian Pembelajaran'],
    'atp' => ['label' => '📊 ATP (Alur Tujuan Pembelajaran)', 'icon' => 'fa-chart-line', 'color' => '#0d9488', 'desc' => 'Alur Tujuan Pembelajaran'],
    'prota' => ['label' => '📅 PROTA (Program Tahunan)', 'icon' => 'fa-calendar', 'color' => '#2563eb', 'desc' => 'Program Tahunan'],
    'promes' => ['label' => '📆 PROMES (Program Semester)', 'icon' => 'fa-calendar-alt', 'color' => '#7c3aed', 'desc' => 'Program Semester'],
    'jurnal' => ['label' => '📓 Jurnal Mengajar', 'icon' => 'fa-book', 'color' => '#d97706', 'desc' => 'Jurnal Mengajar'],
    'rpp' => ['label' => '📄 RPP', 'icon' => 'fa-file-pdf', 'color' => '#dc2626', 'desc' => 'Rencana Pelaksanaan Pembelajaran'],
    'modul' => ['label' => '📘 Modul Ajar', 'icon' => 'fa-book-open', 'color' => '#059669', 'desc' => 'Modul Ajar'],
    'penilaian' => ['label' => '📝 Penilaian', 'icon' => 'fa-check-double', 'color' => '#6f42c1', 'desc' => 'Penilaian'],
    'album' => ['label' => '🖼️ Album Kegiatan', 'icon' => 'fa-images', 'color' => '#ec4899', 'desc' => 'Album Kegiatan'],
    'catatan' => ['label' => '📒 Catatan Harian', 'icon' => 'fa-sticky-note', 'color' => '#f59e0b', 'desc' => 'Catatan Harian'],
    'raport' => ['label' => '📊 Raport', 'icon' => 'fa-file-alt', 'color' => '#3b82f6', 'desc' => 'Raport']
];

// Validasi jenis
if (!isset($jenis_dokumen[$jenis])) {
    header('Location: perangkat_dashboard.php');
    exit;
}

$jenis_info = $jenis_dokumen[$jenis];

// Query berdasarkan role
if ($role == 'guru') {
    $where = "d.id_guru = $user_id";
} elseif ($role == 'kepala_sekolah') {
    $where = "d.id_sekolah = $sekolah_id";
} elseif ($role == 'pengawas') {
    $sekolah_ids = getSekolahBinaanIds($user_id);
    $ids_str = !empty($sekolah_ids) ? implode(',', $sekolah_ids) : '0';
    $where = "d.id_sekolah IN ($ids_str)";
} else {
    $where = "1=1";
}

// Tambahkan filter
if ($filter_mapel > 0) $where .= " AND d.id_mapel = $filter_mapel";
if ($filter_kelas > 0) $where .= " AND d.id_kelas = $filter_kelas";
if (!empty($filter_semester)) $where .= " AND d.semester = '$filter_semester'";

// Ambil data dokumen
$dokumen = fetchAll("
    SELECT d.*, k.nama_kelas, mp.nama_mapel, u.name as guru_name, s.nama_sekolah
    FROM dokumen_perangkat d
    LEFT JOIN kelas k ON d.id_kelas = k.id
    LEFT JOIN mata_pelajaran mp ON d.id_mapel = mp.id
    LEFT JOIN users u ON d.id_guru = u.id
    LEFT JOIN sekolah s ON d.id_sekolah = s.id
    WHERE d.jenis = '$jenis' AND $where
    ORDER BY d.created_at DESC
");

// Ambil data untuk filter
$mapel_list = fetchAll("SELECT * FROM mata_pelajaran ORDER BY nama_mapel");
$kelas_list = fetchAll("SELECT * FROM kelas ORDER BY jenjang, nama_kelas");

// Fungsi status
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
    <title><?php echo $jenis_info['label']; ?> - Perangkat Pembelajaran</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/website_perangkat/public/css/style.css">
    <style>
        .header-list {
            background: linear-gradient(135deg, <?php echo $jenis_info['color']; ?> 0%, <?php echo $jenis_info['color']; ?>aa 100%);
        }
        .btn-filter {
            background: <?php echo $jenis_info['color']; ?> !important;
        }
        .btn-filter:hover {
            opacity: 0.85 !important;
        }
        .filter-box .form-group select:focus {
            border-color: <?php echo $jenis_info['color']; ?> !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- HEADER -->
        <header class="header header-list">
            <div>
                <h1><i class="fas <?php echo $jenis_info['icon']; ?>"></i> <?php echo $jenis_info['label']; ?></h1>
                <p><?php echo $jenis_info['desc']; ?></p>
            </div>
            <div class="badge-user">
                <i class="fas fa-file-alt"></i> <?php echo count($dokumen); ?> dokumen
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

            <!-- Filter -->
            <div class="filter-box">
                <div class="form-group">
                    <label>📚 Mata Pelajaran</label>
                    <select name="mapel" id="filterMapel" onchange="applyFilter()">
                        <option value="">-- Semua --</option>
                        <?php foreach ($mapel_list as $m): ?>
                            <option value="<?php echo $m['id']; ?>" <?php echo $filter_mapel == $m['id'] ? 'selected' : ''; ?>>
                                <?php echo $m['nama_mapel']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>🏫 Kelas</label>
                    <select name="kelas" id="filterKelas" onchange="applyFilter()">
                        <option value="">-- Semua --</option>
                        <?php foreach ($kelas_list as $k): ?>
                            <option value="<?php echo $k['id']; ?>" <?php echo $filter_kelas == $k['id'] ? 'selected' : ''; ?>>
                                Kelas <?php echo $k['nama_kelas']; ?> (<?php echo $k['jenjang']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>📖 Semester</label>
                    <select name="semester" id="filterSemester" onchange="applyFilter()">
                        <option value="">-- Semua --</option>
                        <option value="1" <?php echo $filter_semester == '1' ? 'selected' : ''; ?>>Semester 1</option>
                        <option value="2" <?php echo $filter_semester == '2' ? 'selected' : ''; ?>>Semester 2</option>
                    </select>
                </div>
                <div>
                    <a href="perangkat_list.php?jenis=<?php echo $jenis; ?>" class="btn btn-secondary">↺ Reset</a>
                </div>
            </div>

            <!-- Tombol Upload -->
            <?php if ($role == 'guru'): ?>
                <div style="margin-bottom:15px;">
                    <a href="perangkat_upload.php" class="btn-upload">
                        <i class="fas fa-upload"></i> Upload <?php echo $jenis_info['label']; ?>
                    </a>
                </div>
            <?php endif; ?>

            <!-- Daftar Dokumen -->
            <div class="section-title">
                <span>📄 Daftar Dokumen</span>
                <span class="total-count">Total: <?php echo count($dokumen); ?> dokumen</span>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
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
                            <?php $no = 1; foreach ($dokumen as $d): 
                                $tahun_ajaran = $d['tahun_ajaran'] ?? '-';
                                $tanggal_upload = date('d/m/Y H:i', strtotime($d['created_at']));
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
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
                                                <a href="?arsipkan=1&id=<?php echo $d['id']; ?>&jenis=<?php echo $jenis; ?>" class="archive" onclick="return confirm('Arsipkan dokumen ini?')">
                                                    <i class="fas fa-archive"></i> Arsipkan
                                                </a>
                                            <?php endif; ?>
                                            
                                            <!-- Hapus (untuk guru, semua status kecuali terverifikasi) -->
                                            <?php if ($role == 'guru' && $d['status'] != 'terverifikasi'): ?>
                                                <div class="divider"></div>
                                                <a href="?hapus=1&id=<?php echo $d['id']; ?>&jenis=<?php echo $jenis; ?>" class="delete" onclick="return confirm('Hapus dokumen ini? Tindakan ini tidak dapat dibatalkan!')">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </a>
                                            <?php endif; ?>
                                            
                                            <!-- Approve (untuk Kepala Sekolah) -->
                                            <?php if ($role == 'kepala_sekolah' && $d['status'] == 'pending_kepsek'): ?>
                                                <div class="divider"></div>
                                                <a href="?approve=1&id=<?php echo $d['id']; ?>&jenis=<?php echo $jenis; ?>" class="view" onclick="return confirm('Setujui dokumen ini?')">
                                                    <i class="fas fa-check-circle"></i> Setujui
                                                </a>
                                                <a href="?reject=1&id=<?php echo $d['id']; ?>&jenis=<?php echo $jenis; ?>" class="delete" onclick="return confirm('Tolak dokumen ini?')">
                                                    <i class="fas fa-times-circle"></i> Tolak
                                                </a>
                                            <?php endif; ?>
                                            
                                            <!-- Approve (untuk Pengawas) -->
                                            <?php if ($role == 'pengawas' && $d['status'] == 'pending_pengawas'): ?>
                                                <div class="divider"></div>
                                                <a href="?approve=1&id=<?php echo $d['id']; ?>&jenis=<?php echo $jenis; ?>" class="view" onclick="return confirm('Setujui dokumen ini?')">
                                                    <i class="fas fa-check-circle"></i> Setujui
                                                </a>
                                                <a href="?reject=1&id=<?php echo $d['id']; ?>&jenis=<?php echo $jenis; ?>" class="delete" onclick="return confirm('Tolak dokumen ini?')">
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
                                        <i class="fas <?php echo $jenis_info['icon']; ?>"></i>
                                        <h3>Belum Ada Dokumen</h3>
                                        <p>Belum ada dokumen <?php echo $jenis_info['label']; ?> yang tersedia.</p>
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
        // FILTER
        // ============================================
        function applyFilter() {
            const mapel = document.getElementById('filterMapel').value;
            const kelas = document.getElementById('filterKelas').value;
            const semester = document.getElementById('filterSemester').value;
            
            let url = 'perangkat_list.php?jenis=<?php echo $jenis; ?>';
            if (mapel) url += '&mapel=' + mapel;
            if (kelas) url += '&kelas=' + kelas;
            if (semester) url += '&semester=' + semester;
            
            window.location.href = url;
        }

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