<?php
// katalog.php - Halaman Katalog Perangkat Pembelajaran
session_start();
include_once 'config/database.php';

// Ambil data untuk filter
$mapel_list = fetchAll("SELECT * FROM mata_pelajaran ORDER BY nama_mapel");
$kelas_list = fetchAll("SELECT * FROM kelas ORDER BY jenjang, CAST(nama_kelas AS UNSIGNED)");

// Filter
$where = "WHERE p.status = 'terverifikasi'";

if (isset($_GET['mapel']) && !empty($_GET['mapel'])) {
    $mapel = (int)$_GET['mapel'];
    $where .= " AND p.id_mapel = $mapel";
}

if (isset($_GET['kelas']) && !empty($_GET['kelas'])) {
    $kelas = (int)$_GET['kelas'];
    $where .= " AND p.id_kelas = $kelas";
}

if (isset($_GET['semester']) && !empty($_GET['semester'])) {
    $semester = $_GET['semester'];
    $where .= " AND p.semester = '$semester'";
}

if (isset($_GET['jenis']) && !empty($_GET['jenis'])) {
    $jenis = $_GET['jenis'];
    $where .= " AND p.jenis = '$jenis'";
}

if (isset($_GET['cari']) && !empty($_GET['cari'])) {
    $cari = mysqli_real_escape_string($conn, $_GET['cari']);
    $where .= " AND (p.judul LIKE '%$cari%' OR p.deskripsi LIKE '%$cari%')";
}

