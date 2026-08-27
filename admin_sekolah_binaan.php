<?php
// admin_sekolah.php - Kelola Sekolah dan Sekolah Binaan Pengawas
session_start();

// Cek login dan role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

include_once 'config/database.php';

// Ambil semua sekolah
$sekolah = fetchAll("
    SELECT s.*, u.name as kepala_sekolah_name, 
           (SELECT name FROM users WHERE role = 'pengawas' AND sekolah_id = s.id LIMIT 1) as pengawas_name
    FROM sekolah s
    LEFT JOIN users u ON s.kepala_sekolah = u.id
    ORDER BY s.nama_sekolah
");

// Ambil semua pengawas untuk dropdown
$pengawas_list = fetchAll("
    SELECT id, name, sekolah_id 
    FROM users 
    WHERE role = 'pengawas' 
    ORDER BY name
");

// Ambil semua sekolah untuk dropdown
$sekolah_list = fetchAll("SELECT * FROM sekolah ORDER BY nama_sekolah");

// Proses tambah sekolah binaan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $id_pengawas = (int)$_POST['id_pengawas'];
    $id_sekolah = (int)$_POST['id_sekolah'];
    
    if ($_POST['action'] == 'tambah_binaan') {
        // Update sekolah_id pengawas
        $sql = "UPDATE users SET sekolah_id = $id_sekolah WHERE id = $id_pengawas";
        if (query($sql)) {
            $success = "✅ Sekolah binaan berhasil ditambahkan!";
        } else {
            $error = "❌ Gagal menambahkan sekolah binaan!";
        }
    } elseif ($_POST['action'] == 'hapus_binaan') {
        $sql = "UPDATE users SET sekolah_id = NULL WHERE id = $id_pengawas";
        if (query($sql)) {
            $success = "✅ Sekolah binaan berhasil dihapus!";
        } else {
            $error = "❌ Gagal menghapus sekolah binaan!";
        }
    }
    
    // Refresh data
    $sekolah = fetchAll("
        SELECT s.*, u.name as kepala_sekolah_name, 
               (SELECT name FROM users WHERE role = 'pengawas' AND sekolah_id = s.id LIMIT 1) as pengawas_name
        FROM sekolah s
        LEFT JOIN users u ON s.kepala_sekolah = u.id
        ORDER BY s.nama_sekolah
    ");
}

// Proses tambah sekolah baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah_sekolah') {
    $nama_sekolah = mysqli_real_escape_string($conn, $_POST['nama_sekolah']);
    $npsn = mysqli_real_escape_string($conn, $_POST['npsn']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $kelurahan = mysqli_real_escape_string($conn, $_POST['kelurahan']);
    $kecamatan = mysqli_real_escape_string($conn, $_POST['kecamatan']);
    $kabupaten = mysqli_real_escape_string($conn, $_POST['kabupaten']);
    $provinsi = mysqli_real_escape_string($conn, $_POST['provinsi']);
    $kepala_sekolah = !empty($_POST['kepala_sekolah']) ? (int)$_POST['kepala_sekolah'] : 'NULL';
    
    // Cek NPSN duplikat
    $cek = fetchOne("SELECT id FROM sekolah WHERE npsn = '$npsn'");
    if ($cek) {
        $error = "❌ NPSN sudah terdaftar!";
    } else {
        $sql = "INSERT INTO sekolah (nama_sekolah, npsn, alamat, kelurahan, kecamatan, kabupaten, provinsi, kepala_sekolah) 
                VALUES ('$nama_sekolah', '$npsn', '$alamat', '$kelurahan', '$kecamatan', '$kabupaten', '$provinsi', $kepala_sekolah)";
        
        if (query($sql)) {
            $success = "✅ Sekolah <strong>$nama_sekolah</strong> berhasil ditambahkan!";
            // Refresh data
            $sekolah = fetchAll("
                SELECT s.*, u.name as kepala_sekolah_name, 
                       (SELECT name FROM users WHERE role = 'pengawas' AND sekolah_id = s.id LIMIT 1) as pengawas_name
                FROM sekolah s
                LEFT JOIN users u ON s.kepala_sekolah = u.id
                ORDER BY s.nama_sekolah
            ");
        } else {
            $error = "❌ Gagal menambahkan sekolah: " . mysqli_error($conn);
        }
    }
}

// Ambil daftar user untuk dropdown kepala sekolah
$user_list = fetchAll("SELECT id, name, role FROM users WHERE role IN ('kepala_sekolah', 'guru') ORDER BY name");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Sekolah - Admin</title>
    <link rel="stylesheet" href="/website_perangkat/public/css/style.css">
    <style>
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #28a745 0%, #1a7a3a 100%); color: white; padding: 20px 25px; border-radius: 12px; margin-bottom: 20px; }
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
            color: #28a745;
        }
        .stats-grid .stat-card .label { font-size: 13px; color: #666; }
        
        .btn-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .btn {
            padding: 10px 25px;
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
        .btn-primary { background: #0d47a1; color: white; }
        .btn-primary:hover { background: #0a3a7a; }
        .btn-success { background: #28a745; color: white; }
        .btn-success:hover { background: #218838; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-danger:hover { background: #c82333; }
        .btn-warning { background: #ffc107; color: #000; }
        .btn-warning:hover { background: #e0a800; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #5a6268; }
        
        .table-container {
            overflow-x: auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #28a745;
            color: white;
        }
        tr:hover { background: #f5f5f5; }
        
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
        .modal-overlay.active {
            display: flex;
        }
        .modal-content {
            background: white;
            border-radius: 15px;
            padding: 35px;
            max-width: 600px;
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
        .modal-content .modal-header h3 {
            margin: 0;
            color: #333;
            font-size: 1.3em;
        }
        .modal-content .modal-header .close-btn {
            background: none;
            border: none;
            font-size: 28px;
            color: #aaa;
            cursor: pointer;
        }
        .modal-content .modal-header .close-btn:hover { color: #333; }
        .modal-content .form-group {
            margin-bottom: 18px;
        }
        .modal-content .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            color: #555;
            font-size: 14px;
        }
        .modal-content .form-group input, 
        .modal-content .form-group select,
        .modal-content .form-group textarea {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid #e8ecf1;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        .modal-content .form-group input:focus,
        .modal-content .form-group select:focus,
        .modal-content .form-group textarea:focus {
            border-color: #28a745;
            outline: none;
        }
        .modal-content .form-group textarea {
            resize: vertical;
            min-height: 60px;
        }
        .modal-content .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #28a745 0%, #1a7a3a 100%);
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
        
        .form-inline {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        .form-inline select {
            padding: 6px 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 13px;
            min-width: 200px;
        }
        .form-inline select:focus {
            border-color: #0d47a1;
            outline: none;
        }
        
        .sekolah-badge {
            display: inline-block;
            padding: 2px 10px;
            background: #e8f0fe;
            color: #0d47a1;
            border-radius: 12px;
            font-size: 12px;
            margin: 2px 4px 2px 0;
        }
        
        .empty-state {
            text-align: center;
            padding: 30px;
            color: #888;
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
            background: #28a745;
            color: white;
            padding: 2px 14px;
            border-radius: 20px;
            font-size: 12px;
        }
        
        @media (max-width: 768px) {
            .form-inline {
                flex-direction: column;
                align-items: stretch;
            }
            .form-inline select {
                min-width: 100%;
            }
            .modal-content { padding: 25px 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏫 Kelola Sekolah & Sekolah Binaan</h1>
            <p>Kelola data sekolah dan tetapkan sekolah binaan untuk pengawas</p>
        </div>

         <!-- ===== NAVIGASI PAKAI NAVBAR.PHP ===== -->
        <?php include_once 'navbar.php'; ?>
        <!-- ===================================== -->

        <main>
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Statistik -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="number"><?php echo count($sekolah); ?></div>
                    <div class="label">🏫 Total Sekolah</div>
                </div>
                <div class="stat-card">
                    <div class="number">
                        <?php 
                        $total_pengawas = count($pengawas_list);
                        echo $total_pengawas;
                        ?>
                    </div>
                    <div class="label">🔍 Total Pengawas</div>
                </div>
                <div class="stat-card">
                    <div class="number">
                        <?php 
                        $total_binaan = 0;
                        foreach ($pengawas_list as $p) {
                            if ($p['sekolah_id']) $total_binaan++;
                        }
                        echo $total_binaan;
                        ?>
                    </div>
                    <div class="label">📋 Sudah Ada Binaan</div>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="btn-group">
                <button onclick="openModal()" class="btn btn-success">➕ Tambah Sekolah</button>
                <a href="admin_sekolah_binaan.php" class="btn btn-primary">🏫 Kelola Sekolah Binaan</a>
            </div>

            <!-- Daftar Sekolah -->
            <div class="section-title">
                <span>🏫 Daftar Sekolah <span class="badge-count"><?php echo count($sekolah); ?></span></span>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Sekolah</th>
                            <th>NPSN</th>
                            <th>Kecamatan</th>
                            <th>Kepala Sekolah</th>
                            <th>Pengawas Binaan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($sekolah) > 0): ?>
                            <?php $no = 1; foreach ($sekolah as $s): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><strong><?php echo $s['nama_sekolah']; ?></strong></td>
                                <td><?php echo $s['npsn']; ?></td>
                                <td><?php echo $s['kecamatan']; ?></td>
                                <td><?php echo $s['kepala_sekolah_name'] ?? '-'; ?></td>
                                <td>
                                    <?php 
                                    // Cari pengawas untuk sekolah ini
                                    $pengawas = fetchOne("SELECT name FROM users WHERE role = 'pengawas' AND sekolah_id = " . $s['id'] . " LIMIT 1");
                                    echo $pengawas ? $pengawas['name'] : '<span style="color:#888;">Belum ada</span>';
                                    ?>
                                </td>
                                <td>
                                    <a href="admin_edit_sekolah.php?id=<?php echo $s['id']; ?>" class="btn btn-warning" style="padding:4px 12px; font-size:11px; text-decoration:none;">✏️</a>
                                    <a href="admin_delete_sekolah.php?id=<?php echo $s['id']; ?>" class="btn btn-danger" style="padding:4px 12px; font-size:11px; text-decoration:none;" onclick="return confirm('Hapus sekolah ini?')">🗑️</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="empty-state">Belum ada data sekolah.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Daftar Pengawas & Sekolah Binaan -->
            <div class="section-title">
                <span>🔍 Pengawas & Sekolah Binaan <span class="badge-count"><?php echo count($pengawas_list); ?></span></span>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pengawas</th>
                            <th>NIP</th>
                            <th>Jenjang</th>
                            <th>Sekolah Binaan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($pengawas_list) > 0): ?>
                            <?php $no = 1; foreach ($pengawas_list as $p): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><strong><?php echo $p['name']; ?></strong></td>
                                <td><?php echo $p['nip'] ?? '-'; ?></td>
                                <td>
                                    <?php 
                                    if (strpos($p['sekolah'] ?? '', 'PAUD') !== false) {
                                        echo 'PAUD';
                                    } else {
                                        echo 'DIKDAS';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if ($p['sekolah_id']): ?>
                                        <?php 
                                        $sekolah_binaan = fetchOne("SELECT nama_sekolah FROM sekolah WHERE id = " . $p['sekolah_id']);
                                        echo '<span class="sekolah-badge">' . ($sekolah_binaan ? $sekolah_binaan['nama_sekolah'] : 'Sekolah tidak ditemukan') . '</span>';
                                        ?>
                                    <?php else: ?>
                                        <span style="color:#888;">Belum ditentukan</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="POST" class="form-inline" style="display:flex; gap:5px; flex-wrap:wrap;">
                                        <input type="hidden" name="id_pengawas" value="<?php echo $p['id']; ?>">
                                        
                                        <select name="id_sekolah" required>
                                            <option value="">-- Pilih Sekolah --</option>
                                            <?php foreach ($sekolah_list as $s): ?>
                                                <option value="<?php echo $s['id']; ?>" <?php echo ($p['sekolah_id'] == $s['id']) ? 'selected' : ''; ?>>
                                                    <?php echo $s['nama_sekolah']; ?> (<?php echo $s['npsn']; ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        
                                        <button type="submit" name="action" value="tambah_binaan" class="btn btn-success" style="padding:4px 12px; font-size:12px;">
                                            ✅ Tetapkan
                                        </button>
                                        
                                        <?php if ($p['sekolah_id']): ?>
                                            <button type="submit" name="action" value="hapus_binaan" class="btn btn-danger" style="padding:4px 12px; font-size:12px;" onclick="return confirm('Hapus sekolah binaan ini?')">
                                                🗑️ Hapus
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="empty-state">Belum ada data pengawas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Informasi -->
            <div style="margin-top:20px; padding:15px; background:#e8f0fe; border-radius:8px; border-left:4px solid #28a745;">
                <h4 style="margin:0 0 8px 0; color:#28a745;">📌 Informasi</h4>
                <p style="margin:4px 0; font-size:13px; color:#555;">
                    <strong>PAUD:</strong> Ahad Abdullah, S.Pd.AUD
                </p>
                <p style="margin:4px 0; font-size:13px; color:#555;">
                    <strong>DIKDAS:</strong> Donatus Tato, Vitalianus Pio, Getrudis Toja, Seto Agustinus, 
                    Maria Magdalena Tea, Muhammad Rusmin, Eni Sulistiowati, Yosefina Fransiska Dhana,
                    Bernadus Gae Longa, Arnolda Yuliana Weti, Maria Imakulata Bule, Antonius Singga,
                    Martinianus Pegu, Maria Skolastika Muni
                </p>
            </div>
        </main>

        <footer style="text-align:center; margin-top:30px; color:#888; font-size:13px; border-top:1px solid #e0e0e0; padding-top:20px;">
            <p>&copy; 2026 Pusat Perangkat Pembelajaran - Admin Panel</p>
        </footer>
    </div>

    <!-- ===== MODAL TAMBAH SEKOLAH ===== -->
    <div class="modal-overlay" id="modalTambahSekolah">
        <div class="modal-content">
            <div class="modal-header">
                <h3>➕ Tambah Sekolah Baru</h3>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="tambah_sekolah">
                
                <div class="form-group">
                    <label>🏫 Nama Sekolah *</label>
                    <input type="text" name="nama_sekolah" required placeholder="Contoh: SMA Negeri 1 Ende">
                </div>
                
                <div class="form-group">
                    <label>🆔 NPSN *</label>
                    <input type="text" name="npsn" required placeholder="Nomor Pokok Sekolah Nasional">
                </div>
                
                <div class="form-group">
                    <label>📍 Alamat</label>
                    <textarea name="alamat" placeholder="Alamat lengkap sekolah"></textarea>
                </div>
                
                <div class="form-group">
                    <label>🏘️ Kelurahan</label>
                    <input type="text" name="kelurahan" placeholder="Kelurahan/Desa">
                </div>
                
                <div class="form-group">
                    <label>🏛️ Kecamatan</label>
                    <input type="text" name="kecamatan" placeholder="Kecamatan">
                </div>
                
                <div class="form-group">
                    <label>🏛️ Kabupaten</label>
                    <input type="text" name="kabupaten" value="Kabupaten Ende">
                </div>
                
                <div class="form-group">
                    <label>🌏 Provinsi</label>
                    <input type="text" name="provinsi" value="Nusa Tenggara Timur">
                </div>
                
                <div class="form-group">
                    <label>👔 Kepala Sekolah (Opsional)</label>
                    <select name="kepala_sekolah">
                        <option value="">-- Pilih Kepala Sekolah --</option>
                        <?php foreach ($user_list as $u): ?>
                            <option value="<?php echo $u['id']; ?>">
                                <?php echo $u['name']; ?> (<?php echo $u['role']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="btn-submit">➕ Tambah Sekolah</button>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('modalTambahSekolah').classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function closeModal() {
            document.getElementById('modalTambahSekolah').classList.remove('active');
            document.body.style.overflow = 'auto';
        }
        
        document.getElementById('modalTambahSekolah').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html>