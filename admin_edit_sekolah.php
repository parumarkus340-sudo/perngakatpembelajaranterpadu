<?php
// admin_edit_sekolah.php - Edit Data Sekolah
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
    header('Location: admin_sekolah.php');
    exit;
}

// Ambil data sekolah
$sekolah = fetchOne("SELECT * FROM sekolah WHERE id = $id");
if (!$sekolah) {
    header('Location: admin_sekolah.php');
    exit;
}

// Ambil daftar user untuk dropdown kepala sekolah
$user_list = fetchAll("SELECT id, name, role FROM users WHERE role IN ('kepala_sekolah', 'guru') ORDER BY name");

// Proses update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $nama_sekolah = mysqli_real_escape_string($conn, $_POST['nama_sekolah']);
    $npsn = mysqli_real_escape_string($conn, $_POST['npsn']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $kelurahan = mysqli_real_escape_string($conn, $_POST['kelurahan']);
    $kecamatan = mysqli_real_escape_string($conn, $_POST['kecamatan']);
    $kabupaten = mysqli_real_escape_string($conn, $_POST['kabupaten']);
    $provinsi = mysqli_real_escape_string($conn, $_POST['provinsi']);
    $kode_pos = mysqli_real_escape_string($conn, $_POST['kode_pos']);
    $kepala_sekolah = !empty($_POST['kepala_sekolah']) ? (int)$_POST['kepala_sekolah'] : 'NULL';
    
    // Cek NPSN duplikat (kecuali ID sendiri)
    $cek = fetchOne("SELECT id FROM sekolah WHERE npsn = '$npsn' AND id != $id");
    if ($cek) {
        $error = "❌ NPSN sudah digunakan oleh sekolah lain!";
    } else {
        $sql = "UPDATE sekolah SET 
                    nama_sekolah = '$nama_sekolah',
                    npsn = '$npsn',
                    alamat = '$alamat',
                    kelurahan = '$kelurahan',
                    kecamatan = '$kecamatan',
                    kabupaten = '$kabupaten',
                    provinsi = '$provinsi',
                    kode_pos = '$kode_pos',
                    kepala_sekolah = $kepala_sekolah
                WHERE id = $id";
        
        if (query($sql)) {
            $success = "✅ Data sekolah berhasil diperbarui!";
            // Refresh data
            $sekolah = fetchOne("SELECT * FROM sekolah WHERE id = $id");
        } else {
            $error = "❌ Gagal memperbarui data: " . mysqli_error($conn);
        }
    }
}

