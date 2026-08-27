<?php
// admin_edit_user.php - Edit Data User + Kelola Sekolah Binaan (Multi)
session_start();

// Cek login dan role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

include_once 'config/database.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id == 0) {
    header('Location: admin_users.php');
    exit;
}

// Ambil data user
$user = fetchOne("SELECT * FROM users WHERE id = $id");
if (!$user) {
    header('Location: admin_users.php');
    exit;
}

// Ambil daftar sekolah untuk dropdown
$sekolah_list = fetchAll("SELECT * FROM sekolah ORDER BY nama_sekolah");

// ============================================
// AMBIL SEKOLAH BINAAN (jika user adalah pengawas)
// ============================================
$sekolah_binaan = [];
$sekolah_binaan_ids = [];
if ($user['role'] == 'pengawas') {
    $sekolah_binaan = fetchAll("
        SELECT s.* 
        FROM sekolah s
        JOIN pengawas_sekolah ps ON s.id = ps.sekolah_id
        WHERE ps.pengawas_id = $id
        ORDER BY s.nama_sekolah
    ");
    foreach ($sekolah_binaan as $s) {
        $sekolah_binaan_ids[] = $s['id'];
    }
}

// ============================================
// PROSES UPDATE USER
// ============================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $sekolah = mysqli_real_escape_string($conn, $_POST['sekolah']);
    $sekolah_id = !empty($_POST['sekolah_id']) ? (int)$_POST['sekolah_id'] : 'NULL';
    $nip = mysqli_real_escape_string($conn, $_POST['nip']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Cek email duplikat
    $cek = fetchOne("SELECT id FROM users WHERE email = '$email' AND id != $id");
    if ($cek) {
        $error = "❌ Email sudah digunakan oleh user lain!";
    } else {
        // Cek NIP duplikat
        $cek_nip = fetchOne("SELECT id FROM users WHERE nip = '$nip' AND id != $id AND nip != ''");
        if ($cek_nip) {
            $error = "❌ NIP sudah digunakan oleh user lain!";
        } else {
            $sql = "UPDATE users SET 
                        name = '$name',
                        email = '$email',
                        role = '$role',
                        sekolah = '$sekolah',
                        sekolah_id = $sekolah_id,
                        nip = " . (!empty($nip) ? "'$nip'" : "NULL") . ",
                        phone = " . (!empty($phone) ? "'$phone'" : "NULL") . ",
                        is_active = $is_active
                    WHERE id = $id";
            
            if (query($sql)) {
                $success = "✅ Data user berhasil diperbarui!";
                // Refresh data
                $user = fetchOne("SELECT * FROM users WHERE id = $id");
            } else {
                $error = "❌ Gagal memperbarui data: " . mysqli_error($conn);
            }
        }
    }
}

// ============================================
// PROSES TAMBAH SEKOLAH BINAAN
// ============================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah_binaan') {
    $id_pengawas = (int)$_POST['id_pengawas'];
    $id_sekolah = (int)$_POST['id_sekolah_binaan'];
    
    if ($id_sekolah > 0) {
        // Cek apakah sudah ada
        $cek = fetchOne("SELECT id FROM pengawas_sekolah WHERE pengawas_id = $id_pengawas AND sekolah_id = $id_sekolah");
        if ($cek) {
            $error_binaan = "❌ Sekolah sudah terdaftar sebagai binaan!";
        } else {
            $sql = "INSERT INTO pengawas_sekolah (pengawas_id, sekolah_id) VALUES ($id_pengawas, $id_sekolah)";
            if (query($sql)) {
                $success_binaan = "✅ Sekolah binaan berhasil ditambahkan!";
                // Refresh data
                $sekolah_binaan = fetchAll("
                    SELECT s.* 
                    FROM sekolah s
                    JOIN pengawas_sekolah ps ON s.id = ps.sekolah_id
                    WHERE ps.pengawas_id = $id
                    ORDER BY s.nama_sekolah
                ");
                foreach ($sekolah_binaan as $s) {
                    $sekolah_binaan_ids[] = $s['id'];
                }
            } else {
                $error_binaan = "❌ Gagal menambahkan sekolah binaan!";
            }
        }
    } else {
        $error_binaan = "❌ Pilih sekolah terlebih dahulu!";
    }
}

// ============================================
// PROSES HAPUS SEKOLAH BINAAN
// ============================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'hapus_binaan') {
    $id_pengawas = (int)$_POST['id_pengawas'];
    $id_sekolah = (int)$_POST['id_sekolah'];
    
    $sql = "DELETE FROM pengawas_sekolah WHERE pengawas_id = $id_pengawas AND sekolah_id = $id_sekolah";
    if (query($sql)) {
        $success_binaan = "✅ Sekolah binaan berhasil dihapus!";
        // Refresh data
        $sekolah_binaan = fetchAll("
            SELECT s.* 
            FROM sekolah s
            JOIN pengawas_sekolah ps ON s.id = ps.sekolah_id
            WHERE ps.pengawas_id = $id
            ORDER BY s.nama_sekolah
        ");
        $sekolah_binaan_ids = [];
        foreach ($sekolah_binaan as $s) {
            $sekolah_binaan_ids[] = $s['id'];
        }
    } else {
        $error_binaan = "❌ Gagal menghapus sekolah binaan!";
    }
}

// ============================================
// PROSES HAPUS SEMUA BINAAN
// ============================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'hapus_semua_binaan') {
    $id_pengawas = (int)$_POST['id_pengawas'];
    
    $sql = "DELETE FROM pengawas_sekolah WHERE pengawas_id = $id_pengawas";
    if (query($sql)) {
        $success_binaan = "✅ Semua sekolah binaan berhasil dihapus!";
        $sekolah_binaan = [];
        $sekolah_binaan_ids = [];
    } else {
        $error_binaan = "❌ Gagal menghapus sekolah binaan!";
    }
}