// Ambil data perangkat
$perangkat = fetchAll("
    SELECT p.*, u.name as guru_name, mp.nama_mapel, k.nama_kelas, k.jenjang
    FROM perangkat p
    LEFT JOIN users u ON p.id_guru = u.id
    LEFT JOIN mata_pelajaran mp ON p.id_mapel = mp.id
    LEFT JOIN kelas k ON p.id_kelas = k.id
    $where
    ORDER BY p.created_at DESC
");

$total_dokumen = countData("SELECT * FROM perangkat WHERE status = 'terverifikasi'");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru - Pusat Perangkat Pembelajaran</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/website_perangkat/public/css/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <div>
                <h1><i class="fas fa-book"></i> Katalog Perangkat Pembelajaran</h1>
                <p>Kumpulan RPP, Modul, PPT, Video, dan Soal untuk Guru</p>
            </div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="badge-user">
                    <i class="fas fa-user"></i> <?php echo $_SESSION['name']; ?>
                </div>
            <?php endif; ?>
        </header>

   <!-- ===== NAVIGASI PAKAI NAVBAR.PHP ===== -->
        <?php include_once 'navbar.php'; ?>
        <!-- ===================================== -->

        <main>
            <div class="stat-info">
                <div class="total">
                    📊 Menampilkan <strong><?php echo count($perangkat); ?></strong> dari <strong><?php echo $total_dokumen; ?></strong> dokumen terverifikasi
                </div>
            </div>

            <div class="filter-section">
                <form method="GET" class="filter-form">
                    <div class="form-group">
                        <label>🔍 Cari</label>
                        <input type="text" name="cari" placeholder="Cari judul..." value="<?php echo isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>📚 Mata Pelajaran</label>
                        <select name="mapel">
                            <option value="">-- Semua --</option>
                            <?php foreach ($mapel_list as $m): ?>
                                <option value="<?php echo $m['id']; ?>" <?php echo (isset($_GET['mapel']) && $_GET['mapel'] == $m['id']) ? 'selected' : ''; ?>>
                                    <?php echo $m['nama_mapel']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>🏫 Kelas</label>
                        <select name="kelas">
                            <option value="">-- Semua --</option>
                            <?php foreach ($kelas_list as $k): ?>
                                <option value="<?php echo $k['id']; ?>" <?php echo (isset($_GET['kelas']) && $_GET['kelas'] == $k['id']) ? 'selected' : ''; ?>>
                                    Kelas <?php echo $k['nama_kelas']; ?> (<?php echo $k['jenjang']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>📖 Semester</label>
                        <select name="semester">
                            <option value="">-- Semua --</option>
                            <option value="1" <?php echo (isset($_GET['semester']) && $_GET['semester'] == '1') ? 'selected' : ''; ?>>Semester 1</option>
                            <option value="2" <?php echo (isset($_GET['semester']) && $_GET['semester'] == '2') ? 'selected' : ''; ?>>Semester 2</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>📄 Jenis</label>
                        <select name="jenis">
                            <option value="">-- Semua --</option>
                            <option value="RPP" <?php echo (isset($_GET['jenis']) && $_GET['jenis'] == 'RPP') ? 'selected' : ''; ?>>RPP</option>
                            <option value="Modul" <?php echo (isset($_GET['jenis']) && $_GET['jenis'] == 'Modul') ? 'selected' : ''; ?>>Modul</option>
                            <option value="PPT" <?php echo (isset($_GET['jenis']) && $_GET['jenis'] == 'PPT') ? 'selected' : ''; ?>>PPT</option>
                            <option value="Video" <?php echo (isset($_GET['jenis']) && $_GET['jenis'] == 'Video') ? 'selected' : ''; ?>>Video</option>
                            <option value="Soal" <?php echo (isset($_GET['jenis']) && $_GET['jenis'] == 'Soal') ? 'selected' : ''; ?>>Soal</option>
                            <option value="Lainnya" <?php echo (isset($_GET['jenis']) && $_GET['jenis'] == 'Lainnya') ? 'selected' : ''; ?>>Lainnya</option>
                        </select>
                    </div>
                    <div class="btn-group">
                        <button type="submit" class="btn-filter">🔍 Filter</button>
                        <a href="katalog.php" class="btn-reset">↺ Reset</a>
                    </div>
                </form>
            </div>

            <?php if (count($perangkat) > 0): ?>
                <div class="catalog-grid">
                    <?php foreach ($perangkat as $p): ?>
                        <?php $badge_class = 'badge-' . strtolower($p['jenis']); ?>
                        <div class="catalog-item">
                            <div class="card-body">
                                <span class="badge <?php echo $badge_class; ?>">
                                    <?php echo $p['jenis']; ?>
                                </span>
                                <h3>
                                    <a href="detail.php?id=<?php echo $p['id']; ?>">
                                        <?php echo htmlspecialchars($p['judul']); ?>
                                    </a>
                                </h3>
                                <div class="meta">
                                    <span>📚 <?php echo $p['nama_mapel'] ?? '-'; ?></span>
                                    <span>🏫 Kelas <?php echo $p['nama_kelas'] ?? '-'; ?></span>
                                    <span>📖 Semester <?php echo $p['semester']; ?></span>
                                </div>
                                <div class="meta">
                                    <span>👨‍🏫 <?php echo $p['guru_name'] ?? 'Unknown'; ?></span>
                                    <span>📅 <?php echo date('d/m/Y', strtotime($p['created_at'])); ?></span>
                                </div>
                                <?php if (!empty($p['deskripsi'])): ?>
                                    <p style="font-size: 13px; color: #666; margin: 8px 0;">
                                        <?php echo substr(htmlspecialchars($p['deskripsi']), 0, 100); ?>...
                                    </p>
                                <?php endif; ?>
                                <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:10px;">
                                    <a href="detail.php?id=<?php echo $p['id']; ?>" class="btn-detail">👁️ Detail</a>
                                    <?php if (!empty($p['file_path']) && file_exists($p['file_path'])): ?>
                                        <a href="<?php echo $p['file_path']; ?>" class="btn-download" download>⬇️ Download</a>
                                    <?php endif; ?>
                                </div>
                                <div class="stats">
                                    <span>👁️ <?php echo $p['views']; ?> dilihat</span>
                                    <span>⬇️ <?php echo $p['downloads']; ?> diunduh</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="icon">📭</div>
                    <h2>Belum Ada Perangkat</h2>
                    <p>Belum ada perangkat pembelajaran yang terverifikasi.</p>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'guru'): ?>
                        <p><a href="upload.php" class="btn-filter" style="display:inline-block; margin-top:10px;">📤 Upload Perangkat</a></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </main>

        <footer class="footer">
            <p>&copy; <?php echo date('Y'); ?> Pusat Perangkat Pembelajaran</p>
        </footer>
    </div>
</body>
</html>