// Proses hapus sekolah
if (isset($_GET['delete']) && $_GET['delete'] == '1') {
    // Cek apakah sekolah memiliki relasi dengan user
    $cek_user = fetchOne("SELECT id FROM users WHERE sekolah_id = $id LIMIT 1");
    if ($cek_user) {
        $error = "❌ Sekolah tidak dapat dihapus karena masih terhubung dengan user!";
    } else {
        // Cek apakah sekolah memiliki perangkat
        $cek_perangkat = fetchOne("
            SELECT p.id FROM perangkat p 
            JOIN users u ON p.id_guru = u.id 
            WHERE u.sekolah_id = $id LIMIT 1
        ");
        if ($cek_perangkat) {
            $error = "❌ Sekolah tidak dapat dihapus karena masih memiliki perangkat!";
        } else {
            query("DELETE FROM sekolah WHERE id = $id");
            header('Location: admin_sekolah.php?success=Sekolah berhasil dihapus!');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Sekolah - Admin</title>
    <link rel="stylesheet" href="/website_perangkat/public/css/style.css">
    <style>
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #28a745 0%, #1a7a3a 100%); color: white; padding: 20px 25px; border-radius: 12px; margin-bottom: 20px; }
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
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #28a745;
            outline: none;
            background: white;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 60px;
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
        .btn-primary { background: #28a745; color: white; }
        .btn-primary:hover { background: #218838; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #5a6268; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-danger:hover { background: #c82333; }
        
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
        .alert-info {
            background: #cce5ff;
            color: #004085;
            border: 1px solid #b8daff;
        }
        
        .info-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }
        .info-box p { margin: 4px 0; font-size: 13px; color: #555; }
        
        .delete-section {
            background: #f8d7da;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #dc3545;
            margin-top: 20px;
        }
        .delete-section h4 { margin: 0 0 10px 0; color: #721c24; }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            .container { padding: 10px; }
            .form-container { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✏️ Edit Data Sekolah</h1>
            <p>Perbarui informasi sekolah</p>
        </div>

        <nav>
            <a href="/website_perangkat/index.php">🏠 Beranda</a>
            <a href="/website_perangkat/admin_dashboard.php">📊 Dashboard</a>
            <a href="/website_perangkat/admin_sekolah.php">🏫 Sekolah</a>
            <a href="/website_perangkat/logout.php">🚪 Logout</a>
        </nav>

        <main>
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Info Sekolah -->
            <div class="info-box">
                <p><strong>ID:</strong> <?php echo $sekolah['id']; ?></p>
                <p><strong>NPSN:</strong> <?php echo $sekolah['npsn']; ?></p>
                <p><strong>Tanggal Dibuat:</strong> <?php echo date('d/m/Y H:i', strtotime($sekolah['created_at'])); ?></p>
            </div>

            <!-- Form Edit Sekolah -->
            <div class="form-container">
                <form method="POST">
                    <input type="hidden" name="action" value="update">
                    
                    <div class="form-group">
                        <label>🏫 Nama Sekolah <span class="required">*</span></label>
                        <input type="text" name="nama_sekolah" required value="<?php echo htmlspecialchars($sekolah['nama_sekolah']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>🆔 NPSN <span class="required">*</span></label>
                        <input type="text" name="npsn" required value="<?php echo htmlspecialchars($sekolah['npsn']); ?>">
                        <div class="help-text">Nomor Pokok Sekolah Nasional</div>
                    </div>
                    
                    <div class="form-group">
                        <label>📍 Alamat</label>
                        <textarea name="alamat" placeholder="Alamat lengkap sekolah"><?php echo htmlspecialchars($sekolah['alamat'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>🏘️ Kelurahan</label>
                            <input type="text" name="kelurahan" value="<?php echo htmlspecialchars($sekolah['kelurahan'] ?? ''); ?>" placeholder="Kelurahan/Desa">
                        </div>
                        
                        <div class="form-group">
                            <label>🏛️ Kecamatan</label>
                            <input type="text" name="kecamatan" value="<?php echo htmlspecialchars($sekolah['kecamatan'] ?? ''); ?>" placeholder="Kecamatan">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>🏛️ Kabupaten</label>
                            <input type="text" name="kabupaten" value="<?php echo htmlspecialchars($sekolah['kabupaten'] ?? 'Kabupaten Ende'); ?>" placeholder="Kabupaten">
                        </div>
                        
                        <div class="form-group">
                            <label>🌏 Provinsi</label>
                            <input type="text" name="provinsi" value="<?php echo htmlspecialchars($sekolah['provinsi'] ?? 'Nusa Tenggara Timur'); ?>" placeholder="Provinsi">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>📮 Kode Pos</label>
                        <input type="text" name="kode_pos" value="<?php echo htmlspecialchars($sekolah['kode_pos'] ?? ''); ?>" placeholder="Kode Pos">
                    </div>
                    
                    <div class="form-group">
                        <label>👔 Kepala Sekolah</label>
                        <select name="kepala_sekolah">
                            <option value="">-- Pilih Kepala Sekolah --</option>
                            <?php foreach ($user_list as $u): ?>
                                <option value="<?php echo $u['id']; ?>" <?php echo ($sekolah['kepala_sekolah'] == $u['id']) ? 'selected' : ''; ?>>
                                    <?php echo $u['name']; ?> (<?php echo $u['role']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="help-text">Pilih user yang menjadi Kepala Sekolah</div>
                    </div>
                    
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
                        <a href="admin_sekolah.php" class="btn btn-secondary">🔙 Kembali</a>
                    </div>
                </form>
            </div>

            <!-- Hapus Sekolah -->
            <div class="delete-section">
                <h4>⚠️ Hapus Sekolah</h4>
                <p style="font-size:13px; color:#721c24; margin-bottom:15px;">
                    Hapus sekolah ini dari database. Tindakan ini tidak dapat dibatalkan!
                </p>
                <a href="?id=<?php echo $id; ?>&delete=1" class="btn btn-danger" onclick="return confirm('Hapus sekolah ini? Tindakan ini tidak dapat dibatalkan!')">
                    🗑️ Hapus Sekolah
                </a>
            </div>
        </main>

        <footer style="text-align:center; margin-top:30px; color:#888; font-size:13px; border-top:1px solid #e0e0e0; padding-top:20px;">
            <p>&copy; 2026 Pusat Perangkat Pembelajaran - Admin Panel</p>
        </footer>
    </div>
</body>
</html>