// ============================================
// PROSES RESET PASSWORD
// ============================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'reset_password') {
    $new_password = $_POST['new_password'];
    if (strlen($new_password) < 6) {
        $error = "❌ Password minimal 6 karakter!";
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = '$hashed' WHERE id = $id";
        if (query($sql)) {
            $success = "✅ Password berhasil direset menjadi: <strong>$new_password</strong>";
        } else {
            $error = "❌ Gagal reset password: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Admin</title>
    <link rel="stylesheet" href="/website_perangkat/public/css/style.css">
    <style>
        .container { max-width: 900px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px 25px; border-radius: 12px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 1.5em; }
        .header p { margin: 5px 0 0 0; opacity: 0.9; }
        
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            color: #555;
            font-size: 14px;
        }
        .form-group label .required {
            color: #dc3545;
        }
        .form-group input, 
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid #e8ecf1;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
            background: #fafafa;
        }
        .form-group input:focus,
        .form-group select:focus {
            border-color: #667eea;
            outline: none;
            background: white;
        }
        .form-group .help-text {
            font-size: 12px;
            color: #888;
            margin-top: 4px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        .btn {
            padding: 10px 30px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
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
        .btn-warning { background: #ffc107; color: #000; }
        .btn-warning:hover { background: #e0a800; }
        .btn-sm { padding: 4px 12px; font-size: 12px; }
        
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
        
        .role-badge {
            display: inline-block;
            padding: 2px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .role-admin { background: #f8d7da; color: #721c24; }
        .role-guru { background: #d4edda; color: #155724; }
        .role-kepala_sekolah { background: #cce5ff; color: #004085; }
        .role-pengawas { background: #fff3cd; color: #856404; }
        .role-dinas { background: #d1ecf1; color: #0c5460; }
        
        .info-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        .info-box p { margin: 4px 0; font-size: 13px; color: #555; }
        
        .binaan-section {
            background: #e8f0fe;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #c5d5e8;
            margin-top: 20px;
        }
        .binaan-section h4 {
            margin: 0 0 10px 0;
            color: #0d47a1;
        }
        .binaan-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 10px 0;
        }
        .binaan-item {
            background: white;
            padding: 5px 14px;
            border-radius: 15px;
            font-size: 13px;
            color: #0d47a1;
            font-weight: 500;
            border: 1px solid #c5d5e8;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .binaan-item .remove-btn {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
            padding: 0 4px;
        }
        .binaan-item .remove-btn:hover { color: #c82333; }
        
        .binaan-form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
            margin-top: 10px;
        }
        .binaan-form select {
            padding: 8px 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 13px;
            min-width: 250px;
            flex: 1;
        }
        .binaan-form select:focus {
            border-color: #0d47a1;
            outline: none;
        }
        
        .password-section {
            background: #fff3cd;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ffc107;
            margin-top: 20px;
        }
        .password-section h4 { margin: 0 0 10px 0; color: #856404; }
        
        .delete-section {
            background: #f8d7da;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #dc3545;
            margin-top: 20px;
        }
        .delete-section h4 { margin: 0 0 10px 0; color: #721c24; }
        
        @media (max-width: 768px) {
            .form-row { grid-template-columns: 1fr; }
            .container { padding: 10px; }
            .form-container { padding: 20px; }
            .binaan-form { flex-direction: column; }
            .binaan-form select { min-width: 100%; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✏️ Edit Data User</h1>
            <p>Perbarui informasi user dan kelola sekolah binaan</p>
        </div>

        <nav>
            <a href="/website_perangkat/index.php">🏠 Beranda</a>
            <a href="/website_perangkat/admin_dashboard.php">📊 Dashboard</a>
            <a href="/website_perangkat/admin_users.php">👥 User</a>
            <a href="/website_perangkat/logout.php">🚪 Logout</a>
        </nav>

        <main>
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Info User -->
            <div class="info-box">
                <p><strong>ID:</strong> <?php echo $user['id']; ?></p>
                <p><strong>Role:</strong> <span class="role-badge role-<?php echo $user['role']; ?>"><?php echo $user['role']; ?></span></p>
                <p><strong>Status:</strong> <?php echo $user['is_active'] ? '✅ Aktif' : '❌ Nonaktif'; ?></p>
            </div>

            <!-- Form Edit User -->
            <div class="form-container">
                <form method="POST">
                    <input type="hidden" name="action" value="update">
                    
                    <div class="form-group">
                        <label>👤 Nama Lengkap <span class="required">*</span></label>
                        <input type="text" name="name" required value="<?php echo htmlspecialchars($user['name']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>📧 Email <span class="required">*</span></label>
                        <input type="email" name="email" required value="<?php echo htmlspecialchars($user['email']); ?>">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>🔑 Role <span class="required">*</span></label>
                            <select name="role" required>
                                <option value="guru" <?php echo $user['role'] == 'guru' ? 'selected' : ''; ?>>👨‍🏫 Guru</option>
                                <option value="kepala_sekolah" <?php echo $user['role'] == 'kepala_sekolah' ? 'selected' : ''; ?>>👔 Kepala Sekolah</option>
                                <option value="pengawas" <?php echo $user['role'] == 'pengawas' ? 'selected' : ''; ?>>🔍 Pengawas</option>
                                <option value="dinas" <?php echo $user['role'] == 'dinas' ? 'selected' : ''; ?>>📊 Dinas</option>
                                <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>⚙️ Admin</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>🆔 NIP</label>
                            <input type="text" name="nip" value="<?php echo htmlspecialchars($user['nip'] ?? ''); ?>">
                            <div class="help-text">Nomor Induk Pegawai (opsional)</div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>📱 No. Telepon</label>
                            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>🏫 Sekolah (Teks)</label>
                            <input type="text" name="sekolah" value="<?php echo htmlspecialchars($user['sekolah'] ?? ''); ?>" placeholder="Nama sekolah/instansi">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>🏫 Sekolah (Terdaftar)</label>
                        <select name="sekolah_id">
                            <option value="">-- Tidak terhubung --</option>
                            <?php foreach ($sekolah_list as $s): ?>
                                <option value="<?php echo $s['id']; ?>" <?php echo ($user['sekolah_id'] == $s['id']) ? 'selected' : ''; ?>>
                                    <?php echo $s['nama_sekolah']; ?> (<?php echo $s['npsn']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="help-text">Hubungkan user dengan sekolah yang terdaftar di database</div>
                    </div>
                    
                    <div class="form-group" style="margin-top:10px;">
                        <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                            <input type="checkbox" name="is_active" value="1" <?php echo $user['is_active'] ? 'checked' : ''; ?> style="width:18px; height:18px;">
                            <span>✅ Akun Aktif</span>
                        </label>
                        <div class="help-text">Nonaktifkan untuk menonaktifkan akses user</div>
                    </div>
                    
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
                        <a href="admin_users.php" class="btn btn-secondary">🔙 Kembali</a>
                    </div>
                </form>
            </div>

            <!-- ========================================== -->
            <!-- MANAJEMEN SEKOLAH BINAAN (KHUSUS PENGAWAS) -->
            <!-- ========================================== -->
            <?php if ($user['role'] == 'pengawas'): ?>
                <div class="binaan-section">
                    <h4>🏫 Kelola Sekolah Binaan</h4>
                    <p style="font-size:13px; color:#555; margin-bottom:10px;">
                        Pengawas dapat memiliki lebih dari 1 sekolah binaan.
                    </p>

                    <?php if (isset($success_binaan)): ?>
                        <div class="alert alert-success"><?php echo $success_binaan; ?></div>
                    <?php endif; ?>
                    <?php if (isset($error_binaan)): ?>
                        <div class="alert alert-error"><?php echo $error_binaan; ?></div>
                    <?php endif; ?>

                    <!-- Daftar Sekolah Binaan -->
                    <div class="binaan-list">
                        <?php if (count($sekolah_binaan) > 0): ?>
                            <?php foreach ($sekolah_binaan as $s): ?>
                                <span class="binaan-item">
                                    <?php echo $s['nama_sekolah']; ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="id_pengawas" value="<?php echo $id; ?>">
                                        <input type="hidden" name="id_sekolah" value="<?php echo $s['id']; ?>">
                                        <input type="hidden" name="action" value="hapus_binaan">
                                        <button type="submit" class="remove-btn" onclick="return confirm('Hapus sekolah binaan ini?')" title="Hapus">×</button>
                                    </form>
                                </span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span style="color:#888; font-size:13px;">Belum ada sekolah binaan.</span>
                        <?php endif; ?>
                    </div>

                    <!-- Form Tambah Sekolah Binaan -->
                    <form method="POST" class="binaan-form">
                        <input type="hidden" name="id_pengawas" value="<?php echo $id; ?>">
                        <input type="hidden" name="action" value="tambah_binaan">
                        
                        <select name="id_sekolah_binaan" required>
                            <option value="">-- Pilih Sekolah --</option>
                            <?php foreach ($sekolah_list as $s): ?>
                                <?php if (!in_array($s['id'], $sekolah_binaan_ids)): ?>
                                    <option value="<?php echo $s['id']; ?>">
                                        <?php echo $s['nama_sekolah']; ?> (<?php echo $s['npsn']; ?>)
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        
                        <button type="submit" class="btn btn-success btn-sm">➕ Tambah Sekolah</button>
                    </form>

                    <!-- Tombol Hapus Semua -->
                    <?php if (count($sekolah_binaan) > 0): ?>
                        <form method="POST" style="margin-top:10px;">
                            <input type="hidden" name="id_pengawas" value="<?php echo $id; ?>">
                            <input type="hidden" name="action" value="hapus_semua_binaan">
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus SEMUA sekolah binaan?')">
                                🗑️ Hapus Semua
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- ========================================== -->
            <!-- RESET PASSWORD -->
            <!-- ========================================== -->
            <div class="password-section">
                <h4>🔑 Reset Password</h4>
                <p style="font-size:13px; color:#856404; margin-bottom:15px;">Reset password user ini ke password baru</p>
                
                <form method="POST" onsubmit="return confirm('Reset password user ini?')">
                    <input type="hidden" name="action" value="reset_password">
                    <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                        <input type="text" name="new_password" required placeholder="Password baru" style="flex:1; min-width:200px; padding:10px 14px; border:2px solid #e8ecf1; border-radius:8px; font-size:14px;">
                        <button type="submit" class="btn btn-warning">🔑 Reset Password</button>
                    </div>
                    <div class="help-text" style="margin-top:5px;">Minimal 6 karakter</div>
                </form>
            </div>

            <!-- ========================================== -->
            <!-- HAPUS USER -->
            <!-- ========================================== -->
            <div class="delete-section">
                <h4>⚠️ Hapus User</h4>
                <p style="font-size:13px; color:#721c24; margin-bottom:15px;">
                    Hapus user ini dari database. Tindakan ini tidak dapat dibatalkan!
                </p>
                <a href="admin_delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-danger" onclick="return confirm('Hapus user ini? Tindakan ini tidak dapat dibatalkan!')">
                    🗑️ Hapus User
                </a>
            </div>
        </main>

        <footer style="text-align:center; margin-top:30px; color:#888; font-size:13px; border-top:1px solid #e0e0e0; padding-top:20px;">
            <p>&copy; 2026 Pusat Perangkat Pembelajaran - Admin Panel</p>
        </footer>
    </div>
</body>
</html>