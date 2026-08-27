<?php
// index.php - Halaman Utama Website Perangkat Pembelajaran
session_start();

// Include koneksi database
include_once 'config/database.php';

// Hitung statistik
$total_perangkat = countData("SELECT * FROM perangkat WHERE status = 'terverifikasi'");
$total_guru = countData("SELECT * FROM users WHERE role = 'guru'");
$total_downloads_query = fetchOne("SELECT SUM(downloads) as total FROM perangkat");
$total_downloads = $total_downloads_query['total'] ?? 0;

// Ambil 6 perangkat terbaru
$perangkat_terbaru = fetchAll("
    SELECT p.*, u.name as guru_name, mp.nama_mapel, k.nama_kelas 
    FROM perangkat p
    LEFT JOIN users u ON p.id_guru = u.id
    LEFT JOIN mata_pelajaran mp ON p.id_mapel = mp.id
    LEFT JOIN kelas k ON p.id_kelas = k.id
    WHERE p.status = 'terverifikasi'
    ORDER BY p.created_at DESC
    LIMIT 6
");

// Ambil 6 perangkat paling populer
$perangkat_populer = fetchAll("
    SELECT p.*, u.name as guru_name, mp.nama_mapel, k.nama_kelas 
    FROM perangkat p
    LEFT JOIN users u ON p.id_guru = u.id
    LEFT JOIN mata_pelajaran mp ON p.id_mapel = mp.id
    LEFT JOIN kelas k ON p.id_kelas = k.id
    WHERE p.status = 'terverifikasi'
    ORDER BY p.views DESC
    LIMIT 6
");

// Statistik per jenjang
$stat_sd = countData("
    SELECT p.* FROM perangkat p 
    JOIN kelas k ON p.id_kelas = k.id 
    WHERE p.status = 'terverifikasi' AND k.jenjang = 'SD'
");
$stat_smp = countData("
    SELECT p.* FROM perangkat p 
    JOIN kelas k ON p.id_kelas = k.id 
    WHERE p.status = 'terverifikasi' AND k.jenjang = 'SMP'
");
$stat_sma = countData("
    SELECT p.* FROM perangkat p 
    JOIN kelas k ON p.id_kelas = k.id 
    WHERE p.status = 'terverifikasi' AND k.jenjang = 'SMA'
");

// Statistik per jenis
$stat_rpp = countData("SELECT * FROM perangkat WHERE status = 'terverifikasi' AND jenis = 'RPP'");
$stat_modul = countData("SELECT * FROM perangkat WHERE status = 'terverifikasi' AND jenis = 'Modul'");
$stat_ppt = countData("SELECT * FROM perangkat WHERE status = 'terverifikasi' AND jenis = 'PPT'");
$stat_video = countData("SELECT * FROM perangkat WHERE status = 'terverifikasi' AND jenis = 'Video'");
$stat_soal = countData("SELECT * FROM perangkat WHERE status = 'terverifikasi' AND jenis = 'Soal'");
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
        <!-- HEADER -->
        <header class="header">
            <div>
                <h1><i class="fas fa-book-open"></i> Pusat Perangkat Pembelajaran</h1>
                <p>Kumpulan RPP, Modul, PPT, Video, dan Soal untuk Guru</p>
            </div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="badge-user">
                    <i class="fas fa-user"></i> <?php echo $_SESSION['name']; ?> 
                    (<?php echo $_SESSION['role']; ?>)
                </div>
            <?php endif; ?>
        </header>

         <!-- ===== NAVIGASI PAKAI NAVBAR.PHP ===== -->
        <?php include_once 'navbar.php'; ?>
        <!-- ===================================== -->

        <main>
            <!-- Hero -->
            <div class="hero-section">
                <h2>📖 Selamat Datang di Pusat Perangkat Pembelajaran!</h2>
                <p>Temukan berbagai perangkat pembelajaran berkualitas untuk mendukung kegiatan mengajar dan belajar.</p>
                <a href="/website_perangkat/katalog.php" class="btn-hero">📋 Lihat Katalog</a>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="/website_perangkat/login.php" class="btn-hero" style="background:rgba(255,255,255,0.2); color:white; margin-left:10px;">🔐 Login</a>
                <?php endif; ?>
            </div>

            <!-- Statistik -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="number"><?php echo $total_perangkat; ?></div>
                    <div class="label">📄 Total Perangkat</div>
                </div>
                <div class="stat-card">
                    <div class="number green"><?php echo $total_guru; ?></div>
                    <div class="label">👨‍🏫 Total Guru</div>
                </div>
                <div class="stat-card">
                    <div class="number blue"><?php echo $total_downloads; ?></div>
                    <div class="label">⬇️ Total Unduhan</div>
                </div>
                <div class="stat-card">
                    <div class="number purple"><?php echo $stat_rpp + $stat_modul + $stat_ppt + $stat_video + $stat_soal; ?></div>
                    <div class="label">📚 Dokumen Terverifikasi</div>
                </div>
            </div>

            <!-- Per Jenjang -->
            <div style="display:flex; gap:20px; flex-wrap:wrap; margin-bottom:20px;">
                <div style="flex:1; min-width:200px; background:white; padding:15px; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,0.06); text-align:center; border:1px solid rgba(0,0,0,0.04);">
                    <div style="font-size:2em; color:#28a745;">📚</div>
                    <div style="font-size:1.8em; font-weight:bold; color:#28a745;"><?php echo $stat_sd; ?></div>
                    <div style="color:#666; font-size:14px;">SD (Kelas 1-6)</div>
                </div>
                <div style="flex:1; min-width:200px; background:white; padding:15px; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,0.06); text-align:center; border:1px solid rgba(0,0,0,0.04);">
                    <div style="font-size:2em; color:#ffc107;">📗</div>
                    <div style="font-size:1.8em; font-weight:bold; color:#ffc107;"><?php echo $stat_smp; ?></div>
                    <div style="color:#666; font-size:14px;">SMP (Kelas 7-9)</div>
                </div>
                <div style="flex:1; min-width:200px; background:white; padding:15px; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,0.06); text-align:center; border:1px solid rgba(0,0,0,0.04);">
                    <div style="font-size:2em; color:#667eea;">📘</div>
                    <div style="font-size:1.8em; font-weight:bold; color:#667eea;"><?php echo $stat_sma; ?></div>
                    <div style="color:#666; font-size:14px;">SMA (Kelas 10-12)</div>
                </div>
            </div>

            <!-- Perangkat Terbaru -->
            <div class="section-title">
                <span>🆕 Perangkat Terbaru</span>
                <a href="/website_perangkat/katalog.php">Lihat semua →</a>
            </div>
            <div class="perangkat-grid">
                <?php if (count($perangkat_terbaru) > 0): ?>
                    <?php foreach ($perangkat_terbaru as $p): ?>
                        <?php $badge_class = 'badge-' . strtolower($p['jenis']); ?>
                        <div class="perangkat-card">
                            <div class="card-body">
                                <span class="badge <?php echo $badge_class; ?>"><?php echo $p['jenis']; ?></span>
                                <h4><a href="detail.php?id=<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['judul']); ?></a></h4>
                                <div class="meta">
                                    <span>📚 <?php echo $p['nama_mapel'] ?? '-'; ?></span>
                                    <span>🏫 Kelas <?php echo $p['nama_kelas'] ?? '-'; ?></span>
                                </div>
                                <div class="meta">
                                    <span>👨‍🏫 <?php echo $p['guru_name'] ?? 'Unknown'; ?></span>
                                    <span>📅 <?php echo date('d/m/Y', strtotime($p['created_at'])); ?></span>
                                </div>
                                <a href="detail.php?id=<?php echo $p['id']; ?>" class="btn-detail">👁️ Lihat Detail</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="grid-column:1/-1; text-align:center; padding:30px; background:white; border-radius:10px; color:#888;">
                        📭 Belum ada perangkat pembelajaran terverifikasi.
                    </div>
                <?php endif; ?>
            </div>

            <!-- Perangkat Populer -->
            <div class="section-title">
                <span>🔥 Perangkat Populer</span>
                <a href="/website_perangkat/katalog.php">Lihat semua →</a>
            </div>
            <div class="perangkat-grid">
                <?php if (count($perangkat_populer) > 0): ?>
                    <?php foreach ($perangkat_populer as $p): ?>
                        <?php $badge_class = 'badge-' . strtolower($p['jenis']); ?>
                        <div class="perangkat-card">
                            <div class="card-body">
                                <span class="badge <?php echo $badge_class; ?>"><?php echo $p['jenis']; ?></span>
                                <h4><a href="detail.php?id=<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['judul']); ?></a></h4>
                                <div class="meta">
                                    <span>📚 <?php echo $p['nama_mapel'] ?? '-'; ?></span>
                                    <span>🏫 Kelas <?php echo $p['nama_kelas'] ?? '-'; ?></span>
                                </div>
                                <div class="meta">
                                    <span>👁️ <?php echo $p['views']; ?> dilihat</span>
                                    <span>⬇️ <?php echo $p['downloads']; ?> diunduh</span>
                                </div>
                                <a href="detail.php?id=<?php echo $p['id']; ?>" class="btn-detail">👁️ Lihat Detail</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="grid-column:1/-1; text-align:center; padding:30px; background:white; border-radius:10px; color:#888;">
                        📭 Belum ada perangkat pembelajaran populer.
                    </div>
                <?php endif; ?>
            </div>

            <!-- Statistik per Jenis -->
            <div class="section-title">
                <span>📊 Statistik per Jenis</span>
            </div>
            <div class="jenis-stats">
                <div class="jenis-item">
                    <div class="jml"><?php echo $stat_rpp; ?></div>
                    <div class="jml-label">📋 RPP</div>
                </div>
                <div class="jenis-item">
                    <div class="jml"><?php echo $stat_modul; ?></div>
                    <div class="jml-label">📖 Modul</div>
                </div>
                <div class="jenis-item">
                    <div class="jml"><?php echo $stat_ppt; ?></div>
                    <div class="jml-label">📊 PPT</div>
                </div>
                <div class="jenis-item">
                    <div class="jml"><?php echo $stat_video; ?></div>
                    <div class="jml-label">🎬 Video</div>
                </div>
                <div class="jenis-item">
                    <div class="jml"><?php echo $stat_soal; ?></div>
                    <div class="jml-label">📝 Soal</div>
                </div>
            </div>

            <!-- CTA -->
            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'guru'): ?>
                <div class="cta-section">
                    <h3>📤 Bagikan Perangkat Pembelajaran Anda!</h3>
                    <p>Upload perangkat pembelajaran yang Anda buat dan bagikan dengan guru lainnya.</p>
                    <a href="/website_perangkat/upload.php" class="btn-hero">📤 Upload Sekarang</a>
                </div>
            <?php else: ?>
                <div class="cta-section" style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h3>👋 Bergabunglah dengan Kami!</h3>
                    <p>Daftar sebagai guru dan bagikan perangkat pembelajaran Anda.</p>
                    <a href="/website_perangkat/register.php" class="btn-hero" style="color:#667eea;">📝 Daftar Sekarang</a>
                </div>
            <?php endif; ?>
        </main>

        <footer class="footer">
            <p>&copy; <?php echo date('Y'); ?> Pusat Perangkat Pembelajaran</p>
        </footer>
    </div>
</body>
</html>