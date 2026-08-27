<?php
// admin_mapel_kelas.php - Kelola Mata Pelajaran dan Kelas
session_start();

// Cek login dan role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

include_once 'config/database.php';

// ============================================
// PROSES MATA PELAJARAN
// ============================================

// Tambah Mapel
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah_mapel') {
    $nama_mapel = mysqli_real_escape_string($conn, $_POST['nama_mapel']);
    $kode_mapel = mysqli_real_escape_string($conn, $_POST['kode_mapel']);
    
    $cek = fetchOne("SELECT id FROM mata_pelajaran WHERE kode_mapel = '$kode_mapel'");
    if ($cek) {
        $error_mapel = "❌ Kode mapel sudah terdaftar!";
    } else {
        $sql = "INSERT INTO mata_pelajaran (nama_mapel, kode_mapel) VALUES ('$nama_mapel', '$kode_mapel')";
        if (query($sql)) {
            $success_mapel = "✅ Mata pelajaran berhasil ditambahkan!";
        } else {
            $error_mapel = "❌ Gagal menambahkan: " . mysqli_error($conn);
        }
    }
}

// Edit Mapel
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit_mapel') {
    $id = (int)$_POST['id'];
    $nama_mapel = mysqli_real_escape_string($conn, $_POST['nama_mapel']);
    $kode_mapel = mysqli_real_escape_string($conn, $_POST['kode_mapel']);
    
    $cek = fetchOne("SELECT id FROM mata_pelajaran WHERE kode_mapel = '$kode_mapel' AND id != $id");
    if ($cek) {
        $error_mapel = "❌ Kode mapel sudah terdaftar!";
    } else {
        $sql = "UPDATE mata_pelajaran SET nama_mapel = '$nama_mapel', kode_mapel = '$kode_mapel' WHERE id = $id";
        if (query($sql)) {
            $success_mapel = "✅ Mata pelajaran berhasil diperbarui!";
        } else {
            $error_mapel = "❌ Gagal memperbarui: " . mysqli_error($conn);
        }
    }
}

// Hapus Mapel
if (isset($_GET['hapus_mapel'])) {
    $id = (int)$_GET['hapus_mapel'];
    // Cek apakah mapel digunakan di perangkat
    $cek = fetchOne("SELECT id FROM perangkat WHERE id_mapel = $id LIMIT 1");
    if ($cek) {
        $error_mapel = "❌ Mata pelajaran tidak dapat dihapus karena masih digunakan!";
    } else {
        query("DELETE FROM mata_pelajaran WHERE id = $id");
        $success_mapel = "✅ Mata pelajaran berhasil dihapus!";
    }
}

// ============================================
// PROSES KELAS
// ============================================

// Tambah Kelas
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah_kelas') {
    $nama_kelas = mysqli_real_escape_string($conn, $_POST['nama_kelas']);
    $jenjang = mysqli_real_escape_string($conn, $_POST['jenjang']);
    
    $cek = fetchOne("SELECT id FROM kelas WHERE nama_kelas = '$nama_kelas' AND jenjang = '$jenjang'");
    if ($cek) {
        $error_kelas = "❌ Kelas sudah terdaftar!";
    } else {
        $sql = "INSERT INTO kelas (nama_kelas, jenjang) VALUES ('$nama_kelas', '$jenjang')";
        if (query($sql)) {
            $success_kelas = "✅ Kelas berhasil ditambahkan!";
        } else {
            $error_kelas = "❌ Gagal menambahkan: " . mysqli_error($conn);
        }
    }
}

// Edit Kelas
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit_kelas') {
    $id = (int)$_POST['id'];
    $nama_kelas = mysqli_real_escape_string($conn, $_POST['nama_kelas']);
    $jenjang = mysqli_real_escape_string($conn, $_POST['jenjang']);
    
    $cek = fetchOne("SELECT id FROM kelas WHERE nama_kelas = '$nama_kelas' AND jenjang = '$jenjang' AND id != $id");
    if ($cek) {
        $error_kelas = "❌ Kelas sudah terdaftar!";
    } else {
        $sql = "UPDATE kelas SET nama_kelas = '$nama_kelas', jenjang = '$jenjang' WHERE id = $id";
        if (query($sql)) {
            $success_kelas = "✅ Kelas berhasil diperbarui!";
        } else {
            $error_kelas = "❌ Gagal memperbarui: " . mysqli_error($conn);
        }
    }
}

// Hapus Kelas
if (isset($_GET['hapus_kelas'])) {
    $id = (int)$_GET['hapus_kelas'];
    // Cek apakah kelas digunakan di perangkat
    $cek = fetchOne("SELECT id FROM perangkat WHERE id_kelas = $id LIMIT 1");
    if ($cek) {
        $error_kelas = "❌ Kelas tidak dapat dihapus karena masih digunakan!";
    } else {
        query("DELETE FROM kelas WHERE id = $id");
        $success_kelas = "✅ Kelas berhasil dihapus!";
    }
}

// ============================================
// AMBIL DATA
// ============================================

