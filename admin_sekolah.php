<?php
// admin_sekolah.php - Kelola Sekolah dan Sekolah Binaan (Multi)
session_start();

// Cek login dan role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

include_once 'config/database.php';

// ============================================
// PROSES TAMBAH SEKOLAH
// ============================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah_sekolah') {
    $nama_sekolah = mysqli_real_escape_string($conn, $_POST['nama_sekolah']);
    $npsn = mysqli_real_escape_string($conn, $_POST['npsn']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $kelurahan = mysqli_real_escape_string($conn, $_POST['kelurahan']);
    $kecamatan = mysqli_real_escape_string($conn, $_POST['kecamatan']);
    $kabupaten = mysqli_real_escape_string($conn, $_POST['kabupaten']);
    $provinsi = mysqli_real_escape_string($conn, $_POST['provinsi']);
    
    $cek = fetchOne("SELECT id FROM sekolah WHERE npsn = '$npsn'");
    if ($cek) {
        $error = "❌ NPSN sudah terdaftar!";
    } else {
        $sql = "INSERT INTO sekolah (nama_sekolah, npsn, alamat, kelurahan, kecamatan, kabupaten, provinsi) 
                VALUES ('$nama_sekolah', '$npsn', '$alamat', '$kelurahan', '$kecamatan', '$kabupaten', '$provinsi')";
        
        if (query($sql)) {
            $success = "✅ Sekolah <strong>$nama_sekolah</strong> berhasil ditambahkan!";
        } else {
            $error = "❌ Gagal menambahkan sekolah: " . mysqli_error($conn);
        }
    }
}

// ============================================
// PROSES TETAPKAN SEKOLAH BINAAN (MULTI)
// ============================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tetapkan_binaan') {
    $id_pengawas = (int)$_POST['id_pengawas'];
    $id_sekolah = (int)$_POST['id_sekolah'];
    
    if ($id_sekolah > 0) {
        // Cek apakah sudah ada
        $cek = fetchOne("SELECT id FROM pengawas_sekolah WHERE pengawas_id = $id_pengawas AND sekolah_id = $id_sekolah");
        if ($cek) {
            $error = "❌ Sekolah sudah terdaftar sebagai binaan!";
        } else {
            $sql = "INSERT INTO pengawas_sekolah (pengawas_id, sekolah_id) VALUES ($id_pengawas, $id_sekolah)";
            if (query($sql)) {
                $success = "✅ Sekolah binaan berhasil ditetapkan!";
            } else {
                $error = "❌ Gagal menetapkan sekolah binaan: " . mysqli_error($conn);
            }
        }
    } else {
        $error = "❌ Pilih sekolah terlebih dahulu!";
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
        $success = "✅ Sekolah binaan berhasil dihapus!";
    } else {
        $error = "❌ Gagal menghapus sekolah binaan!";
    }
}

// ============================================
// PROSES HAPUS SEMUA BINAAN
// ============================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'hapus_semua_binaan') {
    $id_pengawas = (int)$_POST['id_pengawas'];
    
    $sql = "DELETE FROM pengawas_sekolah WHERE pengawas_id = $id_pengawas";
    if (query($sql)) {
        $success = "✅ Semua sekolah binaan berhasil dihapus!";
    } else {
        $error = "❌ Gagal menghapus sekolah binaan!";
    }
}

// ============================================
// AMBIL DATA
// ============================================

