<?php
// kepsek_approve.php - Dashboard Kepala Sekolah
session_start();

// Cek login dan role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'kepala_sekolah') {
    header('Location: login.php');
    exit;
}

include_once 'config/database.php';

$id_kepsek = $_SESSION['user_id'];

// Ambil data Kepala Sekolah
$kepsek_data = getUserSekolah($id_kepsek);
$sekolah_id = $kepsek_data['sekolah_id'] ?? 0;
$nama_sekolah = $kepsek_data['nama_sekolah'] ?? 'Sekolah belum terdaftar';
$sekolah_npsn = $kepsek_data['npsn'] ?? '-';

// ============================================
// AMBIL DATA GURU BINAAN (SUDAH & BELUM)
// ============================================
$guru_binaan = fetchAll("
    SELECT u.*, 
           COUNT(p.id) as total_dokumen,
           SUM(CASE WHEN p.status = 'terverifikasi' THEN 1 ELSE 0 END) as dokumen_terverifikasi,
           SUM(CASE WHEN p.status = 'pending_kepsek' THEN 1 ELSE 0 END) as dokumen_pending_kepsek,
           SUM(CASE WHEN p.status = 'pending_pengawas' THEN 1 ELSE 0 END) as dokumen_pending_pengawas,
           SUM(CASE WHEN p.status = 'ditolak_kepsek' OR p.status = 'ditolak_pengawas' THEN 1 ELSE 0 END) as dokumen_ditolak,
           GROUP_CONCAT(DISTINCT p.judul SEPARATOR ', ') as judul_dokumen
    FROM users u
    LEFT JOIN perangkat p ON u.id = p.id_guru
    WHERE u.role = 'guru' AND u.sekolah_id = $sekolah_id
    GROUP BY u.id
    ORDER BY u.name
");

// Pisahkan guru sudah dan belum
$guru_sudah = [];
$guru_belum = [];
foreach ($guru_binaan as $g) {
    if ($g['total_dokumen'] > 0) {
        $guru_sudah[] = $g;
    } else {
        $guru_belum[] = $g;
    }
}

// ============================================
// PROSES TAMBAH GURU
// ============================================
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah_guru') {
    $name = escape($_POST['name']);
    $email = escape($_POST['email']);
    $nip = escape($_POST['nip']);
    $password = $_POST['password'];
    $role = 'guru';
    
    $errors = [];
    if (empty($name)) $errors[] = 'Nama harus diisi';
    if (empty($email)) $errors[] = 'Email harus diisi';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid';
    if (empty($password)) $errors[] = 'Password harus diisi';
    if (strlen($password) < 6) $errors[] = 'Password minimal 6 karakter';
    
    $cek_email = fetchOne("SELECT id FROM users WHERE email = '$email'");
    if ($cek_email) $errors[] = 'Email sudah terdaftar';
    
    if (!empty($nip)) {
        $cek_nip = fetchOne("SELECT id FROM users WHERE nip = '$nip'");
        if ($cek_nip) $errors[] = 'NIP sudah terdaftar';
    }
    
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (name, email, password, role, sekolah_id, nip, sekolah, is_active) VALUES (
            '$name', '$email', '$hashed_password', '$role', 
            " . ($sekolah_id > 0 ? $sekolah_id : 'NULL') . ",
            " . (!empty($nip) ? "'$nip'" : "NULL") . ",
            '$nama_sekolah', 1
        )";
        
        if (query($sql)) {
            $message = "✅ Guru <strong>$name</strong> berhasil ditambahkan!";
            $message_type = 'success';
            // Refresh data
            $guru_binaan = fetchAll("
                SELECT u.*, 
                       COUNT(p.id) as total_dokumen,
                       SUM(CASE WHEN p.status = 'terverifikasi' THEN 1 ELSE 0 END) as dokumen_terverifikasi,
                       SUM(CASE WHEN p.status = 'pending_kepsek' THEN 1 ELSE 0 END) as dokumen_pending_kepsek,
                       SUM(CASE WHEN p.status = 'pending_pengawas' THEN 1 ELSE 0 END) as dokumen_pending_pengawas,
                       SUM(CASE WHEN p.status = 'ditolak_kepsek' OR p.status = 'ditolak_pengawas' THEN 1 ELSE 0 END) as dokumen_ditolak,
                       GROUP_CONCAT(DISTINCT p.judul SEPARATOR ', ') as judul_dokumen
                FROM users u
                LEFT JOIN perangkat p ON u.id = p.id_guru
                WHERE u.role = 'guru' AND u.sekolah_id = $sekolah_id
                GROUP BY u.id
                ORDER BY u.name
            ");
            $guru_sudah = [];
            $guru_belum = [];
            foreach ($guru_binaan as $g) {
                if ($g['total_dokumen'] > 0) {
                    $guru_sudah[] = $g;
                } else {
                    $guru_belum[] = $g;
                }
            }
        } else {
            $message = "❌ Gagal menambahkan guru: " . mysqli_error($conn);
            $message_type = 'error';
        }
    } else {
        $message = "❌ " . implode('<br>', $errors);
        $message_type = 'error';
    }
}

