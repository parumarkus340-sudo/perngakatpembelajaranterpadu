<?php
// profil.php - Halaman Profil User
session_start();

// Cek login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include_once 'config/database.php';

$id_user = $_SESSION['user_id'];

// Ambil data user
$user = fetchOne("
    SELECT u.*, s.nama_sekolah as sekolah_nama, s.npsn, s.kecamatan
    FROM users u
    LEFT JOIN sekolah s ON u.sekolah_id = s.id
    WHERE u.id = $id_user
");

if (!$user) {
    header('Location: index.php');
    exit;
}

// Proses update profil
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_profil') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $sekolah = mysqli_real_escape_string($conn, $_POST['sekolah']);
    
    $sql = "UPDATE users SET 
                name = '$name',
                phone = '$phone',
                sekolah = '$sekolah'
            WHERE id = $id_user";
    
    if (query($sql)) {
        $_SESSION['name'] = $name;
        $message = "✅ Profil berhasil diperbarui!";
        $message_type = 'success';
        // Refresh data
        $user = fetchOne("
            SELECT u.*, s.nama_sekolah as sekolah_nama, s.npsn, s.kecamatan
            FROM users u
            LEFT JOIN sekolah s ON u.sekolah_id = s.id
            WHERE u.id = $id_user
        ");
    } else {
        $message = "❌ Gagal memperbarui profil!";
        $message_type = 'error';
    }
}

// Proses ganti password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'ganti_password') {
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $password_konfirmasi = $_POST['password_konfirmasi'];
    
    // Verifikasi password lama
    if (!password_verify($password_lama, $user['password'])) {
        $message = "❌ Password lama salah!";
        $message_type = 'error';
    } elseif (strlen($password_baru) < 6) {
        $message = "❌ Password baru minimal 6 karakter!";
        $message_type = 'error';
    } elseif ($password_baru !== $password_konfirmasi) {
        $message = "❌ Konfirmasi password tidak sesuai!";
        $message_type = 'error';
    } else {
        $hashed = password_hash($password_baru, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = '$hashed' WHERE id = $id_user";
        if (query($sql)) {
            $message = "✅ Password berhasil diubah!";
            $message_type = 'success';
        } else {
            $message = "❌ Gagal mengubah password!";
            $message_type = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Pusat Perangkat Pembelajaran</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ============================================
           BASE
        ============================================ */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f0f4f8;
            color: #1a202c;
            line-height: 1.6;
        }
        .container { max-width: 1100px; margin: 0 auto; padding: 20px; }
        
        /* ============================================
           HEADER
        ============================================ */
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
        
        /* ============================================
           PROFIL CARD
        ============================================ */
        .profil-wrapper {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 24px;
            margin-top: 20px;
        }
        
        /* Sidebar Profil */
        .profil-sidebar {
            background: white;
            border-radius: 16px;
            padding: 30px 20px;
            text-align: center;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.04);
            height: fit-content;
        }
        
        .profil-sidebar .avatar-large {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 48px;
            font-weight: 700;
            color: white;
            box-shadow: 0 8px 30px rgba(102, 126, 234, 0.3);
        }
        
        .profil-sidebar .fullname {
            font-size: 18px;
            font-weight: 700;
            color: #0f2b5c;
        }
        
        .profil-sidebar .role-badge {
            display: inline-block;
            padding: 4px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 6px;
        }
        .role-admin { background: #f8d7da; color: #721c24; }
        .role-guru { background: #d4edda; color: #155724; }
        .role-kepala_sekolah { background: #cce5ff; color: #004085; }
        .role-pengawas { background: #fff3cd; color: #856404; }
        .role-dinas { background: #d1ecf1; color: #0c5460; }
        
        .profil-sidebar .email-info {
            font-size: 13px;
            color: #6b7280;
            margin-top: 8px;
        }
        
        .profil-sidebar .sekolah-info {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid #f3f4f6;
        }
        
        .profil-sidebar .sekolah-info .label {
            font-size: 11px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .profil-sidebar .sekolah-info .value {
            font-size: 14px;
            font-weight: 500;
            color: #0f2b5c;
        }
        
        .profil-sidebar .sekolah-info .npsn {
            font-size: 12px;
            color: #6b7280;
        }
        
        /* Main Content */
        .profil-main {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }
        
        .profil-card {
            background: white;
            border-radius: 16px;
            padding: 25px 30px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.04);
        }
        
        .profil-card .card-title {
            font-size: 16px;
            font-weight: 600;
            color: #0f2b5c;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .profil-card .card-title i {
            color: #667eea;
        }
        
        .form-group {
            margin-bottom: 18px;
        }
        
        .form-group label {
            display: block;
            font-weight: 500;
            font-size: 13px;
            color: #4a5568;
            margin-bottom: 4px;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
            background: #f9fafb;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #667eea;
            outline: none;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.08);
        }
        
        .form-group .help-text {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 4px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .btn {
            padding: 10px 30px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn:hover { transform: scale(1.02); }
        .btn-primary { background: #667eea; color: white; }
        .btn-primary:hover { background: #5a67d8; }
        .btn-success { background: #28a745; color: white; }
        .btn-success:hover { background: #218838; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-danger:hover { background: #c82333; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #5a6268; }
        
        .alert {
            padding: 15px;
            border-radius: 10px;
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
        
        .password-section {
            margin-top: 8px;
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
            .profil-wrapper { grid-template-columns: 1fr; }
            .profil-sidebar { padding: 20px; }
            .profil-sidebar .avatar-large {
                width: 80px;
                height: 80px;
                font-size: 32px;
            }
            .profil-card { padding: 20px; }
            .form-row { grid-template-columns: 1fr; }
        }
        @media (max-width: 480px) {
            .profil-card { padding: 16px; }
            .btn { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- HEADER -->
        <header class="header">
            <div>
                <h1><i class="fas fa-user-circle"></i> Profil Saya</h1>
                <p>Kelola informasi akun Anda</p>
            </div>
            <div class="badge-user">
                <i class="fas fa-user"></i> <?php echo $_SESSION['name']; ?>
            </div>
        </header>

        <!-- NAVBAR -->
        <?php include_once 'navbar.php'; ?>

        <!-- MAIN -->
        <main>
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="profil-wrapper">
                <!-- SIDEBAR -->
                <div class="profil-sidebar">
                    <div class="avatar-large">
                        <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                    </div>
                    <div class="fullname"><?php echo $user['name']; ?></div>
                    <span class="role-badge role-<?php echo $user['role']; ?>">
                        <?php 
                        $role_label = [
                            'admin' => '⚙️ Admin',
                            'guru' => '👨‍🏫 Guru',
                            'kepala_sekolah' => '👔 Kepala Sekolah',
                            'pengawas' => '🔍 Pengawas',
                            'dinas' => '📊 Dinas'
                        ];
                        echo $role_label[$user['role']] ?? $user['role'];
                        ?>
                    </span>
                    <div class="email-info">
                        <i class="fas fa-envelope"></i> <?php echo $user['email']; ?>
                    </div>
                    
                    <?php if (!empty($user['sekolah_nama']) || !empty($user['sekolah'])): ?>
                        <div class="sekolah-info">
                            <div class="label"><i class="fas fa-school"></i> Sekolah</div>
                            <div class="value"><?php echo $user['sekolah_nama'] ?? $user['sekolah']; ?></div>
                            <?php if (!empty($user['npsn'])): ?>
                                <div class="npsn">NPSN: <?php echo $user['npsn']; ?></div>
                            <?php endif; ?>
                            <?php if (!empty($user['kecamatan'])): ?>
                                <div class="npsn">Kecamatan: <?php echo $user['kecamatan']; ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div style="margin-top:16px; padding-top:16px; border-top:1px solid #f3f4f6;">
                        <div class="label">Status Akun</div>
                        <div class="value" style="font-size:14px; color:<?php echo $user['is_active'] ? '#28a745' : '#dc3545'; ?>;">
                            <?php echo $user['is_active'] ? '✅ Aktif' : '❌ Nonaktif'; ?>
                        </div>
                    </div>
                </div>

                <!-- MAIN CONTENT -->
                <div class="profil-main">
                    <!-- Edit Profil -->
                    <div class="profil-card">
                        <div class="card-title">
                            <i class="fas fa-edit"></i> Edit Profil
                        </div>
                        <form method="POST">
                            <input type="hidden" name="action" value="update_profil">
                            
                            <div class="form-group">
                                <label>Nama Lengkap</label>
                                <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled style="background:#f3f4f6; cursor:not-allowed;">
                                    <div class="help-text">Email tidak dapat diubah</div>
                                </div>
                                <div class="form-group">
                                    <label>No. Telepon</label>
                                    <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="Contoh: 0812-3456-7890">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Nama Sekolah / Instansi</label>
                                <input type="text" name="sekolah" value="<?php echo htmlspecialchars($user['sekolah'] ?? ''); ?>" placeholder="Nama sekolah atau instansi">
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </form>
                    </div>

                    <!-- Ganti Password -->
                    <div class="profil-card password-section">
                        <div class="card-title">
                            <i class="fas fa-key"></i> Ganti Password
                        </div>
                        <form method="POST">
                            <input type="hidden" name="action" value="ganti_password">
                            
                            <div class="form-group">
                                <label>Password Lama</label>
                                <input type="password" name="password_lama" required placeholder="Masukkan password lama">
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Password Baru</label>
                                    <input type="password" name="password_baru" required placeholder="Minimal 6 karakter">
                                </div>
                                <div class="form-group">
                                    <label>Konfirmasi Password Baru</label>
                                    <input type="password" name="password_konfirmasi" required placeholder="Ulangi password baru">
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-warning" style="background:#ffc107; color:#000;">
                                <i class="fas fa-key"></i> Ganti Password
                            </button>
                        </form>
                    </div>

                    <!-- Informasi Akun -->
                    <div class="profil-card">
                        <div class="card-title">
                            <i class="fas fa-info-circle"></i> Informasi Akun
                        </div>
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; font-size:14px;">
                            <div>
                                <span style="color:#6b7280;">ID User</span>
                                <div style="font-weight:600; color:#0f2b5c;">#<?php echo $user['id']; ?></div>
                            </div>
                            <div>
                                <span style="color:#6b7280;">Role</span>
                                <div style="font-weight:600; color:#0f2b5c;">
                                    <?php echo $role_label[$user['role']] ?? $user['role']; ?>
                                </div>
                            </div>
                            <div>
                                <span style="color:#6b7280;">Tanggal Bergabung</span>
                                <div style="font-weight:600; color:#0f2b5c;">
                                    <?php echo date('d F Y', strtotime($user['created_at'])); ?>
                                </div>
                            </div>
                            <div>
                                <span style="color:#6b7280;">Status</span>
                                <div style="font-weight:600; color:<?php echo $user['is_active'] ? '#28a745' : '#dc3545'; ?>;">
                                    <?php echo $user['is_active'] ? '✅ Aktif' : '❌ Nonaktif'; ?>
                                </div>
                            </div>
                        </div>
                    </div>
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