// Ambil semua sekolah
$sekolah = fetchAll("
    SELECT s.*, u.name as kepala_sekolah_name 
    FROM sekolah s
    LEFT JOIN users u ON s.kepala_sekolah = u.id
    ORDER BY s.nama_sekolah
");

// Ambil semua pengawas dengan sekolah binaan (dari tabel relasi)
$pengawas_list = fetchAll("
    SELECT u.*, 
           GROUP_CONCAT(DISTINCT s.nama_sekolah ORDER BY s.nama_sekolah SEPARATOR ', ') as sekolah_binaan_nama,
           GROUP_CONCAT(DISTINCT s.id ORDER BY s.nama_sekolah SEPARATOR ',') as sekolah_binaan_ids,
           COUNT(DISTINCT s.id) as total_binaan
    FROM users u
    LEFT JOIN pengawas_sekolah ps ON u.id = ps.pengawas_id
    LEFT JOIN sekolah s ON ps.sekolah_id = s.id
    WHERE u.role = 'pengawas'
    GROUP BY u.id
    ORDER BY u.name
");

// Ambil semua sekolah untuk dropdown
$sekolah_list = fetchAll("SELECT * FROM sekolah ORDER BY nama_sekolah");

// Statistik
$total_sekolah = count($sekolah);
$total_pengawas = count($pengawas_list);
$total_binaan = 0;
foreach ($pengawas_list as $p) {
    if ($p['sekolah_binaan_ids']) {
        $total_binaan += count(explode(',', $p['sekolah_binaan_ids']));
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Sekolah & Binaan - Admin</title>
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
        .btn-success { background: #28a745; color: white; }
        .btn-success:hover { background: #218838; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-danger:hover { background: #c82333; }
        .btn-warning { background: #ffc107; color: #000; }
        .btn-warning:hover { background: #e0a800; }
        .btn-sm { padding: 4px 12px; font-size: 12px; border-radius: 5px; }
        .btn-primary { background: #0d47a1; color: white; }
        .btn-primary:hover { background: #0a3a7a; }
        
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
        
        .form-inline {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }
        .form-inline select {
            padding: 5px 10px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 13px;
            min-width: 200px;
        }
        .form-inline select:focus {
            border-color: #0d47a1;
            outline: none;
        }
        
        .badge-binaan {
            display: inline-block;
            padding: 3px 12px;
            background: #e8f0fe;
            color: #0d47a1;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            margin: 2px 4px 2px 0;
        }
        .badge-belum {
            display: inline-block;
            padding: 3px 12px;
            background: #f5f5f5;
            color: #888;
            border-radius: 12px;
            font-size: 12px;
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
        
        .empty-state {
            text-align: center;
            padding: 30px;
            color: #888;
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
        }
        .modal-overlay.active { display: flex; }
        .modal-content {
            background: white;
            border-radius: 15px;
            padding: 35px;
            max-width: 500px;
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
        .modal-content .modal-header h3 { margin: 0; }
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
        .modal-content .form-group textarea {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid #e8ecf1;
            border-radius: 8px;
            font-size: 14px;
        }
        .modal-content .form-group input:focus,
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
        }
        .modal-content .btn-submit:hover { opacity: 0.9; }
        
        .binaan-list {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }
        .btn-remove-binaan {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            font-weight: bold;
            padding: 0 4px;
            font-size: 14px;
        }
        .btn-remove-binaan:hover { color: #c82333; }
        
        .info-note {
            background: #e8f0fe;
            padding: 12px 16px;
            border-radius: 8px;
            border-left: 4px solid #0d47a1;
            margin-top: 10px;
        }
        .info-note p { margin: 0; font-size: 13px; color: #555; }
        
        @media (max-width: 768px) {
            .form-inline { flex-direction: column; align-items: stretch; }
            .form-inline select { min-width: 100%; }
            .modal-content { padding: 25px 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏫 Kelola Sekolah & Sekolah Binaan</h1>
            <p>Kelola data sekolah dan tetapkan sekolah binaan untuk pengawas (bisa lebih dari 1)</p>
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
                    <div class="number"><?php echo $total_sekolah; ?></div>
                    <div class="label">🏫 Total Sekolah</div>
                </div>
                <div class="stat-card">
                    <div class="number"><?php echo $total_pengawas; ?></div>
                    <div class="label">🔍 Total Pengawas</div>
                </div>
                <div class="stat-card">
                    <div class="number"><?php echo $total_binaan; ?></div>
                    <div class="label">📋 Total Binaan</div>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="btn-group">
                <button onclick="openModal()" class="btn btn-success">➕ Tambah Sekolah</button>
            </div>

            <!-- ========================================== -->
            <!-- DAFTAR SEKOLAH -->
            <!-- ========================================== -->
            <div class="section-title">
                <span>🏫 Daftar Sekolah <span class="badge-count"><?php echo $total_sekolah; ?></span></span>
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
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total_sekolah > 0): ?>
                            <?php $no = 1; foreach ($sekolah as $s): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><strong><?php echo $s['nama_sekolah']; ?></strong></td>
                                <td><?php echo $s['npsn']; ?></td>
                                <td><?php echo $s['kecamatan']; ?></td>
                                <td><?php echo $s['kepala_sekolah_name'] ?? '-'; ?></td>
                                <td>
                                    <a href="admin_edit_sekolah.php?id=<?php echo $s['id']; ?>" class="btn btn-warning btn-sm" style="text-decoration:none;">✏️</a>
                                    <a href="admin_delete_sekolah.php?id=<?php echo $s['id']; ?>" class="btn btn-danger btn-sm" style="text-decoration:none;" onclick="return confirm('Hapus sekolah ini?')">🗑️</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="empty-state">Belum ada data sekolah.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- ========================================== -->
            <!-- DAFTAR PENGAWAS & SEKOLAH BINAAN -->
            <!-- ========================================== -->
            <div class="section-title">
                <span>🔍 Pengawas & Sekolah Binaan <span class="badge-count"><?php echo $total_pengawas; ?></span></span>
                <span style="font-size:12px; color:#888;">* Pengawas bisa memiliki lebih dari 1 sekolah binaan</span>
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
                        <?php if ($total_pengawas > 0): ?>
                            <?php $no = 1; foreach ($pengawas_list as $p): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><strong><?php echo $p['name']; ?></strong></td>
                                <td><?php echo $p['nip'] ?? '-'; ?></td>
                                <td>
                                    <?php 
                                    $jenjang = 'DIKDAS';
                                    if (strpos(strtolower($p['sekolah'] ?? ''), 'paud') !== false) {
                                        $jenjang = 'PAUD';
                                    }
                                    echo $jenjang;
                                    ?>
                                </td>
                                <td>
                                    <?php if ($p['sekolah_binaan_ids']): ?>
                                        <div class="binaan-list">
                                            <?php 
                                            $nama_list = explode(', ', $p['sekolah_binaan_nama']);
                                            $id_list = explode(',', $p['sekolah_binaan_ids']);
                                            foreach ($nama_list as $index => $nama): 
                                            ?>
                                                <span class="badge-binaan">
                                                    ✅ <?php echo $nama; ?>
                                                    <form method="POST" style="display:inline;">
                                                        <input type="hidden" name="id_pengawas" value="<?php echo $p['id']; ?>">
                                                        <input type="hidden" name="id_sekolah" value="<?php echo $id_list[$index]; ?>">
                                                        <input type="hidden" name="action" value="hapus_binaan">
                                                        <button type="submit" class="btn-remove-binaan" onclick="return confirm('Hapus binaan ini?')" title="Hapus">×</button>
                                                    </form>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge-belum">⏳ Belum ditentukan</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <!-- Form Tetapkan Binaan -->
                                    <form method="POST" class="form-inline">
                                        <input type="hidden" name="id_pengawas" value="<?php echo $p['id']; ?>">
                                        <input type="hidden" name="action" value="tetapkan_binaan">
                                        
                                        <select name="id_sekolah" required>
                                            <option value="">-- Tambah Sekolah --</option>
                                            <?php foreach ($sekolah_list as $s): ?>
                                                <option value="<?php echo $s['id']; ?>">
                                                    <?php echo $s['nama_sekolah']; ?> (<?php echo $s['npsn']; ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        
                                        <button type="submit" class="btn btn-success btn-sm">➕ Tambah</button>
                                    </form>
                                    
                                    <?php if ($p['sekolah_binaan_ids']): ?>
                                    <!-- Form Hapus Semua Binaan -->
                                    <form method="POST" style="display:inline; margin-top:5px;">
                                        <input type="hidden" name="id_pengawas" value="<?php echo $p['id']; ?>">
                                        <input type="hidden" name="action" value="hapus_semua_binaan">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus SEMUA sekolah binaan pengawas ini?')">🗑️ Hapus Semua</button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="empty-state">Belum ada data pengawas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Informasi -->
            <div class="info-note">
                <p>💡 <strong>Cara menambahkan sekolah binaan:</strong></p>
                <p>1. Pilih sekolah dari dropdown di samping nama pengawas</p>
                <p>2. Klik tombol <strong>"➕ Tambah"</strong></p>
                <p>3. Untuk menghapus satu sekolah, klik <strong>"×"</strong> pada badge sekolah</p>
                <p>4. Untuk menghapus semua, klik <strong>"🗑️ Hapus Semua"</strong></p>
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