// ============================================
// PROSES APPROVE/REJECT DOKUMEN
// ============================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action_approve'])) {
    $id_perangkat = (int)$_POST['id_perangkat'];
    $action = $_POST['action_approve'];
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan'] ?? '');
    
    if ($action == 'approve') {
        if (approvePerangkat($id_perangkat, $id_kepsek, 'kepala_sekolah', $catatan)) {
            $message = "✅ Dokumen berhasil disetujui dan ditandatangani!";
            $message_type = 'success';
        }
    } elseif ($action == 'reject') {
        if (tolakPerangkat($id_perangkat, $id_kepsek, 'kepala_sekolah', $catatan)) {
            $message = "❌ Dokumen ditolak!";
            $message_type = 'success';
        }
    }
}

// ============================================
// PROSES AKTIF/NONAKTIF GURU
// ============================================
if (isset($_GET['toggle']) && isset($_GET['id'])) {
    $guru_id = (int)$_GET['id'];
    $guru = fetchOne("SELECT id, is_active FROM users WHERE id = $guru_id AND role = 'guru' AND sekolah_id = $sekolah_id");
    if ($guru) {
        $new_status = $guru['is_active'] ? 0 : 1;
        query("UPDATE users SET is_active = $new_status WHERE id = $guru_id");
        $message = $new_status ? "✅ Guru berhasil diaktifkan!" : "⚠️ Guru berhasil dinonaktifkan!";
        $message_type = 'success';
        // Refresh data
        $guru_binaan = fetchAll("
            SELECT u.*, 
                   COUNT(p.id) as total_dokumen,
                   SUM(CASE WHEN p.status = 'terverifikasi' THEN 1 ELSE 0 END) as dokumen_terverifikasi,
                   SUM(CASE WHEN p.status = 'pending_kepsek' THEN 1 ELSE 0 END) as dokumen_pending_kepsek,
                   SUM(CASE WHEN p.status = 'pending_pengawas' THEN 1 ELSE 0 END) as dokumen_pending_pengawas,
                   SUM(CASE WHEN p.status = 'ditolak_kepsek' OR p.status = 'ditolak_pengawas' THEN 1 ELSE 0 END) as dokumen_ditolak,
                   GROUP_CONCAT(DISTINCT p.judul SEPARATOR ', ') as judul_dokumen
            FROM users u
            LEFT JOIN perangkat p ON u.id = p.id_guru
            WHERE u.role = 'guru' AND u.sekolah_id = $sekolah_id
            GROUP BY u.id
            ORDER BY u.name
        ");
        $guru_sudah = [];
        $guru_belum = [];
        foreach ($guru_binaan as $g) {
            if ($g['total_dokumen'] > 0) {
                $guru_sudah[] = $g;
            } else {
                $guru_belum[] = $g;
            }
        }
    }
}

// ============================================
// PROSES HAPUS GURU
// ============================================
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $guru_id = (int)$_GET['id'];
    $guru = fetchOne("SELECT id FROM users WHERE id = $guru_id AND role = 'guru' AND sekolah_id = $sekolah_id");
    if ($guru) {
        $dokumen = fetchOne("SELECT id FROM perangkat WHERE id_guru = $guru_id LIMIT 1");
        if ($dokumen) {
            $message = "❌ Guru tidak dapat dihapus karena memiliki dokumen. Nonaktifkan saja.";
            $message_type = 'error';
        } else {
            query("DELETE FROM users WHERE id = $guru_id");
            $message = "✅ Guru berhasil dihapus!";
            $message_type = 'success';
            // Refresh data
            $guru_binaan = fetchAll("
                SELECT u.*, 
                       COUNT(p.id) as total_dokumen,
                       SUM(CASE WHEN p.status = 'terverifikasi' THEN 1 ELSE 0 END) as dokumen_terverifikasi,
                       SUM(CASE WHEN p.status = 'pending_kepsek' THEN 1 ELSE 0 END) as dokumen_pending_kepsek,
                       SUM(CASE WHEN p.status = 'pending_pengawas' THEN 1 ELSE 0 END) as dokumen_pending_pengawas,
                       SUM(CASE WHEN p.status = 'ditolak_kepsek' OR p.status = 'ditolak_pengawas' THEN 1 ELSE 0 END) as dokumen_ditolak,
                       GROUP_CONCAT(DISTINCT p.judul SEPARATOR ', ') as judul_dokumen
                FROM users u
                LEFT JOIN perangkat p ON u.id = p.id_guru
                WHERE u.role = 'guru' AND u.sekolah_id = $sekolah_id
                GROUP BY u.id
                ORDER BY u.name
            ");
            $guru_sudah = [];
            $guru_belum = [];
            foreach ($guru_binaan as $g) {
                if ($g['total_dokumen'] > 0) {
                    $guru_sudah[] = $g;
                } else {
                    $guru_belum[] = $g;
                }
            }
        }
    }
}

// ============================================
// AMBIL PERANGKAT PENDING
// ============================================
$pending_list = getPerangkatPendingKepsek($id_kepsek);

$riwayat = fetchAll("
    SELECT rs.*, u.name as user_name, p.judul as perangkat_judul
    FROM riwayat_status rs
    JOIN users u ON rs.id_user = u.id
    LEFT JOIN perangkat p ON rs.id_perangkat = p.id
    WHERE u.sekolah_id = $sekolah_id
    ORDER BY rs.created_at DESC
    LIMIT 30
");

// Statistik
$total_guru = count($guru_binaan);
$total_pending = count($pending_list);
$total_dokumen = 0;
$total_terverifikasi = 0;
foreach ($guru_binaan as $g) {
    $total_dokumen += $g['total_dokumen'];
    $total_terverifikasi += $g['dokumen_terverifikasi'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kepala Sekolah</title>
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
        .container { max-width: 1280px; margin: 0 auto; padding: 20px; }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 25px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        .header h1 { margin: 0; font-size: 1.5em; }
        .header p { margin: 5px 0 0 0; opacity: 0.9; }
        
        .dashboard-header {
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
        .dashboard-header .info h2 { margin: 0; font-size: 1.5em; }
        .dashboard-header .info p { opacity: 0.9; margin: 5px 0 0 0; font-size: 14px; }
        .dashboard-header .btn-tambah-guru {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 10px 25px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .dashboard-header .btn-tambah-guru:hover {
            background: rgba(255,255,255,0.3);
            transform: scale(1.02);
        }
        
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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .stats-grid .stat-card {
            background: white;
            padding: 18px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.04);
            transition: all 0.3s ease;
        }
        .stats-grid .stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 30px rgba(0,0,0,0.10); }
        .stats-grid .stat-card .number {
            font-size: 2.2em;
            font-weight: 700;
            color: #667eea;
        }
        .stats-grid .stat-card .number.green { color: #28a745; }
        .stats-grid .stat-card .number.orange { color: #ffc107; }
        .stats-grid .stat-card .number.red { color: #dc3545; }
        .stats-grid .stat-card .label { font-size: 13px; color: #666; }
        .stats-grid .stat-card .sub-label { font-size: 11px; color: #999; margin-top: 2px; }
        
        /* Guru Cards */
        .guru-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }
        .guru-card-section {
            background: white;
            border-radius: 12px;
            padding: 18px 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.04);
        }
        .guru-card-section .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #f1f5f9;
        }
        .guru-card-section .section-header h4 {
            font-size: 14px;
            font-weight: 600;
            color: #0f2b5c;
        }
        .guru-card-section .section-header .count {
            font-size: 12px;
            padding: 2px 12px;
            border-radius: 20px;
            font-weight: 600;
        }
        .guru-card-section .section-header .count.sudah {
            background: #d1fae5;
            color: #059669;
        }
        .guru-card-section .section-header .count.belum {
            background: #fef3c7;
            color: #d97706;
        }
        
        .guru-list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 10px;
            border-radius: 8px;
            margin-bottom: 4px;
            transition: all 0.2s;
            font-size: 13px;
        }
        .guru-list-item:hover { background: #f8fafc; }
        .guru-list-item .guru-name {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .guru-list-item .guru-name i { font-size: 12px; }
        .guru-list-item .guru-name .status-icon {
            font-size: 12px;
        }
        .guru-list-item .guru-name .status-icon.sudah { color: #28a745; }
        .guru-list-item .guru-name .status-icon.belum { color: #d97706; }
        .guru-list-item .guru-stats {
            font-size: 11px;
            color: #6b7280;
        }
        .guru-list-item .guru-stats .sudah-count { color: #28a745; }
        .guru-list-item .guru-stats .pending-count { color: #ffc107; }
        .guru-list-item .guru-stats .ditolak-count { color: #dc3545; }
        .guru-list-item .guru-actions {
            display: flex;
            gap: 4px;
        }
        .guru-list-item .guru-actions .btn-sm {
            padding: 2px 8px;
            font-size: 10px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }
        .guru-list-item .guru-actions .btn-toggle {
            background: #6c757d;
            color: white;
        }
        .guru-list-item .guru-actions .btn-toggle.active { background: #28a745; }
        .guru-list-item .guru-actions .btn-delete {
            background: #dc3545;
            color: white;
        }
        
        .no-guru-text {
            text-align: center;
            padding: 20px;
            color: #9ca3af;
            font-size: 13px;
        }
        
        /* Table */
        .table-container {
            overflow-x: auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-top: 15px;
        }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #667eea; color: white; font-weight: 600; }
        tr:hover { background: #f5f5f5; }
        
        .status-badge {
            padding: 3px 12px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: bold;
            display: inline-block;
        }
        .status-pending { background: #ffc107; color: #000; }
        .status-approved { background: #28a745; color: #fff; }
        .status-rejected { background: #dc3545; color: #fff; }
        .status-draft { background: #6c757d; color: #fff; }
        .status-terverifikasi { background: #28a745; color: #fff; }
        .status-ditolak { background: #dc3545; color: #fff; }
        
        .btn-approve {
            background: #28a745;
            color: white;
            padding: 5px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
        }
        .btn-approve:hover { background: #218838; }
        .btn-reject {
            background: #dc3545;
            color: white;
            padding: 5px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
        }
        .btn-reject:hover { background: #c82333; }
        .btn-download {
            background: #667eea;
            color: white;
            padding: 5px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 11px;
            display: inline-block;
        }
        .btn-download:hover { background: #5a67d8; }
        
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
            font-size: 12px;
            width: 120px;
        }
        
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
            background: #667eea;
            color: white;
            padding: 2px 14px;
            border-radius: 20px;
            font-size: 12px;
        }
        
        .empty-state { text-align: center; padding: 40px; color: #888; }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(4px);
        }
        .modal-overlay.active { display: flex; }
        .modal-content {
            background: white;
            border-radius: 15px;
            padding: 35px;
            max-width: 550px;
            width: 95%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: modalSlideIn 0.3s ease;
        }
        @keyframes modalSlideIn {
            from { transform: translateY(-30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .modal-content .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f4f8;
        }
        .modal-content .modal-header h3 { margin: 0; color: #333; font-size: 1.3em; }
        .modal-content .modal-header .close-btn {
            background: none;
            border: none;
            font-size: 28px;
            color: #aaa;
            cursor: pointer;
        }
        .modal-content .modal-header .close-btn:hover { color: #333; }
        .modal-content .form-group { margin-bottom: 18px; }
        .modal-content .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            color: #555;
            font-size: 14px;
        }
        .modal-content .form-group input {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid #e8ecf1;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        .modal-content .form-group input:focus {
            border-color: #667eea;
            outline: none;
        }
        .modal-content .form-group .help-text { font-size: 12px; color: #888; margin-top: 4px; }
        .modal-content .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .modal-content .btn-submit:hover { transform: scale(1.02); }
        
        @media (max-width: 768px) {
            .guru-grid { grid-template-columns: 1fr; }
            .dashboard-header { flex-direction: column; gap: 10px; text-align: center; }
            .nav { padding: 10px 16px; justify-content: center; }
            .nav .user-info { margin-left: 0; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .modal-content { padding: 25px 20px; }
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
        <header>
            <h1>📋 Dashboard Kepala Sekolah</h1>
            <p>Kelola Guru Binaan & Verifikasi Dokumen</p>
        </header>

         <!-- ===== NAVIGASI PAKAI NAVBAR.PHP ===== -->
        <?php include_once 'navbar.php'; ?>
        <!-- ===================================== -->

        <main>
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <!-- Header -->
            <div class="dashboard-header">
                <div class="info">
                    <h2>👔 <?php echo $_SESSION['name']; ?></h2>
                    <p>🏫 <strong><?php echo $nama_sekolah; ?></strong> &nbsp;|&nbsp; NPSN: <?php echo $sekolah_npsn; ?></p>
                </div>
                <button class="btn-tambah-guru" onclick="openModal()"><i class="fas fa-plus"></i> Tambah Guru</button>
            </div>

            <!-- Statistik -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="number"><?php echo $total_guru; ?></div>
                    <div class="label">👨‍🏫 Total Guru</div>
                    <div class="sub-label">✅ <?php echo count($guru_sudah); ?> sudah · ⏳ <?php echo count($guru_belum); ?> belum</div>
                </div>
                <div class="stat-card">
                    <div class="number orange"><?php echo $total_pending; ?></div>
                    <div class="label">⏳ Menunggu Persetujuan</div>
                </div>
                <div class="stat-card">
                    <div class="number"><?php echo $total_dokumen; ?></div>
                    <div class="label">📄 Total Dokumen</div>
                </div>
                <div class="stat-card">
                    <div class="number green"><?php echo $total_terverifikasi; ?></div>
                    <div class="label">✅ Terverifikasi</div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- GURU SUDAH & BELUM -->
            <!-- ========================================== -->
            <div class="guru-grid">
                <!-- Guru SUDAH Mengerjakan -->
                <div class="guru-card-section">
                    <div class="section-header">
                        <h4><i class="fas fa-check-circle" style="color:#28a745;"></i> Guru Sudah Mengerjakan</h4>
                        <span class="count sudah"><?php echo count($guru_sudah); ?> Guru</span>
                    </div>
                    <?php if (count($guru_sudah) > 0): ?>
                        <?php foreach ($guru_sudah as $g): ?>
                            <div class="guru-list-item">
                                <div class="guru-name">
                                    <span class="status-icon sudah"><i class="fas fa-check-circle"></i></span>
                                    <span><?php echo $g['name']; ?></span>
                                    <?php if (!empty($g['nip'])): ?>
                                        <span style="font-size:10px; color:#999;">(<?php echo $g['nip']; ?>)</span>
                                    <?php endif; ?>
                                </div>
                                <div class="guru-stats">
                                    <span class="sudah-count">✅ <?php echo $g['dokumen_terverifikasi']; ?></span>
                                    <span class="pending-count">⏳ <?php echo $g['dokumen_pending_kepsek'] + $g['dokumen_pending_pengawas']; ?></span>
                                    <span class="ditolak-count">❌ <?php echo $g['dokumen_ditolak']; ?></span>
                                    <span>📄 <?php echo $g['total_dokumen']; ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-guru-text">Belum ada guru yang mengerjakan perangkat</div>
                    <?php endif; ?>
                </div>

                <!-- Guru BELUM Mengerjakan -->
                <div class="guru-card-section">
                    <div class="section-header">
                        <h4><i class="fas fa-clock" style="color:#d97706;"></i> Guru Belum Mengerjakan</h4>
                        <span class="count belum"><?php echo count($guru_belum); ?> Guru</span>
                    </div>
                    <?php if (count($guru_belum) > 0): ?>
                        <?php foreach ($guru_belum as $g): ?>
                            <div class="guru-list-item">
                                <div class="guru-name">
                                    <span class="status-icon belum"><i class="fas fa-clock"></i></span>
                                    <span><?php echo $g['name']; ?></span>
                                    <?php if (!empty($g['nip'])): ?>
                                        <span style="font-size:10px; color:#999;">(<?php echo $g['nip']; ?>)</span>
                                    <?php endif; ?>
                                </div>
                                <div class="guru-stats">
                                    <span style="color:#d97706;">⏳ Belum membuat perangkat</span>
                                </div>
                                <div class="guru-actions">
                                    <a href="?toggle=1&id=<?php echo $g['id']; ?>" class="btn-sm btn-toggle <?php echo $g['is_active'] ? 'active' : ''; ?>" onclick="return confirm('Ubah status guru ini?')">
                                        <?php echo $g['is_active'] ? '🔴' : '🟢'; ?>
                                    </a>
                                    <?php if ($g['total_dokumen'] == 0): ?>
                                        <a href="?delete=1&id=<?php echo $g['id']; ?>" class="btn-sm btn-delete" onclick="return confirm('Hapus guru ini?')">🗑️</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-guru-text" style="color:#28a745;">
                            <i class="fas fa-check-circle"></i> Semua guru sudah mengerjakan perangkat!
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- DOKUMEN MENUNGGU PERSETUJUAN -->
            <!-- ========================================== -->
            <div class="section-title">
                <span>📄 Dokumen Menunggu Persetujuan <span class="badge-count"><?php echo $total_pending; ?></span></span>
            </div>

            <?php if (count($pending_list) > 0): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul</th>
                                <th>Guru</th>
                                <th>Mapel</th>
                                <th>Kelas</th>
                                <th>File</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($pending_list as $p): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><strong><?php echo $p['judul']; ?></strong></td>
                                <td><?php echo $p['guru_name']; ?></td>
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
                                        <input type="hidden" name="action_approve" value="approve">
                                        <button type="submit" class="btn-approve" onclick="return confirm('Setuju dan tanda tangani dokumen ini?')">
                                            ✅ TTD
                                        </button>
                                    </form>
                                    <form action="" method="POST" class="reject-form" style="display:inline-flex; margin-top:5px;">
                                        <input type="hidden" name="id_perangkat" value="<?php echo $p['id']; ?>">
                                        <input type="hidden" name="action_approve" value="reject">
                                        <input type="text" name="catatan" placeholder="Alasan ditolak..." style="padding:3px 8px; font-size:12px; width:120px; border:1px solid #ddd; border-radius:4px;">
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
                    ✅ Tidak ada dokumen yang menunggu persetujuan.
                </div>
            <?php endif; ?>

            <!-- ========================================== -->
            <!-- RIWAYAT AKTIVITAS -->
            <!-- ========================================== -->
            <div class="section-title">
                <span>📜 Riwayat Aktivitas</span>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>User</th>
                            <th>Dokumen</th>
                            <th>Status</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($riwayat) > 0): ?>
                            <?php foreach ($riwayat as $r): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($r['created_at'])); ?></td>
                                <td><?php echo $r['user_name']; ?></td>
                                <td><?php echo $r['perangkat_judul'] ?? 'ID: ' . $r['id_perangkat']; ?></td>
                                <td>
                                    <span class="status-badge 
                                        <?php echo $r['status_akhir'] == 'terverifikasi' ? 'status-approved' : 
                                                ($r['status_akhir'] == 'pending_kepsek' ? 'status-pending' : 
                                                'status-rejected'); ?>">
                                        <?php echo str_replace('_', ' ', $r['status_akhir']); ?>
                                    </span>
                                </td>
                                <td><?php echo $r['catatan'] ?? '-'; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align:center; color:#888;">Belum ada riwayat aktivitas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>

        <footer style="text-align:center; margin-top:30px; color:#888; font-size:13px; border-top:1px solid #e0e0e0; padding-top:20px;">
            <p>&copy; 2026 Pusat Perangkat Pembelajaran</p>
        </footer>
    </div>

    <!-- ===== MODAL TAMBAH GURU ===== -->
    <div class="modal-overlay" id="modalTambahGuru">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-user-plus"></i> Tambah Guru Binaan</h3>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form method="POST" onsubmit="return validateForm()">
                <input type="hidden" name="action" value="tambah_guru">
                
                <div class="form-group">
                    <label>👤 Nama Lengkap <span style="color:#dc3545;">*</span></label>
                    <input type="text" name="name" id="name" required placeholder="Masukkan nama lengkap guru">
                </div>
                
                <div class="form-group">
                    <label>📧 Email <span style="color:#dc3545;">*</span></label>
                    <input type="email" name="email" id="email" required placeholder="guru@sekolah.com">
                    <div class="help-text">Email akan digunakan untuk login</div>
                </div>
                
                <div class="form-group">
                    <label>🔑 Password <span style="color:#dc3545;">*</span></label>
                    <input type="password" name="password" id="password" required placeholder="Minimal 6 karakter">
                    <div class="help-text">Minimal 6 karakter</div>
                </div>
                
                <div class="form-group">
                    <label>🆔 NIP (Opsional)</label>
                    <input type="text" name="nip" id="nip" placeholder="Nomor Induk Pegawai">
                </div>
                
                <div class="form-group" style="background: #f8f9fa; padding: 12px; border-radius: 8px; margin-bottom: 18px;">
                    <p style="margin: 0; font-size: 13px; color: #555;">
                        <strong>🏫 Sekolah:</strong> <?php echo $nama_sekolah; ?>
                    </p>
                    <p style="margin: 5px 0 0 0; font-size: 12px; color: #888;">
                        Guru akan otomatis terdaftar di sekolah ini
                    </p>
                </div>
                
                <button type="submit" class="btn-submit"><i class="fas fa-plus"></i> Tambah Guru</button>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('modalTambahGuru').classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function closeModal() {
            document.getElementById('modalTambahGuru').classList.remove('active');
            document.body.style.overflow = 'auto';
        }
        
        document.getElementById('modalTambahGuru').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
        
        function validateForm() {
            var name = document.getElementById('name').value.trim();
            var email = document.getElementById('email').value.trim();
            var password = document.getElementById('password').value;
            
            if (name.length < 3) {
                alert('Nama minimal 3 karakter');
                return false;
            }
            if (email.length < 5 || !email.includes('@')) {
                alert('Masukkan email yang valid');
                return false;
            }
            if (password.length < 6) {
                alert('Password minimal 6 karakter');
                return false;
            }
            return true;
        }
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html>