<?php
// dinas_monitoring.php - Halaman Monitoring & Evaluasi Dinas
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'dinas') {
    header('Location: login.php');
    exit;
}

include_once 'config/database.php';

// ============================================
// FILTER & PAGINATION
// ============================================

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;

$allowed_limits = [5, 10, 25, 50, 100];
if (!in_array($limit, $allowed_limits)) {
    $limit = 5;
}

$offset = ($page - 1) * $limit;

$where = "1=1";
if (!empty($search)) {
    $where .= " AND s.nama_sekolah LIKE '%$search%'";
}

// ============================================
// AMBIL DATA
// ============================================

$total_sekolah = countData("SELECT s.id FROM sekolah s WHERE $where");
$total_pages = ceil($total_sekolah / $limit);

$data_sekolah = fetchAll("
    SELECT s.*, 
           COUNT(DISTINCT u.id) as total_guru,
           COUNT(p.id) as total_dokumen,
           SUM(CASE WHEN p.status = 'terverifikasi' THEN 1 ELSE 0 END) as terverifikasi,
           SUM(CASE WHEN p.status = 'pending_kepsek' OR p.status = 'pending_pengawas' THEN 1 ELSE 0 END) as pending,
           SUM(CASE WHEN p.status = 'ditolak_kepsek' OR p.status = 'ditolak_pengawas' THEN 1 ELSE 0 END) as ditolak,
           GROUP_CONCAT(DISTINCT CASE WHEN p.id IS NOT NULL THEN u.name END SEPARATOR ', ') as guru_sudah,
           GROUP_CONCAT(DISTINCT CASE WHEN p.id IS NULL THEN u.name END SEPARATOR ', ') as guru_belum
    FROM sekolah s
    LEFT JOIN users u ON u.sekolah_id = s.id AND u.role = 'guru'
    LEFT JOIN perangkat p ON p.id_guru = u.id
    WHERE $where
    GROUP BY s.id
    ORDER BY s.nama_sekolah
    LIMIT $offset, $limit
");

// ============================================
// STATISTIK UTAMA
// ============================================

$total_sekolah_all = countData("SELECT * FROM sekolah");
$total_guru = countData("SELECT * FROM users WHERE role = 'guru'");
$total_dokumen = countData("SELECT * FROM perangkat");
$total_terverifikasi = countData("SELECT * FROM perangkat WHERE status = 'terverifikasi'");
$total_pending = countData("SELECT * FROM perangkat WHERE status = 'pending_kepsek' OR status = 'pending_pengawas'");
$total_ditolak = countData("SELECT * FROM perangkat WHERE status = 'ditolak_kepsek' OR status = 'ditolak_pengawas'");

$data_tahun = fetchAll("
    SELECT tahun_ajaran, 
           COUNT(*) as total,
           SUM(CASE WHEN status = 'terverifikasi' THEN 1 ELSE 0 END) as terverifikasi,
           SUM(CASE WHEN status = 'pending_kepsek' OR status = 'pending_pengawas' THEN 1 ELSE 0 END) as pending
    FROM perangkat
    GROUP BY tahun_ajaran
    ORDER BY tahun_ajaran DESC
");

$data_jenis = fetchAll("
    SELECT jenis, 
           COUNT(*) as total,
           SUM(CASE WHEN status = 'terverifikasi' THEN 1 ELSE 0 END) as terverifikasi
    FROM perangkat
    GROUP BY jenis
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring & Evaluasi - Dinas Pendidikan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f0f4f8;
            color: #1a202c;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* HEADER */
        .header {
            background: linear-gradient(135deg, #0f2b5c 0%, #1a4a8a 40%, #2563eb 100%);
            color: white;
            padding: 28px 35px;
            border-radius: 16px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            box-shadow: 0 8px 32px rgba(37, 99, 235, 0.25);
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        
        .header::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.03);
            border-radius: 50%;
        }
        
        .header-left {
            position: relative;
            z-index: 1;
        }
        
        .header-left h1 {
            font-size: 1.8em;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.5px;
        }
        
        .header-left h1 i {
            margin-right: 12px;
            color: #60a5fa;
        }
        
        .header-left p {
            margin: 4px 0 0 0;
            opacity: 0.85;
            font-size: 14px;
            font-weight: 400;
        }
        
        .header-right {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .header-right .badge {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            padding: 8px 20px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 500;
            border: 1px solid rgba(255,255,255,0.1);
        }
        
        .header-right .badge i {
            margin-right: 8px;
            color: #60a5fa;
        }
        
        /* NAVIGASI */
        .nav {
            background: white;
            padding: 12px 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            display: flex;
            gap: 25px;
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
        
        .nav a:hover {
            background: #eef2f7;
            color: #2563eb;
        }
        
        .nav a.active {
            background: #2563eb;
            color: white;
        }
        
        .nav a i {
            font-size: 16px;
        }
        
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
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }
        
        /* STATS GRID */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 25px;
        }
        
        .stat-card {
            background: white;
            padding: 20px 22px;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0,0,0,0.04);
            position: relative;
            overflow: hidden;
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.10);
        }
        
        .stat-card .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 12px;
        }
        
        .stat-card .stat-icon.blue { background: #dbeafe; color: #2563eb; }
        .stat-card .stat-icon.green { background: #d1fae5; color: #059669; }
        .stat-card .stat-icon.orange { background: #fef3c7; color: #d97706; }
        .stat-card .stat-icon.red { background: #fee2e2; color: #dc2626; }
        .stat-card .stat-icon.purple { background: #ede9fe; color: #7c3aed; }
        .stat-card .stat-icon.teal { background: #ccfbf1; color: #0d9488; }
        
        .stat-card .number {
            font-size: 2em;
            font-weight: 700;
            color: #0f2b5c;
            line-height: 1.2;
            margin-bottom: 2px;
        }
        
        .stat-card .number.green { color: #059669; }
        .stat-card .number.orange { color: #d97706; }
        .stat-card .number.red { color: #dc2626; }
        .stat-card .number.purple { color: #7c3aed; }
        .stat-card .number.teal { color: #0d9488; }
        
        .stat-card .label {
            font-size: 13px;
            color: #6b7280;
            font-weight: 500;
        }
        
        .stat-card .label i {
            margin-right: 4px;
            opacity: 0.6;
        }
        
        /* SECTION TITLE */
        .section-title {
            font-size: 1.15em;
            font-weight: 600;
            color: #0f2b5c;
            margin: 30px 0 16px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flexible-wrap: wrap;
            gap: 10px;
        }
        
        .section-title .badge-count {
            background: #2563eb;
            color: white;
            padding: 2px 14px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .section-title .sub-info {
            font-size: 13px;
            color: #6b7280;
            font-weight: 400;
        }
        
        /* FILTER BOX */
        .filter-box {
            display: flex;
            gap: 16px;
            align-items: center;
            flex-wrap: wrap;
            background: white;
            padding: 16px 20px;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            border: 1px solid rgba(0,0,0,0.04);
        }
        
        .filter-box .search-box {
            flex: 1;
            min-width: 250px;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .filter-box .search-box input {
            flex: 1;
            padding: 10px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f9fafb;
            font-family: 'Inter', sans-serif;
        }
        
        .filter-box .search-box input:focus {
            border-color: #2563eb;
            outline: none;
            background: white;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.08);
        }
        
        .filter-box .search-box input::placeholder {
            color: #9ca3af;
        }
        
        .btn {
            padding: 10px 24px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: #2563eb;
            color: white;
        }
        
        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(37, 99, 235, 0.3);
        }
        
        .btn-secondary {
            background: #e5e7eb;
            color: #4a5568;
        }
        
        .btn-secondary:hover {
            background: #d1d5db;
        }
        
        .btn-success {
            background: #059669;
            color: white;
        }
        
        .btn-success:hover {
            background: #047857;
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(5, 150, 105, 0.3);
        }
        
        .filter-box .info-result {
            font-size: 13px;
            color: #6b7280;
        }
        
        .filter-box .info-result strong {
            color: #0f2b5c;
        }
        
        /* TABLE */
        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            padding: 0;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.04);
        }
        
        .table-wrapper {
            overflow-x: auto;
            padding: 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        
        thead {
            background: #f8fafc;
            border-bottom: 2px solid #e5e7eb;
        }
        
        th {
            padding: 14px 16px;
            text-align: left;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6b7280;
            white-space: nowrap;
        }
        
        td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        
        tbody tr {
            transition: background 0.2s ease;
        }
        
        tbody tr:hover {
            background: #f8fafc;
        }
        
        tbody tr:last-child td {
            border-bottom: none;
        }
        
        .table-school-name {
            font-weight: 600;
            color: #0f2b5c;
            font-size: 14px;
        }
        
        .table-school-name i {
            margin-right: 6px;
            color: #2563eb;
        }
        
        /* GURU LIST */
        .guru-section {
            margin-top: 6px;
        }
        
        .guru-section .label {
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
            margin-right: 6px;
        }
        
        .guru-section .label.sudah {
            color: #059669;
        }
        
        .guru-section .label.belum {
            color: #d97706;
        }
        
        .guru-list {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            margin-top: 2px;
        }
        
        .guru-item {
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .guru-item.sudah {
            background: #d1fae5;
            color: #059669;
        }
        
        .guru-item.sudah:hover {
            background: #a7f3d0;
        }
        
        .guru-item.belum {
            background: #fef3c7;
            color: #d97706;
        }
        
        .guru-item.belum:hover {
            background: #fde68a;
        }
        
        .guru-item i {
            font-size: 9px;
            margin-right: 3px;
        }
        
        .guru-item .count-badge {
            background: rgba(0,0,0,0.08);
            padding: 0 6px;
            border-radius: 10px;
            font-size: 10px;
            margin-left: 2px;
        }
        
        /* STATUS BADGE */
        .status-badge {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-badge.success {
            background: #d1fae5;
            color: #059669;
        }
        
        .status-badge.warning {
            background: #fef3c7;
            color: #d97706;
        }
        
        .status-badge.danger {
            background: #fee2e2;
            color: #dc2626;
        }
        
        .status-badge.neutral {
            background: #f3f4f6;
            color: #6b7280;
        }
        
        .status-badge i {
            margin-right: 4px;
        }
        
        .number-cell {
            font-weight: 600;
            text-align: center;
        }
        
        .number-cell.green { color: #059669; }
        .number-cell.orange { color: #d97706; }
        .number-cell.red { color: #dc2626; }
        .number-cell.blue { color: #2563eb; }
        
        /* PAGINATION */
        .pagination-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            padding: 20px 0 10px 0;
        }
        
        .pagination-left {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            color: #4a5568;
        }
        
        .pagination-left select {
            padding: 8px 14px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            background: white;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
        }
        
        .pagination-left select:focus {
            border-color: #2563eb;
            outline: none;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.08);
        }
        
        .pagination-left .info-text {
            color: #6b7280;
            font-size: 13px;
        }
        
        .pagination-left .info-text strong {
            color: #0f2b5c;
        }
        
        .pagination-right {
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .pagination-right .page-link {
            padding: 8px 14px;
            border-radius: 8px;
            text-decoration: none;
            color: #4a5568;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            border: 1px solid transparent;
            min-width: 36px;
            text-align: center;
        }
        
        .pagination-right .page-link:hover {
            background: #eef2f7;
            border-color: #e5e7eb;
        }
        
        .pagination-right .page-link.active {
            background: #2563eb;
            color: white;
            border-color: #2563eb;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
        }
        
        .pagination-right .page-link.disabled {
            color: #9ca3af;
            cursor: not-allowed;
        }
        
        .pagination-right .page-link.disabled:hover {
            background: none;
            border-color: transparent;
        }
        
        .pagination-right .page-link i {
            font-size: 14px;
        }
        
        .pagination-right .page-link.ellipsis {
            cursor: default;
            color: #9ca3af;
        }
        
        .pagination-right .page-link.ellipsis:hover {
            background: none;
            border-color: transparent;
        }
        
        /* JENIS GRID */
        .jenis-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 12px;
        }
        
        .jenis-item {
            background: white;
            padding: 16px 18px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            border: 1px solid rgba(0,0,0,0.04);
            transition: all 0.3s ease;
        }
        
        .jenis-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        }
        
        .jenis-item .jml {
            font-size: 1.5em;
            font-weight: 700;
            color: #2563eb;
        }
        
        .jenis-item .jml-label {
            font-size: 12px;
            color: #6b7280;
            font-weight: 500;
        }
        
        .jenis-item .jml-verif {
            font-size: 12px;
            color: #059669;
        }
        
        /* INFO NOTE */
        .info-note {
            background: #f8fafc;
            padding: 16px 20px;
            border-radius: 12px;
            border-left: 4px solid #2563eb;
            margin-top: 20px;
            border: 1px solid rgba(0,0,0,0.04);
        }
        
        .info-note p {
            margin: 0;
            font-size: 13px;
            color: #4a5568;
        }
        
        .info-note .legend {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-top: 8px;
        }
        
        .info-note .legend span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
        }
        
        .info-note .legend .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }
        
        .info-note .legend .dot.green { background: #059669; }
        .info-note .legend .dot.orange { background: #d97706; }
        .info-note .legend .dot.red { background: #dc2626; }
        .info-note .legend .dot.sudah { background: #059669; }
        .info-note .legend .dot.belum { background: #d97706; }
        
        /* EMPTY STATE */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #6b7280;
        }
        
        .empty-state i {
            font-size: 48px;
            color: #d1d5db;
            margin-bottom: 16px;
        }
        
        .empty-state h3 {
            font-size: 18px;
            color: #0f2b5c;
            margin-bottom: 8px;
        }
        
        /* FOOTER */
        .footer {
            text-align: center;
            margin-top: 40px;
            padding: 20px;
            color: #6b7280;
            font-size: 13px;
            border-top: 1px solid #e5e7eb;
        }
        
        /* RESPONSIVE */
        @media (max-width: 768px) {
            .container { padding: 12px; }
            .header { padding: 20px; flex-direction: column; text-align: center; gap: 12px; }
            .header-left h1 { font-size: 1.4em; }
            .nav { padding: 10px 16px; justify-content: center; }
            .nav .user-info { margin-left: 0; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
            .stat-card { padding: 16px; }
            .stat-card .number { font-size: 1.6em; }
            .filter-box { flex-direction: column; }
            .filter-box .search-box { min-width: 100%; flex-wrap: wrap; }
            .filter-box .search-box input { min-width: 100%; }
            .pagination-wrapper { flex-direction: column; align-items: stretch; gap: 12px; }
            .pagination-left { justify-content: center; flex-wrap: wrap; }
            .pagination-right { justify-content: center; }
            .pagination-right .page-link { padding: 6px 10px; font-size: 13px; min-width: 32px; }
            table { font-size: 12px; }
            th, td { padding: 10px 12px; }
            .jenis-grid { grid-template-columns: repeat(3, 1fr); }
            .section-title { font-size: 1em; }
        }
        
        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .jenis-grid { grid-template-columns: repeat(2, 1fr); }
            .header-right .badge { font-size: 11px; padding: 4px 12px; }
            .pagination-right .page-link { padding: 4px 8px; font-size: 12px; min-width: 28px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- HEADER -->
        <header class="header">
            <div class="header-left">
                <h1><i class="fas fa-chart-line"></i> Monitoring & Evaluasi</h1>
                <p>Dinas Pendidikan - Dashboard Perangkat Pembelajaran</p>
            </div>
            <div class="header-right">
                <span class="badge"><i class="fas fa-calendar-alt"></i> <?php echo date('d F Y'); ?></span>
                <span class="badge"><i class="fas fa-user-graduate"></i> <?php echo $_SESSION['name']; ?></span>
            </div>
        </header>

         <!-- ===== NAVIGASI PAKAI NAVBAR.PHP ===== -->
        <?php include_once 'navbar.php'; ?>
        <!-- ===================================== -->
        <main>
            <!-- STATISTIK UTAMA -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="fas fa-school"></i></div>
                    <div class="number"><?php echo $total_sekolah_all; ?></div>
                    <div class="label"><i class="fas fa-building"></i> Total Sekolah</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fas fa-chalkboard-teacher"></i></div>
                    <div class="number"><?php echo $total_guru; ?></div>
                    <div class="label"><i class="fas fa-user-tie"></i> Total Guru</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="fas fa-file-alt"></i></div>
                    <div class="number"><?php echo $total_dokumen; ?></div>
                    <div class="label"><i class="fas fa-copy"></i> Total Dokumen</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon teal"><i class="fas fa-check-circle"></i></div>
                    <div class="number green"><?php echo $total_terverifikasi; ?></div>
                    <div class="label"><i class="fas fa-check"></i> Terverifikasi</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
                    <div class="number orange"><?php echo $total_pending; ?></div>
                    <div class="label"><i class="fas fa-hourglass-half"></i> Pending</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon red"><i class="fas fa-times-circle"></i></div>
                    <div class="number red"><?php echo $total_ditolak; ?></div>
                    <div class="label"><i class="fas fa-ban"></i> Ditolak</div>
                </div>
            </div>

            <!-- STATISTIK PER JENIS -->
            <div class="section-title">
                <span><i class="fas fa-chart-pie" style="color:#2563eb; margin-right:8px;"></i> Statistik per Jenis</span>
            </div>
            <div class="jenis-grid">
                <?php foreach ($data_jenis as $j): ?>
                    <div class="jenis-item">
                        <div class="jml"><?php echo $j['total']; ?></div>
                        <div class="jml-label"><?php echo $j['jenis']; ?></div>
                        <div class="jml-verif"><i class="fas fa-check-circle"></i> <?php echo $j['terverifikasi']; ?> terverifikasi</div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- STATISTIK PER TAHUN AJARAN -->
            <div class="section-title">
                <span><i class="fas fa-calendar-alt" style="color:#2563eb; margin-right:8px;"></i> Rekapitulasi per Tahun Ajaran</span>
            </div>
            <div class="table-container">
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Tahun Ajaran</th>
                                <th>Total Dokumen</th>
                                <th><i class="fas fa-check-circle" style="color:#059669;"></i> Terverifikasi</th>
                                <th><i class="fas fa-clock" style="color:#d97706;"></i> Pending</th>
                                <th>Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($data_tahun) > 0): ?>
                                <?php foreach ($data_tahun as $t): ?>
                                <tr>
                                    <td><strong><?php echo $t['tahun_ajaran']; ?></strong></td>
                                    <td class="number-cell blue"><?php echo $t['total']; ?></td>
                                    <td class="number-cell green"><?php echo $t['terverifikasi']; ?></td>
                                    <td class="number-cell orange"><?php echo $t['pending']; ?></td>
                                    <td>
                                        <?php 
                                        $percent = ($t['total'] > 0) ? round(($t['terverifikasi'] / $t['total']) * 100, 1) : 0;
                                        $color = $percent >= 80 ? '#059669' : ($percent >= 50 ? '#d97706' : '#dc2626');
                                        ?>
                                        <span style="font-weight:600; color:<?php echo $color; ?>;">
                                            <?php echo $percent; ?>%
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" style="text-align:center; color:#6b7280; padding:30px;">Belum ada data</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- REKAPITULASI PER SEKOLAH -->
            <div class="section-title">
                <span>
                    <i class="fas fa-school" style="color:#2563eb; margin-right:8px;"></i> Rekapitulasi per Sekolah
                    <span class="badge-count"><?php echo $total_sekolah; ?></span>
                </span>
                <span class="sub-info">
                    <i class="fas fa-eye"></i> Menampilkan <?php echo min($limit, max(0, $total_sekolah)); ?> dari <?php echo $total_sekolah; ?> sekolah
                </span>
            </div>

            <!-- Filter -->
            <div class="filter-box">
                <form method="GET" class="search-box">
                    <input type="text" name="search" placeholder="🔍 Cari nama sekolah..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
                    <?php if (!empty($search)): ?>
                        <a href="dinas_monitoring.php" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset</a>
                    <?php endif; ?>
                </form>
                <?php if (!empty($search)): ?>
                    <div class="info-result">
                        <i class="fas fa-search"></i> Hasil: <strong><?php echo $total_sekolah; ?></strong> sekolah ditemukan
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tabel Sekolah -->
            <div class="table-container">
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th style="width:40px;">#</th>
                                <th style="min-width:220px;">Nama Sekolah &amp; Guru</th>
                                <th>NPSN</th>
                                <th>Kecamatan</th>
                                <th style="text-align:center;">👨‍🏫 Total</th>
                                <th style="text-align:center;">📄 Dokumen</th>
                                <th style="text-align:center;">✅ Terverifikasi</th>
                                <th style="text-align:center;">⏳ Pending</th>
                                <th style="text-align:center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($data_sekolah) > 0): ?>
                                <?php $no = $offset + 1; foreach ($data_sekolah as $s): ?>
                                <tr>
                                    <td style="text-align:center; font-weight:600; color:#6b7280;"><?php echo $no++; ?></td>
                                    <td>
                                        <div class="table-school-name">
                                            <i class="fas fa-school"></i> <?php echo $s['nama_sekolah']; ?>
                                        </div>
                                        
                                        <!-- Guru SUDAH mengerjakan -->
                                        <div class="guru-section">
                                            <span class="label sudah"><i class="fas fa-check-circle"></i> Sudah (<?php 
                                                $sudah_list = !empty($s['guru_sudah']) ? explode(', ', $s['guru_sudah']) : [];
                                                echo count($sudah_list);
                                            ?>)</span>
                                            <div class="guru-list">
                                                <?php if (!empty($s['guru_sudah'])): 
                                                    $guru_list = explode(', ', $s['guru_sudah']);
                                                    $max = 10;
                                                    $count = count($guru_list);
                                                    for ($i = 0; $i < min($max, $count); $i++): 
                                                ?>
                                                    <span class="guru-item sudah"><i class="fas fa-user"></i> <?php echo $guru_list[$i]; ?></span>
                                                <?php endfor; 
                                                    if ($count > $max): ?>
                                                    <span class="guru-item sudah" style="background:#a7f3d0;">+<?php echo $count - $max; ?> lagi</span>
                                                <?php endif; ?>
                                                <?php else: ?>
                                                    <span style="font-size:11px; color:#9ca3af;">Tidak ada guru</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- Guru BELUM mengerjakan -->
                                        <div class="guru-section" style="margin-top:4px;">
                                            <span class="label belum"><i class="fas fa-clock"></i> Belum (<?php 
                                                $belum_list = !empty($s['guru_belum']) ? explode(', ', $s['guru_belum']) : [];
                                                echo count($belum_list);
                                            ?>)</span>
                                            <div class="guru-list">
                                                <?php if (!empty($s['guru_belum'])): 
                                                    $guru_list = explode(', ', $s['guru_belum']);
                                                    $max = 10;
                                                    $count = count($guru_list);
                                                    for ($i = 0; $i < min($max, $count); $i++): 
                                                ?>
                                                    <span class="guru-item belum"><i class="fas fa-user"></i> <?php echo $guru_list[$i]; ?></span>
                                                <?php endfor; 
                                                    if ($count > $max): ?>
                                                    <span class="guru-item belum" style="background:#fde68a;">+<?php echo $count - $max; ?> lagi</span>
                                                <?php endif; ?>
                                                <?php else: ?>
                                                    <span style="font-size:11px; color:#9ca3af;">Semua guru sudah mengerjakan ✅</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span style="font-weight:500; color:#4a5568;"><?php echo $s['npsn']; ?></span></td>
                                    <td><?php echo $s['kecamatan']; ?></td>
                                    <td style="text-align:center; font-weight:600;"><?php echo $s['total_guru'] ?? 0; ?></td>
                                    <td style="text-align:center; font-weight:600; color:#2563eb;"><?php echo $s['total_dokumen'] ?? 0; ?></td>
                                    <td style="text-align:center; font-weight:600; color:#059669;"><?php echo $s['terverifikasi'] ?? 0; ?></td>
                                    <td style="text-align:center; font-weight:600; color:#d97706;"><?php echo $s['pending'] ?? 0; ?></td>
                                    <td style="text-align:center;">
                                        <?php 
                                        $total = $s['total_dokumen'] ?? 0;
                                        $terverifikasi = $s['terverifikasi'] ?? 0;
                                        $percent = ($total > 0) ? round(($terverifikasi / $total) * 100, 1) : 0;
                                        
                                        if ($total == 0) {
                                            echo '<span class="status-badge neutral"><i class="fas fa-minus"></i> -</span>';
                                        } elseif ($percent >= 80) {
                                            echo '<span class="status-badge success"><i class="fas fa-check-circle"></i> '.$percent.'%</span>';
                                        } elseif ($percent >= 50) {
                                            echo '<span class="status-badge warning"><i class="fas fa-exclamation-triangle"></i> '.$percent.'%</span>';
                                        } else {
                                            echo '<span class="status-badge danger"><i class="fas fa-times-circle"></i> '.$percent.'%</span>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9">
                                        <div class="empty-state">
                                            <i class="fas fa-search"></i>
                                            <h3>Tidak Ada Data</h3>
                                            <?php if (!empty($search)): ?>
                                                <p>Tidak ditemukan sekolah dengan nama "<strong><?php echo htmlspecialchars($search); ?></strong>"</p>
                                            <?php else: ?>
                                                <p>Belum ada data sekolah yang tersedia</p>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- PAGINATION -->
            <?php if ($total_pages > 0): ?>
                <div class="pagination-wrapper">
                    <div class="pagination-left">
                        <label for="limit-select">Tampilkan:</label>
                        <select id="limit-select" onchange="changeLimit(this.value)">
                            <option value="5" <?php echo $limit == 5 ? 'selected' : ''; ?>>5</option>
                            <option value="10" <?php echo $limit == 10 ? 'selected' : ''; ?>>10</option>
                            <option value="25" <?php echo $limit == 25 ? 'selected' : ''; ?>>25</option>
                            <option value="50" <?php echo $limit == 50 ? 'selected' : ''; ?>>50</option>
                            <option value="100" <?php echo $limit == 100 ? 'selected' : ''; ?>>100</option>
                        </select>
                        <span class="info-text">
                            Menampilkan <strong><?php echo $offset + 1; ?></strong> - 
                            <strong><?php echo min($offset + $limit, $total_sekolah); ?></strong> 
                            dari <strong><?php echo $total_sekolah; ?></strong> data
                        </span>
                    </div>
                    
                    <div class="pagination-right">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>&limit=<?php echo $limit; ?>" class="page-link">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php else: ?>
                            <span class="page-link disabled"><i class="fas fa-chevron-left"></i></span>
                        <?php endif; ?>
                        
                        <?php
                        $start_page = max(1, $page - 2);
                        $end_page = min($total_pages, $page + 2);
                        
                        if ($start_page > 1) {
                            echo '<a href="?page=1&search=' . urlencode($search) . '&limit=' . $limit . '" class="page-link">1</a>';
                            if ($start_page > 2) {
                                echo '<span class="page-link ellipsis">…</span>';
                            }
                        }
                        
                        for ($i = $start_page; $i <= $end_page; $i++): 
                        ?>
                            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&limit=<?php echo $limit; ?>" class="page-link <?php echo ($i == $page) ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php
                        if ($end_page < $total_pages) {
                            if ($end_page < $total_pages - 1) {
                                echo '<span class="page-link ellipsis">…</span>';
                            }
                            echo '<a href="?page=' . $total_pages . '&search=' . urlencode($search) . '&limit=' . $limit . '" class="page-link">' . $total_pages . '</a>';
                        }
                        ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>&limit=<?php echo $limit; ?>" class="page-link">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php else: ?>
                            <span class="page-link disabled"><i class="fas fa-chevron-right"></i></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Informasi -->
            <div class="info-note">
                <p><i class="fas fa-info-circle" style="color:#2563eb;"></i> <strong>Keterangan:</strong></p>
                <div class="legend">
                    <span><span class="dot green"></span> Baik (≥80%)</span>
                    <span><span class="dot orange"></span> Sedang (50-79%)</span>
                    <span><span class="dot red"></span> Kurang (&lt;50%)</span>
                    <span><span class="dot sudah"></span> Guru sudah mengerjakan perangkat</span>
                    <span><span class="dot belum"></span> Guru belum mengerjakan perangkat</span>
                </div>
                <p style="margin-top:8px; font-size:12px; color:#6b7280;">
                    <i class="fas fa-table"></i> Menampilkan <?php echo $limit; ?> sekolah per halaman | Total <?php echo $total_sekolah_all; ?> sekolah
                </p>
            </div>

            <!-- Tombol Export -->
            <div style="margin-top:20px; text-align:right;">
                <a href="export_laporan.php" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Export Laporan (Excel)
                </a>
            </div>
        </main>

        <!-- FOOTER -->
        <footer class="footer">
            <p>&copy; <?php echo date('Y'); ?> Pusat Perangkat Pembelajaran - Dinas Pendidikan</p>
        </footer>
    </div>

    <script>
        function changeLimit(value) {
            var currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('limit', value);
            currentUrl.searchParams.set('page', 1);
            window.location.href = currentUrl.toString();
        }
    </script>
</body>
</html>