<?php
// pengawas_approve.php - Dashboard Pengawas Sekolah
session_start();

// Cek login dan role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pengawas') {
    header('Location: login.php');
    exit;
}

include_once 'config/database.php';

$id_pengawas = $_SESSION['user_id'];

// Ambil data pengawas
$pengawas_data = getUserSekolah($id_pengawas);
$nama_pengawas = $pengawas_data['name'] ?? $_SESSION['name'];

// ============================================
// AMBIL SEKOLAH BINAAN DARI TABEL RELASI
// ============================================
$sekolah_binaan = fetchAll("
    SELECT s.*, 
           COUNT(DISTINCT u.id) as total_guru,
           COUNT(p.id) as total_dokumen,
           SUM(CASE WHEN p.status = 'terverifikasi' THEN 1 ELSE 0 END) as dokumen_terverifikasi,
           SUM(CASE WHEN p.status = 'pending_kepsek' OR p.status = 'pending_pengawas' THEN 1 ELSE 0 END) as dokumen_pending,
           SUM(CASE WHEN p.status = 'ditolak_kepsek' OR p.status = 'ditolak_pengawas' THEN 1 ELSE 0 END) as dokumen_ditolak,
           GROUP_CONCAT(DISTINCT u.name SEPARATOR ', ') as daftar_guru,
           GROUP_CONCAT(DISTINCT CASE WHEN p.id IS NOT NULL THEN u.name END SEPARATOR ', ') as guru_sudah,
           GROUP_CONCAT(DISTINCT CASE WHEN p.id IS NULL THEN u.name END SEPARATOR ', ') as guru_belum
    FROM sekolah s
    JOIN pengawas_sekolah ps ON s.id = ps.sekolah_id
    LEFT JOIN users u ON u.sekolah_id = s.id AND u.role = 'guru'
    LEFT JOIN perangkat p ON p.id_guru = u.id
    WHERE ps.pengawas_id = $id_pengawas
    GROUP BY s.id
    ORDER BY s.nama_sekolah
");

// Ambil ID sekolah binaan
$sekolah_ids = [];
foreach ($sekolah_binaan as $s) {
    $sekolah_ids[] = $s['id'];
}

if (empty($sekolah_ids)) {
    $sekolah_filter = "AND 1=0";
    $total_sekolah = 0;
    $ids_str = '0'; // Set default untuk mencegah error
} else {
    $ids_str = implode(',', $sekolah_ids);
    $sekolah_filter = "AND u.sekolah_id IN ($ids_str)";
    $total_sekolah = count($sekolah_binaan);
}

// ============================================
// AMBIL DATA KEHADIRAN KEPALA SEKOLAH
// ============================================
$tanggal = date('Y-m-d');

// Ambil data kehadiran Kepala Sekolah di sekolah binaan
if (!empty($sekolah_ids)) {
    $ids_str_kepsek = implode(',', $sekolah_ids);
    $kehadiran_kepsek = fetchAll("
        SELECT 
            u.id as kepsek_id,
            u.name as kepsek_name,
            u.nip as kepsek_nip,
            s.nama_sekolah,
            s.id as sekolah_id,
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
        LEFT JOIN presensi_guru pg ON pg.id_guru = u.id AND pg.tanggal = '$tanggal'
        WHERE u.role = 'kepala_sekolah' 
          AND u.sekolah_id IN ($ids_str_kepsek)
          AND u.is_active = 1
        ORDER BY s.nama_sekolah, u.name
    ");
} else {
    $kehadiran_kepsek = [];
}

// Statistik kehadiran Kepala Sekolah
$total_kepsek = count($kehadiran_kepsek);
$kepsek_hadir = 0;
$kepsek_belum = 0;
foreach ($kehadiran_kepsek as $k) {
    if ($k['presensi_id'] && $k['jam_masuk']) {
        $kepsek_hadir++;
    } else {
        $kepsek_belum++;
    }
}

// ============================================
// AMBIL DATA PERANGKAT
// ============================================
$pending_pengawas = fetchAll("
    SELECT p.*, u.name as guru_name, u.sekolah as guru_sekolah,
           mp.nama_mapel, k.nama_kelas, k.jenjang,
           s.nama_sekolah as sekolah_nama
    FROM perangkat p
    JOIN users u ON p.id_guru = u.id
    JOIN mata_pelajaran mp ON p.id_mapel = mp.id
    JOIN kelas k ON p.id_kelas = k.id
    LEFT JOIN sekolah s ON u.sekolah_id = s.id
    WHERE p.status = 'pending_pengawas' $sekolah_filter
    ORDER BY p.created_at ASC
");

$terverifikasi_list = fetchAll("
    SELECT p.*, u.name as guru_name, u.sekolah as guru_sekolah,
           mp.nama_mapel, k.nama_kelas, k.jenjang,
           s.nama_sekolah as sekolah_nama
    FROM perangkat p
    JOIN users u ON p.id_guru = u.id
    JOIN mata_pelajaran mp ON p.id_mapel = mp.id
    JOIN kelas k ON p.id_kelas = k.id
    LEFT JOIN sekolah s ON u.sekolah_id = s.id
    WHERE p.status = 'terverifikasi' $sekolah_filter
    ORDER BY p.created_at DESC
    LIMIT 20
");

// ============================================
// PROSES APPROVE/REJECT
// ============================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $id_perangkat = (int)$_POST['id_perangkat'];
    $action = $_POST['action'];
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan'] ?? '');
    
    if ($action == 'approve') {
        $ttd_path = "public/uploads/ttd/ttd_pengawas_" . $id_perangkat . "_" . date('Ymd_His') . ".png";
        $sql = "UPDATE perangkat SET 
                    status = 'terverifikasi',
                    ttd_pengawas = '$ttd_path',
                    ttd_pengawas_date = NOW(),
                    catatan_pengawas = '$catatan'
                WHERE id = $id_perangkat";
        
        if (query($sql)) {
            $sql_riwayat = "INSERT INTO riwayat_status (id_perangkat, id_user, status_awal, status_akhir, catatan) 
                            VALUES ($id_perangkat, $id_pengawas, 'pending_pengawas', 'terverifikasi', 'Pengawas menyetujui dan menandatangani')";
            query($sql_riwayat);
            $success = "✅ Dokumen berhasil diverifikasi dan ditandatangani!";
        } else {
            $error = "❌ Gagal memverifikasi dokumen!";
        }
    } elseif ($action == 'reject') {
        $sql = "UPDATE perangkat SET 
                    status = 'ditolak_pengawas',
                    catatan_pengawas = '$catatan'
                WHERE id = $id_perangkat";
        
        if (query($sql)) {
            $sql_riwayat = "INSERT INTO riwayat_status (id_perangkat, id_user, status_awal, status_akhir, catatan) 
                            VALUES ($id_perangkat, $id_pengawas, 'pending_pengawas', 'ditolak_pengawas', 'Ditolak oleh Pengawas: $catatan')";
            query($sql_riwayat);
            $success = "❌ Dokumen ditolak!";
        } else {
            $error = "❌ Gagal menolak dokumen!";
        }
    }
}

// Statistik
$total_pending = count($pending_pengawas);
$total_dokumen = 0;
$total_terverifikasi = 0;
foreach ($sekolah_binaan as $s) {
    $total_dokumen += $s['total_dokumen'] ?? 0;
    $total_terverifikasi += $s['dokumen_terverifikasi'] ?? 0;
}

// ============================================
// STATISTIK WILAYAH BINAAN
// ============================================
$total_guru_binaan = 0;
$total_guru_sudah = 0;
$total_guru_belum = 0;
foreach ($sekolah_binaan as $s) {
    $total_guru_binaan += $s['total_guru'] ?? 0;
    $guru_sudah_list = !empty($s['guru_sudah']) ? explode(', ', $s['guru_sudah']) : [];
    $guru_belum_list = !empty($s['guru_belum']) ? explode(', ', $s['guru_belum']) : [];
    $total_guru_sudah += count($guru_sudah_list);
    $total_guru_belum += count($guru_belum_list);
}

$persen_guru_sudah = ($total_guru_binaan > 0) ? round(($total_guru_sudah / $total_guru_binaan) * 100, 1) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengawas - Pusat Perangkat Pembelajaran</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="/website_perangkat/public/css/style.css">
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, #1a237e 0%, #0d47a1 100%);
            color: white;
            padding: 20px 25px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        .dashboard-header h2 { margin: 0; font-size: 1.5em; }
        .dashboard-header .detail {
            opacity: 0.9;
            font-size: 14px;
            margin-top: 5px;
        }
        .dashboard-header .badge-sk {
            display: inline-block;
            background: rgba(255,215,0,0.2);
            color: #ffd700;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            border: 1px solid rgba(255,215,0,0.2);
            margin-top: 8px;
        }
        .kepsek-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }
        .kepsek-card {
            background: white;
            border-radius: 10px;
            padding: 14px 18px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            border: 1px solid rgba(0,0,0,0.04);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            transition: all 0.3s ease;
        }
        .kepsek-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        }
        .kepsek-card .info { display: flex; flex-direction: column; }
        .kepsek-card .info .name {
            font-weight: 600;
            color: #0f2b5c;
            font-size: 14px;
        }
        .kepsek-card .info .sekolah { font-size: 12px; color: #6b7280; }
        .kepsek-card .info .nip { font-size: 11px; color: #9ca3af; }
        .kepsek-card .status-presensi {
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .kepsek-card .status-presensi.hadir { background: #d4edda; color: #155724; }
        .kepsek-card .status-presensi.belum { background: #f8d7da; color: #721c24; }
        .kepsek-card .status-presensi.masuk { background: #fff3cd; color: #856404; }
        .kepsek-card .status-presensi.selesai { background: #d4edda; color: #155724; }
        .kepsek-card .time { font-size: 12px; color: #6b7280; }
        .sekolah-binaan-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }
        .sekolah-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            padding: 18px 20px;
            border-left: 4px solid #0d47a1;
            transition: all 0.3s ease;
        }
        .sekolah-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(0,0,0,0.10);
        }
        .sekolah-card .sekolah-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 8px;
        }
        .sekolah-card .sekolah-header .name {
            font-weight: 700;
            color: #0f2b5c;
            font-size: 15px;
        }
        .sekolah-card .sekolah-header .name i { color: #0d47a1; margin-right: 6px; }
        .sekolah-card .sekolah-header .npsn {
            font-size: 12px;
            color: #6b7280;
            background: #f3f4f6;
            padding: 2px 12px;
            border-radius: 12px;
        }
        .sekolah-card .guru-section { margin-top: 6px; }
        .sekolah-card .guru-section .label {
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
            margin-right: 6px;
        }
        .sekolah-card .guru-section .label.sudah { color: #059669; }
        .sekolah-card .guru-section .label.belum { color: #d97706; }
        .sekolah-card .guru-list {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            margin-top: 2px;
        }
        .sekolah-card .guru-item {
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
            transition: all 0.2s;
        }
        .sekolah-card .guru-item.sudah {
            background: #d1fae5;
            color: #059669;
        }
        .sekolah-card .guru-item.sudah:hover { background: #a7f3d0; }
        .sekolah-card .guru-item.belum {
            background: #fef3c7;
            color: #d97706;
        }
        .sekolah-card .guru-item.belum:hover { background: #fde68a; }
        .sekolah-card .guru-item i { font-size: 9px; margin-right: 3px; }
        .sekolah-card .sekolah-stats {
            display: flex;
            gap: 16px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #f1f5f9;
            font-size: 12px;
        }
        .sekolah-card .sekolah-stats .stat { text-align: center; flex: 1; }
        .sekolah-card .sekolah-stats .stat .num {
            font-weight: 700;
            font-size: 16px;
            color: #0d47a1;
        }
        .sekolah-card .sekolah-stats .stat .num.green { color: #28a745; }
        .sekolah-card .sekolah-stats .stat .num.orange { color: #ffc107; }
        .sekolah-card .sekolah-stats .stat .label-stat {
            font-size: 10px;
            color: #6b7280;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #888;
        }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info-box {
            background: #e8f0fe;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #0d47a1;
        }
        .info-box h4 { color: #0d47a1; margin: 0 0 8px 0; }
        .info-box p { margin: 4px 0; font-size: 13px; color: #555; }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
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
            font-size: 2.2em;
            font-weight: 700;
            color: #0d47a1;
        }
        .stat-card .number.green { color: #28a745; }
        .stat-card .number.orange { color: #ffc107; }
        .stat-card .number.red { color: #dc3545; }
        .stat-card .number.purple { color: #6f42c1; }
        .stat-card .label { font-size: 13px; color: #666; }
        .stat-card .sub-label { font-size: 11px; color: #999; margin-top: 2px; }
        
        .chart-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }
        .chart-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
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
        .chart-card .chart-title i { color: #0d47a1; }
        .chart-card .chart-wrapper { position: relative; height: 200px; }
        .chart-card .chart-wrapper canvas { width: 100% !important; height: 100% !important; }
        .chart-card .chart-legend {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            margin-top: 10px;
            font-size: 12px;
        }
        .chart-card .chart-legend span {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .chart-card .chart-legend .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }
        .chart-card .chart-legend .dot.green { background: #28a745; }
        .chart-card .chart-legend .dot.orange { background: #ffc107; }
        .chart-card .chart-legend .dot.red { background: #dc3545; }
        .chart-card .chart-legend .dot.blue { background: #0d47a1; }
        .chart-card .chart-legend .dot.purple { background: #6f42c1; }
        
        .section-title {
            font-size: 1.2em;
            margin: 30px 0 15px 0;
            color: #333;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .section-title .badge-count {
            background: #0d47a1;
            color: white;
            padding: 2px 14px;
            border-radius: 20px;
            font-size: 12px;
        }
        
        .table-container {
            overflow-x: auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            padding: 20px;
            margin-top: 15px;
            border: 1px solid rgba(0,0,0,0.04);
        }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #f1f5f9; }
        th { background: #0d47a1; color: white; font-weight: 600; }
        tr:hover { background: #f8fafc; }
        
        .status-badge {
            padding: 3px 12px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: bold;
            display: inline-block;
        }
        .status-pending { background: #ffc107; color: #000; }
        .status-terverifikasi { background: #28a745; color: #fff; }
        .status-ditolak { background: #dc3545; color: #fff; }
        .status-draft { background: #6c757d; color: #fff; }
        
        .btn-approve {
            background: #28a745;
            color: white;
            padding: 5px 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
        }
        .btn-approve:hover { background: #218838; }
        .btn-reject {
            background: #dc3545;
            color: white;
            padding: 5px 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
        }
        .btn-reject:hover { background: #c82333; }
        .btn-download {
            background: #0d47a1;
            color: white;
            padding: 4px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 11px;
            display: inline-block;
        }
        .btn-download:hover { background: #0a3a7a; }
        
        .reject-form {
            display: inline-flex;
            gap: 5px;
            align-items: center;
            flex-wrap: wrap;
        }
        .reject-form input[type="text"] {
            padding: 4px 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 11px;
            width: 100px;
        }
        
        .footer { text-align: center; margin-top: 30px; padding: 20px; color: #888; font-size: 13px; border-top: 1px solid #e5e7eb; }
        
        @media (max-width: 768px) {
            .container { padding: 12px; }
            .chart-grid { grid-template-columns: 1fr; }
            .sekolah-binaan-grid { grid-template-columns: 1fr; }
            .kepsek-grid { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .reject-form input[type="text"] { width: 80px; }
        }
        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .stat-card .number { font-size: 1.6em; }
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header header-pengawas">
            <h1><i class="fas fa-user-shield"></i> Dashboard Pengawas</h1>
            <p>Supervisi dan Verifikasi Perangkat Pembelajaran</p>
        </header>

        <?php include_once 'navbar.php'; ?>

        <main>
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Header Pengawas -->
            <div class="dashboard-header">
                <h2>🔍 <?php echo $nama_pengawas; ?></h2>
                <div class="detail">
                    <span>📋 <strong>Jenjang:</strong> PAUD & DIKDAS</span>
                    <span>📅 <strong>SK:</strong> PK.420.VI.03/1708/VII/2026</span>
                    <span>🏫 <strong>Sekolah Binaan:</strong> <?php echo $total_sekolah; ?> sekolah</span>
                </div>
                <div class="badge-sk">
                    📋 SK Penugasan Pengawas
                </div>
            </div>

            <!-- Info Tugas -->
            <div class="info-box">
                <h4>📌 Tugas Pengawas</h4>
                <p>1. Melakukan supervisi dan verifikasi dokumen perangkat pembelajaran</p>
                <p>2. Memastikan dokumen sesuai dengan standar kurikulum yang berlaku</p>
                <p>3. Memantau kehadiran Kepala Sekolah di sekolah binaan</p>
                <p>4. Memberikan tanda tangan "Mengetahui" pada dokumen yang telah diverifikasi</p>
            </div>

            <!-- ========================================== -->
            <!-- STATISTIK -->
            <!-- ========================================== -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="number"><?php echo $total_sekolah; ?></div>
                    <div class="label">🏫 Sekolah Binaan</div>
                </div>
                <div class="stat-card">
                    <div class="number"><?php echo $total_guru_binaan; ?></div>
                    <div class="label">👨‍🏫 Total Guru</div>
                    <div class="sub-label">✅ <?php echo $total_guru_sudah; ?> sudah · ⏳ <?php echo $total_guru_belum; ?> belum</div>
                </div>
                <div class="stat-card">
                    <div class="number"><?php echo $total_dokumen; ?></div>
                    <div class="label">📄 Total Dokumen</div>
                </div>
                <div class="stat-card">
                    <div class="number green"><?php echo $total_terverifikasi; ?></div>
                    <div class="label">✅ Terverifikasi</div>
                </div>
                <div class="stat-card">
                    <div class="number orange"><?php echo $total_pending; ?></div>
                    <div class="label">⏳ Pending</div>
                </div>
                <div class="stat-card">
                    <div class="number purple"><?php echo $total_kepsek; ?></div>
                    <div class="label">👔 Kepala Sekolah</div>
                    <div class="sub-label">✅ <?php echo $kepsek_hadir; ?> hadir · ⏳ <?php echo $kepsek_belum; ?> belum</div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- KEHADIRAN KEPALA SEKOLAH -->
            <!-- ========================================== -->
            <div class="section-title">
                <span>👔 Kehadiran Kepala Sekolah <span class="badge-count"><?php echo date('d F Y'); ?></span></span>
            </div>

            <?php if (count($kehadiran_kepsek) > 0): ?>
                <div class="kepsek-grid">
                    <?php foreach ($kehadiran_kepsek as $k): 
                        $status_class = '';
                        $status_label = '';
                        $time_display = '';
                        
                        if (!$k['presensi_id']) {
                            $status_class = 'belum';
                            $status_label = '⏳ Belum Presensi';
                        } elseif ($k['jam_masuk'] && !$k['jam_keluar']) {
                            $status_class = 'masuk';
                            $status_label = '✅ Masuk';
                            $time_display = '🕐 ' . $k['jam_masuk'];
                        } elseif ($k['jam_masuk'] && $k['jam_keluar']) {
                            $status_class = 'selesai';
                            $status_label = '✅ Selesai';
                            $time_display = '🕐 ' . $k['jam_masuk'] . ' - ' . $k['jam_keluar'];
                        } else {
                            $status_class = 'belum';
                            $status_label = '⏳ Belum Presensi';
                        }
                    ?>
                        <div class="kepsek-card">
                            <div class="info">
                                <span class="name"><?php echo $k['kepsek_name']; ?></span>
                                <span class="sekolah"><i class="fas fa-school"></i> <?php echo $k['nama_sekolah']; ?></span>
                                <?php if (!empty($k['kepsek_nip'])): ?>
                                    <span class="nip">NIP: <?php echo $k['kepsek_nip']; ?></span>
                                <?php endif; ?>
                            </div>
                            <div style="text-align:right;">
                                <span class="status-presensi <?php echo $status_class; ?>">
                                    <?php echo $status_label; ?>
                                </span>
                                <?php if ($time_display): ?>
                                    <div class="time"><?php echo $time_display; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    📭 Tidak ada data Kepala Sekolah di sekolah binaan.
                </div>
            <?php endif; ?>

            <!-- ========================================== -->
            <!-- CHART SECTION -->
            <!-- ========================================== -->
            <div class="chart-grid">
                <div class="chart-card">
                    <div class="chart-title"><i class="fas fa-chart-pie"></i> Status Dokumen Wilayah Binaan</div>
                    <div class="chart-wrapper">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="chart-legend">
                        <span><span class="dot green"></span> Terverifikasi</span>
                        <span><span class="dot orange"></span> Pending</span>
                        <span><span class="dot red"></span> Ditolak</span>
                    </div>
                </div>
                
                <div class="chart-card">
                    <div class="chart-title"><i class="fas fa-chart-doughnut"></i> Guru Sudah vs Belum Mengerjakan</div>
                    <div class="chart-wrapper">
                        <canvas id="guruChart"></canvas>
                    </div>
                    <div class="chart-legend">
                        <span><span class="dot green"></span> Sudah (<?php echo $persen_guru_sudah; ?>%)</span>
                        <span><span class="dot orange"></span> Belum (<?php echo 100 - $persen_guru_sudah; ?>%)</span>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- DAFTAR SEKOLAH BINAAN -->
            <!-- ========================================== -->
            <?php if (!empty($sekolah_binaan)): ?>
                <div class="section-title">
                    <span>🏫 Sekolah Binaan <span class="badge-count"><?php echo count($sekolah_binaan); ?></span></span>
                </div>
                
                <div class="sekolah-binaan-grid">
                    <?php foreach ($sekolah_binaan as $s): ?>
                        <div class="sekolah-card">
                            <div class="sekolah-header">
                                <span class="name"><i class="fas fa-school"></i> <?php echo $s['nama_sekolah']; ?></span>
                                <span class="npsn">NPSN: <?php echo $s['npsn']; ?></span>
                            </div>
                            
                            <div class="guru-section">
                                <span class="label sudah"><i class="fas fa-check-circle"></i> Sudah (<?php 
                                    $sudah_list = !empty($s['guru_sudah']) ? explode(', ', $s['guru_sudah']) : [];
                                    echo count($sudah_list);
                                ?>)</span>
                                <div class="guru-list">
                                    <?php if (!empty($s['guru_sudah'])): 
                                        $guru_list = explode(', ', $s['guru_sudah']);
                                        $max = 8;
                                        $count = count($guru_list);
                                        for ($i = 0; $i < min($max, $count); $i++): 
                                    ?>
                                        <span class="guru-item sudah"><i class="fas fa-user-check"></i> <?php echo $guru_list[$i]; ?></span>
                                    <?php endfor; 
                                        if ($count > $max): ?>
                                        <span class="guru-item sudah" style="background:#a7f3d0;">+<?php echo $count - $max; ?> lagi</span>
                                    <?php endif; ?>
                                    <?php else: ?>
                                        <span style="font-size:11px; color:#9ca3af;">Tidak ada guru</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="guru-section" style="margin-top:4px;">
                                <span class="label belum"><i class="fas fa-clock"></i> Belum (<?php 
                                    $belum_list = !empty($s['guru_belum']) ? explode(', ', $s['guru_belum']) : [];
                                    echo count($belum_list);
                                ?>)</span>
                                <div class="guru-list">
                                    <?php if (!empty($s['guru_belum'])): 
                                        $guru_list = explode(', ', $s['guru_belum']);
                                        $max = 8;
                                        $count = count($guru_list);
                                        for ($i = 0; $i < min($max, $count); $i++): 
                                    ?>
                                        <span class="guru-item belum"><i class="fas fa-user-clock"></i> <?php echo $guru_list[$i]; ?></span>
                                    <?php endfor; 
                                        if ($count > $max): ?>
                                        <span class="guru-item belum" style="background:#fde68a;">+<?php echo $count - $max; ?> lagi</span>
                                    <?php endif; ?>
                                    <?php else: ?>
                                        <span style="font-size:11px; color:#28a745;">Semua guru sudah mengerjakan ✅</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="sekolah-stats">
                                <div class="stat">
                                    <div class="num green"><?php echo $s['dokumen_terverifikasi'] ?? 0; ?></div>
                                    <div class="label-stat">✅ Terverifikasi</div>
                                </div>
                                <div class="stat">
                                    <div class="num orange"><?php echo ($s['total_dokumen'] ?? 0) - ($s['dokumen_terverifikasi'] ?? 0); ?></div>
                                    <div class="label-stat">⏳ Pending</div>
                                </div>
                                <div class="stat">
                                    <div class="num"><?php echo $s['total_guru'] ?? 0; ?></div>
                                    <div class="label-stat">👨‍🏫 Total Guru</div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-error">
                    ⚠️ Anda belum memiliki sekolah binaan. Silahkan hubungi admin untuk menetapkan sekolah binaan.
                </div>
            <?php endif; ?>

            <!-- ========================================== -->
            <!-- DOKUMEN MENUNGGU SUPERVISI -->
            <!-- ========================================== -->
            <div class="section-title">
                <span>📄 Dokumen Menunggu Supervisi <span class="badge-count"><?php echo $total_pending; ?></span></span>
            </div>

            <?php if (count($pending_pengawas) > 0): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul</th>
                                <th>Guru</th>
                                <th>Sekolah</th>
                                <th>Mapel</th>
                                <th>Kelas</th>
                                <th>File</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($pending_pengawas as $p): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><strong><?php echo $p['judul']; ?></strong></td>
                                <td><?php echo $p['guru_name']; ?></td>
                                <td><?php echo $p['sekolah_nama'] ?? $p['guru_sekolah']; ?></td>
                                <td><?php echo $p['nama_mapel']; ?></td>
                                <td>Kelas <?php echo $p['nama_kelas']; ?> (<?php echo $p['jenjang']; ?>)</td>
                                <td>
                                    <?php if (file_exists($p['file_path'])): ?>
                                        <a href="<?php echo $p['file_path']; ?>" target="_blank" class="btn-download">👁️ Lihat</a>
                                    <?php else: ?>
                                        <span style="color:#dc3545;">File tidak ada</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="status-badge status-pending">⏳ Pending</span>
                                </td>
                                <td>
                                    <form action="" method="POST" style="display:inline;">
                                        <input type="hidden" name="id_perangkat" value="<?php echo $p['id']; ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="btn-approve" onclick="return confirm('Setujui dan tanda tangani dokumen ini?')">
                                            ✅ TTD
                                        </button>
                                    </form>
                                    <form action="" method="POST" class="reject-form" style="display:inline-flex; margin-top:5px;">
                                        <input type="hidden" name="id_perangkat" value="<?php echo $p['id']; ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <input type="text" name="catatan" placeholder="Alasan ditolak..." style="padding:3px 8px; font-size:11px; width:100px; border:1px solid #ddd; border-radius:4px;">
                                        <button type="submit" class="btn-reject" onclick="return confirm('Tolak dokumen ini?')">❌ Tolak</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-success">
                    ✅ Tidak ada dokumen yang menunggu supervisi.
                </div>
            <?php endif; ?>

            <!-- ========================================== -->
            <!-- DOKUMEN TERVERIFIKASI -->
            <!-- ========================================== -->
            <div class="section-title">
                <span>✅ Dokumen Terverifikasi (Terbaru)</span>
            </div>

            <?php if (count($terverifikasi_list) > 0): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul</th>
                                <th>Guru</th>
                                <th>Sekolah</th>
                                <th>Mapel</th>
                                <th>Kelas</th>
                                <th>File</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($terverifikasi_list as $p): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><strong><?php echo $p['judul']; ?></strong></td>
                                <td><?php echo $p['guru_name']; ?></td>
                                <td><?php echo $p['sekolah_nama'] ?? $p['guru_sekolah']; ?></td>
                                <td><?php echo $p['nama_mapel']; ?></td>
                                <td>Kelas <?php echo $p['nama_kelas']; ?> (<?php echo $p['jenjang']; ?>)</td>
                                <td>
                                    <?php if (file_exists($p['file_path'])): ?>
                                        <a href="<?php echo $p['file_path']; ?>" target="_blank" class="btn-download">👁️ Lihat</a>
                                    <?php else: ?>
                                        <span style="color:#dc3545;">File tidak ada</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="status-badge status-terverifikasi">✅ Terverifikasi</span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    📭 Belum ada dokumen yang terverifikasi.
                </div>
            <?php endif; ?>

        </main>

        <footer class="footer">
            <p>&copy; <?php echo date('Y'); ?> Pusat Perangkat Pembelajaran - Pengawas PAUD & DIKDAS</p>
        </footer>
    </div>

    <!-- ========================================== -->
    <!-- SCRIPT CHART.JS -->
    <!-- ========================================== -->
    <script>
        // Data dari PHP
        const totalTer = <?php echo $total_terverifikasi; ?>;
        const totalPen = <?php echo $total_pending; ?>;
        const totalDit = <?php 
            $total_ditolak = 0;
            foreach ($sekolah_binaan as $s) {
                $total_ditolak += $s['dokumen_ditolak'] ?? 0;
            }
            echo $total_ditolak; 
        ?>;
        const totalGuruSudah = <?php echo $total_guru_sudah; ?>;
        const totalGuruBelum = <?php echo $total_guru_belum; ?>;
        
        // Chart 1: Status Dokumen
        const ctx1 = document.getElementById('statusChart').getContext('2d');
        new Chart(ctx1, {
            type: 'doughnut',
            data: {
                labels: ['Terverifikasi', 'Pending', 'Ditolak'],
                datasets: [{
                    data: [totalTer, totalPen, totalDit],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                cutout: '70%'
            }
        });

        // Chart 2: Guru Sudah vs Belum
        const ctx2 = document.getElementById('guruChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Sudah Mengerjakan', 'Belum Mengerjakan'],
                datasets: [{
                    data: [totalGuruSudah, totalGuruBelum],
                    backgroundColor: ['#28a745', '#ffc107'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                cutout: '70%'
            }
        });
    </script>
</body>
</html>