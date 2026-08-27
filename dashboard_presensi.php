<?php
// dashboard_presensi.php - Dashboard Monitoring Presensi (Kepsek, Pengawas, Dinas, Admin)
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include_once 'config/database.php';

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];
$sekolah_id = $_SESSION['sekolah_id'] ?? 0;
$tanggal = date('Y-m-d');
$bulan = date('m');
$tahun = date('Y');

// Filter
$filter_sekolah = isset($_GET['sekolah']) ? (int)$_GET['sekolah'] : 0;
$filter_tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : $tanggal;
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : $bulan;
$filter_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : $tahun;

// Query dasar untuk filter sekolah
$where_sekolah = "1=1";
if ($role == 'kepala_sekolah' && $sekolah_id > 0) {
    $where_sekolah = "s.id = $sekolah_id";
} elseif ($role == 'pengawas') {
    $sekolah_binaan = getSekolahBinaanIds($user_id);
    if (!empty($sekolah_binaan)) {
        $ids = implode(',', $sekolah_binaan);
        $where_sekolah = "s.id IN ($ids)";
    } else {
        $where_sekolah = "1=0";
    }
} elseif ($filter_sekolah > 0) {
    $where_sekolah = "s.id = $filter_sekolah";
}

// ============================================
// DATA UNTUK KEPSEK
// ============================================
if ($role == 'kepala_sekolah' && $sekolah_id > 0) {
    $sekolah_data = fetchOne("
        SELECT s.*, 
               COUNT(DISTINCT u.id) as total_guru,
               (SELECT COUNT(*) FROM users WHERE role = 'guru' AND sekolah_id = s.id AND is_active = 1) as guru_aktif
        FROM sekolah s
        LEFT JOIN users u ON u.sekolah_id = s.id AND u.role = 'guru'
        WHERE s.id = $sekolah_id
        GROUP BY s.id
    ");
}

// ============================================
// STATISTIK PRESENSI GURU
// ============================================
$stat_guru = fetchOne("
    SELECT 
        COUNT(DISTINCT pg.id_guru) as total_guru_presensi,
        SUM(CASE WHEN pg.status = 'hadir' THEN 1 ELSE 0 END) as hadir,
        SUM(CASE WHEN pg.status = 'izin' THEN 1 ELSE 0 END) as izin,
        SUM(CASE WHEN pg.status = 'sakit' THEN 1 ELSE 0 END) as sakit,
        SUM(CASE WHEN pg.status = 'alpa' THEN 1 ELSE 0 END) as alpa,
        SUM(CASE WHEN pg.status = 'cuti' THEN 1 ELSE 0 END) as cuti
    FROM presensi_guru pg
    JOIN users u ON pg.id_guru = u.id
    JOIN sekolah s ON pg.id_sekolah = s.id
    WHERE pg.tanggal = '$filter_tanggal' AND $where_sekolah
");

// ============================================
// STATISTIK PRESENSI KEPALA SEKOLAH
// ============================================
$stat_kepsek = fetchOne("
    SELECT 
        COUNT(DISTINCT pg.id_guru) as total_kepsek_presensi,
        SUM(CASE WHEN pg.status = 'hadir' THEN 1 ELSE 0 END) as hadir,
        SUM(CASE WHEN pg.status = 'izin' THEN 1 ELSE 0 END) as izin,
        SUM(CASE WHEN pg.status = 'sakit' THEN 1 ELSE 0 END) as sakit,
        SUM(CASE WHEN pg.status = 'alpa' THEN 1 ELSE 0 END) as alpa
    FROM presensi_guru pg
    JOIN users u ON pg.id_guru = u.id
    JOIN sekolah s ON pg.id_sekolah = s.id
    WHERE pg.tanggal = '$filter_tanggal' 
      AND $where_sekolah
      AND u.role = 'kepala_sekolah'
");

// ============================================
// STATISTIK PRESENSI SISWA
// ============================================
$stat_siswa = fetchOne("
    SELECT 
        SUM(p.total_siswa) as total_siswa,
        SUM(p.hadir) as hadir,
        SUM(p.sakit) as sakit,
        SUM(p.izin) as izin,
        SUM(p.alpa) as alpa
    FROM presensi_siswa p
    JOIN sekolah s ON p.id_sekolah = s.id
    WHERE p.tanggal = '$filter_tanggal' AND $where_sekolah
");

// ============================================
// DATA PRESENSI KEPALA SEKOLAH PER SEKOLAH
// ============================================
$data_kepsek = fetchAll("
    SELECT 
        u.id as kepsek_id,
        u.name as kepsek_name,
        u.nip as kepsek_nip,
        s.id as sekolah_id,
        s.nama_sekolah,
        pg.id as presensi_id,
        pg.tanggal,
        pg.jam_masuk,
        pg.jam_keluar,
        pg.status as presensi_status,
        CASE 
            WHEN pg.id IS NULL THEN 'Belum Presensi'
            WHEN pg.jam_masuk IS NOT NULL AND pg.jam_keluar IS NULL THEN 'Masuk'
            WHEN pg.jam_masuk IS NOT NULL AND pg.jam_keluar IS NOT NULL THEN 'Selesai'
            ELSE 'Belum Presensi'
        END as status_kehadiran
    FROM users u
    JOIN sekolah s ON u.sekolah_id = s.id
    LEFT JOIN presensi_guru pg ON pg.id_guru = u.id AND pg.tanggal = '$filter_tanggal'
    WHERE u.role = 'kepala_sekolah' 
      AND $where_sekolah
      AND u.is_active = 1
    ORDER BY s.nama_sekolah, u.name
");

// ============================================
// DATA PER SEKOLAH (GURU + SISWA)
// ============================================
$data_sekolah = fetchAll("
    SELECT 
        s.id, s.nama_sekolah, s.npsn,
        COUNT(DISTINCT u.id) as total_guru,
        COUNT(DISTINCT pg.id_guru) as guru_presensi,
        SUM(CASE WHEN pg.status = 'hadir' THEN 1 ELSE 0 END) as guru_hadir,
        SUM(CASE WHEN pg.status = 'izin' THEN 1 ELSE 0 END) as guru_izin,
        SUM(CASE WHEN pg.status = 'sakit' THEN 1 ELSE 0 END) as guru_sakit,
        SUM(CASE WHEN pg.status = 'alpa' THEN 1 ELSE 0 END) as guru_alpa,
        SUM(p.total_siswa) as total_siswa,
        SUM(p.hadir) as siswa_hadir,
        SUM(p.sakit) as siswa_sakit,
        SUM(p.izin) as siswa_izin,
        SUM(p.alpa) as siswa_alpa
    FROM sekolah s
    LEFT JOIN users u ON u.sekolah_id = s.id AND u.role = 'guru'
    LEFT JOIN presensi_guru pg ON pg.id_guru = u.id AND pg.tanggal = '$filter_tanggal'
    LEFT JOIN presensi_siswa p ON p.id_sekolah = s.id AND p.tanggal = '$filter_tanggal'
    WHERE $where_sekolah
    GROUP BY s.id
    ORDER BY s.nama_sekolah
");

// ============================================
// DATA PRESENSI GURU PER HARI (untuk chart)
// ============================================
$presensi_harian = fetchAll("
    SELECT 
        DATE(pg.tanggal) as tanggal,
        COUNT(DISTINCT pg.id_guru) as total,
        SUM(CASE WHEN pg.status = 'hadir' THEN 1 ELSE 0 END) as hadir,
        SUM(CASE WHEN pg.status = 'izin' THEN 1 ELSE 0 END) as izin,
        SUM(CASE WHEN pg.status = 'sakit' THEN 1 ELSE 0 END) as sakit,
        SUM(CASE WHEN pg.status = 'alpa' THEN 1 ELSE 0 END) as alpa
    FROM presensi_guru pg
    JOIN users u ON pg.id_guru = u.id
    JOIN sekolah s ON pg.id_sekolah = s.id
    WHERE MONTH(pg.tanggal) = $filter_bulan 
      AND YEAR(pg.tanggal) = $filter_tahun
      AND $where_sekolah
      AND u.role = 'guru'
    GROUP BY DATE(pg.tanggal)
    ORDER BY pg.tanggal ASC
");

// ============================================
// DATA PRESENSI SISWA PER HARI (untuk chart)
// ============================================
$presensi_siswa_harian = fetchAll("
    SELECT 
        DATE(p.tanggal) as tanggal,
        SUM(p.total_siswa) as total,
        SUM(p.hadir) as hadir,
        SUM(p.sakit) as sakit,
        SUM(p.izin) as izin,
        SUM(p.alpa) as alpa
    FROM presensi_siswa p
    JOIN sekolah s ON p.id_sekolah = s.id
    WHERE MONTH(p.tanggal) = $filter_bulan 
      AND YEAR(p.tanggal) = $filter_tahun
      AND $where_sekolah
    GROUP BY DATE(p.tanggal)
    ORDER BY p.tanggal ASC
");

// Daftar sekolah untuk filter
$sekolah_list = fetchAll("SELECT * FROM sekolah ORDER BY nama_sekolah");

// Nama hari
$hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$nama_hari = $hari[date('w', strtotime($filter_tanggal))];

// Role display
$role_label = [
    'admin' => '⚙️ Admin',
    'guru' => '👨‍🏫 Guru',
    'kepala_sekolah' => '👔 Kepala Sekolah',
    'pengawas' => '🔍 Pengawas',
    'dinas' => '📊 Dinas'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Presensi - <?php echo $role_label[$role] ?? 'Sistem'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="/website_perangkat/public/css/style.css">
    <style>
        /* ============================================
           DASHBOARD PRESENSI - CSS LENGKAP
        ============================================ */
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: linear-gradient(135deg, #6f42c1 0%, #8b5cf6 100%);
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

        .filter-box {
            background: white;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid rgba(0,0,0,0.04);
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: end;
        }
        .filter-box .form-group {
            flex: 1;
            min-width: 140px;
        }
        .filter-box .form-group label {
            display: block;
            font-weight: 600;
            font-size: 12px;
            color: #555;
            margin-bottom: 4px;
        }
        .filter-box .form-group input,
        .filter-box .form-group select {
            width: 100%;
            padding: 8px 12px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 13px;
        }
        .filter-box .form-group input:focus,
        .filter-box .form-group select:focus {
            border-color: #6f42c1;
            outline: none;
        }
        .btn-filter {
            padding: 8px 24px;
            background: #6f42c1;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            font-size: 13px;
        }
        .btn-filter:hover { background: #5a32a3; }
        .btn-reset {
            padding: 8px 20px;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            font-size: 13px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-reset:hover { background: #5a6268; }

        .sekolah-info-card {
            background: white;
            border-radius: 12px;
            padding: 20px 25px;
            margin-bottom: 20px;
            border: 1px solid rgba(0,0,0,0.04);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .sekolah-info-card .info h3 {
            font-size: 18px;
            color: #0f2b5c;
            margin: 0;
        }
        .sekolah-info-card .info p {
            font-size: 13px;
            color: #6b7280;
            margin: 4px 0 0 0;
        }
        .sekolah-info-card .stats {
            display: flex;
            gap: 24px;
            flex-wrap: wrap;
        }
        .sekolah-info-card .stats .stat-item {
            text-align: center;
        }
        .sekolah-info-card .stats .stat-item .num {
            font-size: 22px;
            font-weight: 700;
            color: #6f42c1;
        }
        .sekolah-info-card .stats .stat-item .label {
            font-size: 11px;
            color: #6b7280;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: white;
            padding: 18px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.04);
            transition: all 0.3s ease;
        }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 30px rgba(0,0,0,0.10); }
        .stat-card .number {
            font-size: 2em;
            font-weight: 700;
            color: #6f42c1;
        }
        .stat-card .number.green { color: #28a745; }
        .stat-card .number.orange { color: #d97706; }
        .stat-card .number.red { color: #dc3545; }
        .stat-card .number.blue { color: #0d47a1; }
        .stat-card .label { font-size: 13px; color: #666; }
        .stat-card .sub-label { font-size: 11px; color: #999; margin-top: 2px; }

        .kepsek-table-container {
            overflow-x: auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            padding: 20px;
            border: 1px solid rgba(0,0,0,0.04);
            margin-bottom: 20px;
        }
        .kepsek-table-container table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .kepsek-table-container th,
        .kepsek-table-container td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #f1f5f9;
        }
        .kepsek-table-container th {
            background: #0d47a1;
            color: white;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kepsek-table-container tr:hover { background: #f8fafc; }

        .status-presensi {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 600;
        }
        .status-presensi.hadir { background: #d4edda; color: #155724; }
        .status-presensi.belum { background: #f8d7da; color: #721c24; }
        .status-presensi.masuk { background: #fff3cd; color: #856404; }
        .status-presensi.selesai { background: #d4edda; color: #155724; }
        .status-presensi.izin { background: #fff3cd; color: #856404; }
        .status-presensi.sakit { background: #f8d7da; color: #721c24; }
        .status-presensi.alpa { background: #e2e3e5; color: #383d41; }

        .table-container {
            overflow-x: auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            padding: 20px;
            border: 1px solid rgba(0,0,0,0.04);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        th,
        td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #f1f5f9;
        }
        th {
            background: #f8fafc;
            color: #4a5568;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        tr:hover { background: #f8fafc; }

        .section-title {
            font-size: 1.1em;
            font-weight: 600;
            color: #0f2b5c;
            margin: 24px 0 12px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .section-title .badge-count {
            background: #6f42c1;
            color: white;
            padding: 2px 14px;
            border-radius: 20px;
            font-size: 12px;
        }

        /* ============================================
           CHART CSS
        ============================================ */
        .chart-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .chart-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid rgba(0,0,0,0.04);
        }
        .chart-card .chart-title {
            font-size: 14px;
            font-weight: 600;
            color: #0f2b5c;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .chart-card .chart-title i {
            color: #6f42c1;
        }
        .chart-card .chart-wrapper {
            position: relative;
            height: 220px;
        }
        .chart-card .chart-wrapper canvas {
            width: 100% !important;
            height: 100% !important;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            color: #888;
            font-size: 13px;
            border-top: 1px solid #e5e7eb;
        }

        /* ============================================
           RESPONSIVE
        ============================================ */
        @media (max-width: 768px) {
            .container { padding: 12px; }
            .header { flex-direction: column; text-align: center; gap: 10px; }
            .filter-box .form-group { min-width: 100%; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .chart-grid { grid-template-columns: 1fr; }
            .sekolah-info-card { flex-direction: column; text-align: center; }
            .sekolah-info-card .stats { justify-content: center; }
            .kepsek-table-container { padding: 12px; }
            .kepsek-table-container table { font-size: 12px; }
        }
        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .stat-card { padding: 12px; }
            .stat-card .number { font-size: 1.5em; }
            .kepsek-table-container table { font-size: 11px; }
            .kepsek-table-container th,
            .kepsek-table-container td { padding: 6px 8px; }
            .chart-card .chart-wrapper { height: 180px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- HEADER -->
        <header class="header header-presensi">
            <div>
                <h1><i class="fas fa-clipboard-check"></i> Dashboard Presensi</h1>
                <p>Monitoring kehadiran guru, siswa, dan kepala sekolah</p>
            </div>
            <div>
                <i class="fas fa-user"></i> <?php echo $_SESSION['name']; ?>
            </div>
        </header>

        <?php include_once 'navbar.php'; ?>

        <!-- MAIN -->
        <main>
            <!-- ========================================== -->
            <!-- FILTER -->
            <!-- ========================================== -->
            <div class="filter-box">
                <div class="form-group">
                    <label>📅 Tanggal</label>
                    <input type="date" name="tanggal" value="<?php echo $filter_tanggal; ?>" 
                           onchange="window.location.href='?tanggal='+this.value+'&sekolah=<?php echo $filter_sekolah; ?>&bulan=<?php echo $filter_bulan; ?>&tahun=<?php echo $filter_tahun; ?>'">
                </div>
                <div class="form-group">
                    <label>📆 Bulan</label>
                    <select name="bulan" onchange="window.location.href='?bulan='+this.value+'&tahun=<?php echo $filter_tahun; ?>&tanggal=<?php echo $filter_tanggal; ?>&sekolah=<?php echo $filter_sekolah; ?>'">
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?php echo $m; ?>" <?php echo $filter_bulan == $m ? 'selected' : ''; ?>>
                                <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>📅 Tahun</label>
                    <select name="tahun" onchange="window.location.href='?tahun='+this.value+'&bulan=<?php echo $filter_bulan; ?>&tanggal=<?php echo $filter_tanggal; ?>&sekolah=<?php echo $filter_sekolah; ?>'">
                        <?php for ($y = date('Y') - 2; $y <= date('Y'); $y++): ?>
                            <option value="<?php echo $y; ?>" <?php echo $filter_tahun == $y ? 'selected' : ''; ?>>
                                <?php echo $y; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <?php if ($role == 'admin' || $role == 'dinas' || $role == 'pengawas'): ?>
                <div class="form-group">
                    <label>🏫 Sekolah</label>
                    <select name="sekolah" onchange="window.location.href='?sekolah='+this.value+'&tanggal=<?php echo $filter_tanggal; ?>&bulan=<?php echo $filter_bulan; ?>&tahun=<?php echo $filter_tahun; ?>'">
                        <option value="0">-- Semua Sekolah --</option>
                        <?php foreach ($sekolah_list as $s): ?>
                            <option value="<?php echo $s['id']; ?>" <?php echo $filter_sekolah == $s['id'] ? 'selected' : ''; ?>>
                                <?php echo $s['nama_sekolah']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                <div>
                    <button class="btn-filter" onclick="window.location.href='?tanggal=<?php echo date('Y-m-d'); ?>&sekolah=<?php echo $filter_sekolah; ?>&bulan=<?php echo date('m'); ?>&tahun=<?php echo date('Y'); ?>'">
                        <i class="fas fa-sync"></i> Hari Ini
                    </button>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- INFO SEKOLAH (KHUSUS KEPSEK) -->
            <!-- ========================================== -->
            <?php if ($role == 'kepala_sekolah' && isset($sekolah_data) && $sekolah_data): ?>
                <div class="sekolah-info-card">
                    <div class="info">
                        <h3><i class="fas fa-school"></i> <?php echo $sekolah_data['nama_sekolah']; ?></h3>
                        <p>NPSN: <?php echo $sekolah_data['npsn']; ?> | Kecamatan: <?php echo $sekolah_data['kecamatan']; ?></p>
                    </div>
                    <div class="stats">
                        <div class="stat-item">
                            <div class="num"><?php echo $sekolah_data['total_guru'] ?? 0; ?></div>
                            <div class="label">👨‍🏫 Total Guru</div>
                        </div>
                        <div class="stat-item">
                            <div class="num"><?php echo $sekolah_data['guru_aktif'] ?? 0; ?></div>
                            <div class="label">✅ Guru Aktif</div>
                        </div>
                        <div class="stat-item">
                            <div class="num"><?php echo $stat_guru['hadir'] ?? 0; ?></div>
                            <div class="label">✅ Hadir Hari Ini</div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ========================================== -->
            <!-- STATISTIK -->
            <!-- ========================================== -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="number"><?php echo $stat_siswa['total_siswa'] ?? 0; ?></div>
                    <div class="label">👨‍🎓 Total Siswa</div>
                </div>
                <div class="stat-card">
                    <div class="number green"><?php echo $stat_siswa['hadir'] ?? 0; ?></div>
                    <div class="label">✅ Siswa Hadir</div>
                </div>
                <div class="stat-card">
                    <div class="number"><?php echo $stat_guru['total_guru_presensi'] ?? 0; ?></div>
                    <div class="label">👨‍🏫 Total Guru Presensi</div>
                </div>
                <div class="stat-card">
                    <div class="number green"><?php echo $stat_guru['hadir'] ?? 0; ?></div>
                    <div class="label">✅ Guru Hadir</div>
                </div>
                <div class="stat-card">
                    <div class="number blue"><?php echo $stat_kepsek['total_kepsek_presensi'] ?? 0; ?></div>
                    <div class="label">👔 Kepala Sekolah Presensi</div>
                    <div class="sub-label">✅ <?php echo $stat_kepsek['hadir'] ?? 0; ?> hadir</div>
                </div>
                <div class="stat-card">
                    <div class="number orange"><?php echo $stat_guru['izin'] ?? 0; ?></div>
                    <div class="label">📝 Guru Izin</div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- PRESENSI KEPALA SEKOLAH -->
            <!-- ========================================== -->
            <div class="section-title">
                <span>👔 Presensi Kepala Sekolah <span class="badge-count"><?php echo date('d F Y', strtotime($filter_tanggal)); ?></span></span>
            </div>

            <div class="kepsek-table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kepala Sekolah</th>
                            <th>Sekolah</th>
                            <th>NIP</th>
                            <th>Status</th>
                            <th>Jam Masuk</th>
                            <th>Jam Keluar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($data_kepsek) > 0): ?>
                            <?php $no = 1; foreach ($data_kepsek as $k): 
                                $status_class = '';
                                $status_label = '';
                                
                                if (!$k['presensi_id']) {
                                    $status_class = 'belum';
                                    $status_label = '⏳ Belum';
                                } elseif ($k['jam_masuk'] && !$k['jam_keluar']) {
                                    $status_class = 'masuk';
                                    $status_label = '✅ Masuk';
                                } elseif ($k['jam_masuk'] && $k['jam_keluar']) {
                                    $status_class = 'selesai';
                                    $status_label = '✅ Selesai';
                                } else {
                                    $status_class = 'belum';
                                    $status_label = '⏳ Belum';
                                }
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><strong><?php echo $k['kepsek_name']; ?></strong></td>
                                <td><?php echo $k['nama_sekolah']; ?></td>
                                <td><?php echo $k['kepsek_nip'] ?? '-'; ?></td>
                                <td>
                                    <span class="status-presensi <?php echo $status_class; ?>">
                                        <?php echo $status_label; ?>
                                    </span>
                                </td>
                                <td><?php echo $k['jam_masuk'] ?? '-'; ?></td>
                                <td><?php echo $k['jam_keluar'] ?? '-'; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align:center; color:#888;">Tidak ada data Kepala Sekolah</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- ========================================== -->
            <!-- CHART -->
            <!-- ========================================== -->
            <?php if (count($presensi_harian) > 0 || count($presensi_siswa_harian) > 0): ?>
            <div class="chart-grid">
                <div class="chart-card">
                    <div class="chart-title"><i class="fas fa-chart-bar" style="color:#6f42c1;"></i> Grafik Presensi Guru (Bulan <?php echo date('F', mktime(0, 0, 0, $filter_bulan, 1)); ?>)</div>
                    <div class="chart-wrapper">
                        <canvas id="guruChart"></canvas>
                    </div>
                </div>
                <div class="chart-card">
                    <div class="chart-title"><i class="fas fa-chart-bar" style="color:#2563eb;"></i> Grafik Presensi Siswa (Bulan <?php echo date('F', mktime(0, 0, 0, $filter_bulan, 1)); ?>)</div>
                    <div class="chart-wrapper">
                        <canvas id="siswaChart"></canvas>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- ========================================== -->
            <!-- REKAP PER SEKOLAH -->
            <!-- ========================================== -->
            <div class="section-title">
                <span>🏫 Rekap Kehadiran per Sekolah</span>
                <span style="font-size:12px; color:#6b7280;"><?php echo $nama_hari; ?>, <?php echo date('d F Y', strtotime($filter_tanggal)); ?></span>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Sekolah</th>
                            <th>👨‍🏫 Guru</th>
                            <th>✅ Hadir</th>
                            <th>📝 Izin</th>
                            <th>🤒 Sakit</th>
                            <th>❌ Alpa</th>
                            <th>% Guru</th>
                            <th>👨‍🎓 Siswa</th>
                            <th>✅ Hadir</th>
                            <th>% Siswa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($data_sekolah) > 0): ?>
                            <?php foreach ($data_sekolah as $s): 
                                $persen_guru = ($s['total_guru'] > 0) ? round(($s['guru_hadir'] / $s['total_guru']) * 100, 1) : 0;
                                $persen_siswa = ($s['total_siswa'] > 0) ? round(($s['siswa_hadir'] / $s['total_siswa']) * 100, 1) : 0;
                                $guru_color = $persen_guru >= 80 ? '#28a745' : ($persen_guru >= 50 ? '#d97706' : '#dc3545');
                                $siswa_color = $persen_siswa >= 80 ? '#28a745' : ($persen_siswa >= 50 ? '#d97706' : '#dc3545');
                            ?>
                            <tr>
                                <td><strong><?php echo $s['nama_sekolah']; ?></strong></td>
                                <td><?php echo $s['total_guru']; ?></td>
                                <td style="color:#28a745;"><?php echo $s['guru_hadir']; ?></td>
                                <td style="color:#d97706;"><?php echo $s['guru_izin']; ?></td>
                                <td style="color:#dc3545;"><?php echo $s['guru_sakit']; ?></td>
                                <td style="color:#6b7280;"><?php echo $s['guru_alpa']; ?></td>
                                <td style="color:<?php echo $guru_color; ?>; font-weight:600;">
                                    <?php echo $persen_guru; ?>%
                                </td>
                                <td><?php echo $s['total_siswa']; ?></td>
                                <td style="color:#28a745;"><?php echo $s['siswa_hadir']; ?></td>
                                <td style="color:<?php echo $siswa_color; ?>; font-weight:600;">
                                    <?php echo $persen_siswa; ?>%
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="10" style="text-align:center; color:#888;">Belum ada data presensi</td></tr>
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

    <!-- ========================================== -->
    <!-- CHART.JS SCRIPT -->
    <!-- ========================================== -->
    <script>
        // Data dari PHP
        const presensiHarian = <?php echo json_encode($presensi_harian); ?>;
        const presensiSiswaHarian = <?php echo json_encode($presensi_siswa_harian); ?>;
        
        // ============================================
        // CHART GURU
        // ============================================
        if (presensiHarian.length > 0) {
            const labels = presensiHarian.map(item => {
                const d = new Date(item.tanggal);
                return d.getDate() + '/' + (d.getMonth() + 1);
            });
            
            const ctx1 = document.getElementById('guruChart').getContext('2d');
            new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Hadir',
                            data: presensiHarian.map(item => parseInt(item.hadir) || 0),
                            backgroundColor: 'rgba(40, 167, 69, 0.7)',
                            borderColor: '#28a745',
                            borderWidth: 1
                        },
                        {
                            label: 'Izin',
                            data: presensiHarian.map(item => parseInt(item.izin) || 0),
                            backgroundColor: 'rgba(217, 119, 6, 0.7)',
                            borderColor: '#d97706',
                            borderWidth: 1
                        },
                        {
                            label: 'Sakit',
                            data: presensiHarian.map(item => parseInt(item.sakit) || 0),
                            backgroundColor: 'rgba(220, 53, 69, 0.7)',
                            borderColor: '#dc3545',
                            borderWidth: 1
                        },
                        {
                            label: 'Alpa',
                            data: presensiHarian.map(item => parseInt(item.alpa) || 0),
                            backgroundColor: 'rgba(108, 117, 125, 0.7)',
                            borderColor: '#6c757d',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { font: { size: 10 }, boxWidth: 12 }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { font: { size: 10 } } },
                        x: { ticks: { font: { size: 9 } } }
                    }
                }
            });
        }

        // ============================================
        // CHART SISWA
        // ============================================
        if (presensiSiswaHarian.length > 0) {
            const labels = presensiSiswaHarian.map(item => {
                const d = new Date(item.tanggal);
                return d.getDate() + '/' + (d.getMonth() + 1);
            });
            
            const ctx2 = document.getElementById('siswaChart').getContext('2d');
            new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Hadir',
                            data: presensiSiswaHarian.map(item => parseInt(item.hadir) || 0),
                            backgroundColor: 'rgba(37, 99, 235, 0.7)',
                            borderColor: '#2563eb',
                            borderWidth: 1
                        },
                        {
                            label: 'Izin',
                            data: presensiSiswaHarian.map(item => parseInt(item.izin) || 0),
                            backgroundColor: 'rgba(217, 119, 6, 0.7)',
                            borderColor: '#d97706',
                            borderWidth: 1
                        },
                        {
                            label: 'Sakit',
                            data: presensiSiswaHarian.map(item => parseInt(item.sakit) || 0),
                            backgroundColor: 'rgba(220, 53, 69, 0.7)',
                            borderColor: '#dc3545',
                            borderWidth: 1
                        },
                        {
                            label: 'Alpa',
                            data: presensiSiswaHarian.map(item => parseInt(item.alpa) || 0),
                            backgroundColor: 'rgba(108, 117, 125, 0.7)',
                            borderColor: '#6c757d',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { font: { size: 10 }, boxWidth: 12 }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { font: { size: 10 } } },
                        x: { ticks: { font: { size: 9 } } }
                    }
                }
            });
        }
    </script>
</body>
</html>