$mapel = fetchAll("SELECT * FROM mata_pelajaran ORDER BY kode_mapel");
$kelas = fetchAll("SELECT * FROM kelas ORDER BY jenjang, CAST(nama_kelas AS UNSIGNED)");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Mapel & Kelas - Admin</title>
    <link rel="stylesheet" href="/website_perangkat/public/css/style.css">
    <style>
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #17a2b8 0%, #0d6efd 100%); color: white; padding: 20px 25px; border-radius: 12px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 1.5em; }
        .header p { margin: 5px 0 0 0; opacity: 0.9; }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .stats-grid .stat-card {
            background: white;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .stats-grid .stat-card .number {
            font-size: 2em;
            font-weight: bold;
            color: #17a2b8;
        }
        .stats-grid .stat-card .label { font-size: 13px; color: #666; }
        
        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .section-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
            padding: 25px;
        }
        .section-card .section-title {
            font-size: 1.2em;
            margin: 0 0 15px 0;
            color: #333;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f4f8;
        }
        .section-card .section-title .badge-count {
            background: #17a2b8;
            color: white;
            padding: 2px 14px;
            border-radius: 20px;
            font-size: 12px;
        }
        
        .form-inline {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }
        .form-inline input, .form-inline select {
            padding: 8px 12px;
            border: 2px solid #e8ecf1;
            border-radius: 8px;
            font-size: 13px;
            flex: 1;
            min-width: 150px;
        }
        .form-inline input:focus, .form-inline select:focus {
            border-color: #17a2b8;
            outline: none;
        }
        
        .btn {
            padding: 8px 20px;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .btn:hover { transform: scale(1.02); }
        .btn-success { background: #28a745; color: white; }
        .btn-success:hover { background: #218838; }
        .btn-primary { background: #17a2b8; color: white; }
        .btn-primary:hover { background: #138496; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-danger:hover { background: #c82333; }
        .btn-warning { background: #ffc107; color: #000; }
        .btn-warning:hover { background: #e0a800; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #5a6268; }
        .btn-sm { padding: 4px 12px; font-size: 12px; }
        
        .table-container {
            overflow-x: auto;
            margin-top: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background: #f8f9fa;
            color: #333;
            font-weight: 600;
        }
        tr:hover { background: #f8f9fa; }
        
        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 14px;
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
        
        .edit-form {
            display: none;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
        }
        .edit-form.active {
            display: block;
        }
        .edit-form .form-inline {
            margin-bottom: 0;
        }
        
        .empty-state {
            text-align: center;
            padding: 20px;
            color: #888;
        }
        
        @media (max-width: 768px) {
            .two-col {
                grid-template-columns: 1fr;
            }
            .form-inline {
                flex-direction: column;
            }
            .form-inline input, .form-inline select {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📚 Kelola Mata Pelajaran & Kelas</h1>
            <p>Kelola data master mata pelajaran dan kelas</p>
        </div>

         <!-- ===== NAVIGASI PAKAI NAVBAR.PHP ===== -->
        <?php include_once 'navbar.php'; ?>
        <!-- ===================================== -->

        <main>
            <!-- Statistik -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="number"><?php echo count($mapel); ?></div>
                    <div class="label">📚 Mata Pelajaran</div>
                </div>
                <div class="stat-card">
                    <div class="number"><?php echo count($kelas); ?></div>
                    <div class="label">🏫 Kelas</div>
                </div>
            </div>

            <!-- Dua Kolom -->
            <div class="two-col">
                <!-- ========================================== -->
                <!-- MATA PELAJARAN -->
                <!-- ========================================== -->
                <div class="section-card">
                    <div class="section-title">
                        <span>📚 Mata Pelajaran <span class="badge-count"><?php echo count($mapel); ?></span></span>
                    </div>

                    <?php if (isset($success_mapel)): ?>
                        <div class="alert alert-success"><?php echo $success_mapel; ?></div>
                    <?php endif; ?>
                    <?php if (isset($error_mapel)): ?>
                        <div class="alert alert-error"><?php echo $error_mapel; ?></div>
                    <?php endif; ?>

                    <!-- Form Tambah Mapel -->
                    <form method="POST" class="form-inline">
                        <input type="hidden" name="action" value="tambah_mapel">
                        <input type="text" name="kode_mapel" placeholder="Kode (contoh: MTK)" required style="max-width:120px;">
                        <input type="text" name="nama_mapel" placeholder="Nama Mata Pelajaran" required>
                        <button type="submit" class="btn btn-success">➕ Tambah</button>
                    </form>

                    <!-- Daftar Mapel -->
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Mapel</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($mapel) > 0): ?>
                                    <?php foreach ($mapel as $m): ?>
                                    <tr>
                                        <td><strong><?php echo $m['kode_mapel']; ?></strong></td>
                                        <td><?php echo $m['nama_mapel']; ?></td>
                                        <td>
                                            <button onclick="toggleEditMapel(<?php echo $m['id']; ?>)" class="btn btn-warning btn-sm">✏️</button>
                                            <a href="?hapus_mapel=<?php echo $m['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus mata pelajaran ini?')">🗑️</a>
                                        </td>
                                    </tr>
                                    <tr id="edit-mapel-<?php echo $m['id']; ?>" style="display:none;">
                                        <td colspan="3">
                                            <form method="POST" class="form-inline" style="background:#f8f9fa; padding:10px; border-radius:8px;">
                                                <input type="hidden" name="action" value="edit_mapel">
                                                <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
                                                <input type="text" name="kode_mapel" value="<?php echo $m['kode_mapel']; ?>" style="max-width:120px;" required>
                                                <input type="text" name="nama_mapel" value="<?php echo $m['nama_mapel']; ?>" required>
                                                <button type="submit" class="btn btn-primary btn-sm">💾 Simpan</button>
                                                <button type="button" class="btn btn-secondary btn-sm" onclick="toggleEditMapel(<?php echo $m['id']; ?>)">❌ Batal</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="empty-state">Belum ada data mata pelajaran.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- KELAS -->
                <!-- ========================================== -->
                <div class="section-card">
                    <div class="section-title">
                        <span>🏫 Kelas <span class="badge-count"><?php echo count($kelas); ?></span></span>
                    </div>

                    <?php if (isset($success_kelas)): ?>
                        <div class="alert alert-success"><?php echo $success_kelas; ?></div>
                    <?php endif; ?>
                    <?php if (isset($error_kelas)): ?>
                        <div class="alert alert-error"><?php echo $error_kelas; ?></div>
                    <?php endif; ?>

                    <!-- Form Tambah Kelas -->
                    <form method="POST" class="form-inline">
                        <input type="hidden" name="action" value="tambah_kelas">
                        <input type="text" name="nama_kelas" placeholder="Kelas (contoh: 10)" required style="max-width:100px;">
                        <select name="jenjang" required>
                            <option value="">-- Jenjang --</option>
                            <option value="SD">SD</option>
                            <option value="SMP">SMP</option>
                            <option value="SMA">SMA</option>
                            <option value="SMK">SMK</option>
                        </select>
                        <button type="submit" class="btn btn-success">➕ Tambah</button>
                    </form>

                    <!-- Daftar Kelas -->
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Kelas</th>
                                    <th>Jenjang</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($kelas) > 0): ?>
                                    <?php foreach ($kelas as $k): ?>
                                    <tr>
                                        <td><strong><?php echo $k['nama_kelas']; ?></strong></td>
                                        <td><?php echo $k['jenjang']; ?></td>
                                        <td>
                                            <button onclick="toggleEditKelas(<?php echo $k['id']; ?>)" class="btn btn-warning btn-sm">✏️</button>
                                            <a href="?hapus_kelas=<?php echo $k['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus kelas ini?')">🗑️</a>
                                        </td>
                                    </tr>
                                    <tr id="edit-kelas-<?php echo $k['id']; ?>" style="display:none;">
                                        <td colspan="3">
                                            <form method="POST" class="form-inline" style="background:#f8f9fa; padding:10px; border-radius:8px;">
                                                <input type="hidden" name="action" value="edit_kelas">
                                                <input type="hidden" name="id" value="<?php echo $k['id']; ?>">
                                                <input type="text" name="nama_kelas" value="<?php echo $k['nama_kelas']; ?>" style="max-width:100px;" required>
                                                <select name="jenjang" required>
                                                    <option value="SD" <?php echo $k['jenjang'] == 'SD' ? 'selected' : ''; ?>>SD</option>
                                                    <option value="SMP" <?php echo $k['jenjang'] == 'SMP' ? 'selected' : ''; ?>>SMP</option>
                                                    <option value="SMA" <?php echo $k['jenjang'] == 'SMA' ? 'selected' : ''; ?>>SMA</option>
                                                    <option value="SMK" <?php echo $k['jenjang'] == 'SMK' ? 'selected' : ''; ?>>SMK</option>
                                                </select>
                                                <button type="submit" class="btn btn-primary btn-sm">💾 Simpan</button>
                                                <button type="button" class="btn btn-secondary btn-sm" onclick="toggleEditKelas(<?php echo $k['id']; ?>)">❌ Batal</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="empty-state">Belum ada data kelas.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>

        <footer style="text-align:center; margin-top:30px; color:#888; font-size:13px; border-top:1px solid #e0e0e0; padding-top:20px;">
            <p>&copy; 2026 Pusat Perangkat Pembelajaran - Admin Panel</p>
        </footer>
    </div>

    <script>
        // Toggle Edit Mapel
        function toggleEditMapel(id) {
            var row = document.getElementById('edit-mapel-' + id);
            if (row.style.display === 'none') {
                row.style.display = 'table-row';
            } else {
                row.style.display = 'none';
            }
        }
        
        // Toggle Edit Kelas
        function toggleEditKelas(id) {
            var row = document.getElementById('edit-kelas-' + id);
            if (row.style.display === 'none') {
                row.style.display = 'table-row';
            } else {
                row.style.display = 'none';
            }
        }
    </script>
</body>
</html>