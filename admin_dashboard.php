<?php
// admin_dashboard.php - Dashboard Admin dengan Kalimat Inspiratif
session_start();

// Cek login dan role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

include_once 'config/database.php';

// Ambil statistik
$total_sekolah = countData("SELECT * FROM sekolah");
$total_guru = countData("SELECT * FROM users WHERE role = 'guru'");
$total_kepsek = countData("SELECT * FROM users WHERE role = 'kepala_sekolah'");
$total_pengawas = countData("SELECT * FROM users WHERE role = 'pengawas'");
$total_dinas = countData("SELECT * FROM users WHERE role = 'dinas'");
$total_perangkat = countData("SELECT * FROM perangkat");
$total_terverifikasi = countData("SELECT * FROM perangkat WHERE status = 'terverifikasi'");
$total_pending_kepsek = countData("SELECT * FROM perangkat WHERE status = 'pending_kepsek'");
$total_pending_pengawas = countData("SELECT * FROM perangkat WHERE status = 'pending_pengawas'");
$total_ditolak = countData("SELECT * FROM perangkat WHERE status = 'ditolak_kepsek' OR status = 'ditolak_pengawas'");

// Quotes inspiratif
$quotes = [
    '"Pendidikan adalah senjata paling ampuh untuk mengubah dunia." - Nelson Mandela',
    '"Guru terbaik adalah mereka yang menginspirasi, bukan hanya mengajar."',
    '"Kepala sekolah yang baik adalah pemimpin yang melayani."',
    '"Pengawas adalah mitra guru dalam meningkatkan mutu pendidikan."',
    '"Dinas pendidikan adalah garda terdepan kemajuan daerah."',
    '"Siswa adalah masa depan, pendidikan adalah kuncinya."',
    '"Bersama kita wujudkan pendidikan yang berkualitas."',
    '"Perangkat pembelajaran berkualitas adalah fondasi pendidikan yang kuat."',
    '"Inovasi dalam pendidikan dimulai dari guru yang kreatif."',
    '"Kolaborasi semua pihak menciptakan pendidikan yang bermakna."',
    '"Mendidik adalah tugas mulia yang membutuhkan dedikasi dan cinta."',
    '"Pendidikan yang baik adalah investasi terbaik untuk generasi mendatang."',
];

$random_quote = $quotes[array_rand($quotes)];

// Data perangkat terbaru
$perangkat_terbaru = fetchAll("
    SELECT p.*, u.name as guru_name, mp.nama_mapel, k.nama_kelas 
    FROM perangkat p
    LEFT JOIN users u ON p.id_guru = u.id
    LEFT JOIN mata_pelajaran mp ON p.id_mapel = mp.id
    LEFT JOIN kelas k ON p.id_kelas = k.id
    ORDER BY p.created_at DESC
    LIMIT 10
");

// Data user terbaru
$users_terbaru = fetchAll("
    SELECT * FROM users 
    ORDER BY created_at DESC 
    LIMIT 10
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Pusat Perangkat Pembelajaran</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/website_perangkat/public/css/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <div>
                <h1><i class="fas fa-cog"></i> Dashboard Admin</h1>
                <p>Kelola semua data sistem perangkat pembelajaran</p>
            </div>
            <div class="badge-user">
                <i class="fas fa-user"></i> <?php echo $_SESSION['name']; ?>
            </div>
        </header>

        <nav class="nav">
            <a href="/website_perangkat/index.php"><i class="fas fa-home"></i> Beranda</a>
            <a href="/website_perangkat/admin_dashboard.php" class="active"><i class="fas fa-chart-bar"></i> Dashboard</a>
            <a href="/website_perangkat/admin_users.php"><i class="fas fa-users"></i> Kelola User</a>
            <a href="/website_perangkat/admin_sekolah.php"><i class="fas fa-school"></i> Kelola Sekolah</a>
            <a href="/website_perangkat/admin_perangkat.php"><i class="fas fa-file-alt"></i> Kelola Perangkat</a>
            <a href="/website_perangkat/admin_mapel_kelas.php"><i class="fas fa-book"></i> Mapel & Kelas</a>
            <a href="/website_perangkat/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <span class="user-info">
                <span class="avatar"><?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?></span>
                <?php echo $_SESSION['name']; ?>
            </span>
        </nav>

        <main>
            <!-- WELCOME QUOTE -->
            <div class="welcome-quote">
                <div class="icon"><i class="fas fa-quote-left"></i></div>
                <div class="text">
                    <h3>👋 Selamat Datang, <?php echo $_SESSION['name']; ?>!</h3>
                    <p><?php echo $random_quote; ?></p>
                    <div class="author">— Admin Pusat Perangkat Pembelajaran</div>
                </div>
                <div style="font-size:40px; opacity:0.1;"><i class="fas fa-graduation-cap"></i></div>
            </div>

            <!-- STATISTIK -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="icon-circle blue"><i class="fas fa-school"></i></div>
                    <div class="number"><?php echo $total_sekolah; ?></div>
                    <div class="label">🏫 Total Sekolah</div>
                </div>
                <div class="stat-card">
                    <div class="icon-circle green"><i class="fas fa-chalkboard-teacher"></i></div>
                    <div class="number"><?php echo $total_guru; ?></div>
                    <div class="label">👨‍🏫 Total Guru</div>
                </div>
                <div class="stat-card">
                    <div class="icon-circle purple"><i class="fas fa-user-tie"></i></div>
                    <div class="number"><?php echo $total_kepsek; ?></div>
                    <div class="label">👔 Kepala Sekolah</div>
                </div>
                <div class="stat-card">
                    <div class="icon-circle orange"><i class="fas fa-user-shield"></i></div>
                    <div class="number"><?php echo $total_pengawas; ?></div>
                    <div class="label">🔍 Pengawas</div>
                </div>
                <div class="stat-card">
                    <div class="icon-circle teal"><i class="fas fa-chart-line"></i></div>
                    <div class="number"><?php echo $total_dinas; ?></div>
                    <div class="label">📊 Dinas</div>
                </div>
                <div class="stat-card">
                    <div class="icon-circle blue"><i class="fas fa-file-alt"></i></div>
                    <div class="number"><?php echo $total_perangkat; ?></div>
                    <div class="label">📄 Total Dokumen</div>
                </div>
                <div class="stat-card">
                    <div class="icon-circle green"><i class="fas fa-check-circle"></i></div>
                    <div class="number green"><?php echo $total_terverifikasi; ?></div>
                    <div class="label">✅ Terverifikasi</div>
                </div>
                <div class="stat-card">
                    <div class="icon-circle orange"><i class="fas fa-hourglass-half"></i></div>
                    <div class="number orange"><?php echo $total_pending_kepsek + $total_pending_pengawas; ?></div>
                    <div class="label">⏳ Pending</div>
                    <div class="sub-label">Kepsek: <?php echo $total_pending_kepsek; ?> · Pengawas: <?php echo $total_pending_pengawas; ?></div>
                </div>
                <div class="stat-card">
                    <div class="icon-circle red"><i class="fas fa-times-circle"></i></div>
                    <div class="number red"><?php echo $total_ditolak; ?></div>
                    <div class="label">❌ Ditolak</div>
                </div>
            </div>

            <!-- TWO COLUMN -->
            <div class="two-col">
                <!-- USER TERBARU -->
                <div>
                    <div class="section-title">
                        <span>👥 User Terbaru <span class="badge-count"><?php echo count($users_terbaru); ?></span></span>
                        <a href="admin_users.php" style="font-size:13px; color:#667eea; text-decoration:none;">Lihat semua →</a>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users_terbaru as $user): ?>
                                <tr>
                                    <td><?php echo $user['name']; ?></td>
                                    <td><?php echo $user['email']; ?></td>
                                    <td>
                                        <span class="role-badge role-<?php echo $user['role']; ?>">
                                            <?php echo $user['role']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="admin_edit_user.php?id=<?php echo $user['id']; ?>" class="btn-action btn-edit">✏️</a>
                                        <a href="admin_delete_user.php?id=<?php echo $user['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Hapus user ini?')">🗑️</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- PERANGKAT TERBARU -->
                <div>
                    <div class="section-title">
                        <span>📄 Perangkat Terbaru <span class="badge-count"><?php echo count($perangkat_terbaru); ?></span></span>
                        <a href="admin_perangkat.php" style="font-size:13px; color:#667eea; text-decoration:none;">Lihat semua →</a>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Guru</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($perangkat_terbaru as $p): ?>
                                <tr>
                                    <td><?php echo substr($p['judul'], 0, 25); ?>...</td>
                                    <td><?php echo $p['guru_name'] ?? '-'; ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $p['status']; ?>">
                                            <?php 
                                            $status_label = [
                                                'draft' => 'Draft',
                                                'pending_kepsek' => 'Pending Kepsek',
                                                'ditolak_kepsek' => 'Ditolak Kepsek',
                                                'pending_pengawas' => 'Pending Pengawas',
                                                'ditolak_pengawas' => 'Ditolak Pengawas',
                                                'terverifikasi' => '✅ Terverifikasi'
                                            ];
                                            echo $status_label[$p['status']] ?? $p['status'];
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="detail.php?id=<?php echo $p['id']; ?>" class="btn-action btn-view">👁️</a>
                                        <a href="admin_delete_perangkat.php?id=<?php echo $p['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Hapus perangkat ini?')">🗑️</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- QUICK ACTION -->
            <div style="margin-top:25px; padding:20px; background:white; border-radius:12px; border:1px solid rgba(0,0,0,0.04);">
                <div style="display:flex; gap:12px; flex-wrap:wrap; justify-content:center;">
                    <a href="admin_users.php" class="btn btn-primary"><i class="fas fa-users"></i> Kelola User</a>
                    <a href="admin_sekolah.php" class="btn btn-success"><i class="fas fa-school"></i> Kelola Sekolah</a>
                    <a href="admin_perangkat.php" class="btn btn-warning"><i class="fas fa-file-alt"></i> Kelola Perangkat</a>
                    <a href="admin_mapel_kelas.php" class="btn btn-info"><i class="fas fa-book"></i> Mapel & Kelas</a>
                    <a href="export_data.php" class="btn btn-danger"><i class="fas fa-file-excel"></i> Export Data</a>
                </div>
            </div>

            <!-- MOTTO -->
            <div style="margin-top:20px; text-align:center; padding:15px; background:linear-gradient(135deg, #f8fafc 0%, #eef2f7 100%); border-radius:12px; border:1px solid rgba(0,0,0,0.04);">
                <p style="font-size:14px; color:#555; font-style:italic;">
                    <i class="fas fa-quote-left" style="color:#667eea;"></i> 
                    Bersama kita wujudkan pendidikan yang berkualitas, inspiratif, dan bermakna bagi semua.
                    <i class="fas fa-quote-right" style="color:#667eea;"></i>
                </p>
                <p style="font-size:12px; color:#888; margin-top:4px;">
                    <i class="fas fa-heart" style="color:#dc3545;"></i> 
                    Untuk Guru, Kepala Sekolah, Pengawas, Dinas, dan Siswa yang berdedikasi
                </p>
            </div>
        </main>

        <footer class="footer">
            <p>&copy; <?php echo date('Y'); ?> Pusat Perangkat Pembelajaran - Admin Panel</p>
            <p style="font-size:11px; color:#aaa; margin-top:2px;">
                <i class="fas fa-graduation-cap"></i> Mencerdaskan Kehidupan Bangsa
            </p>
        </footer>
    </div>
</body>
</html>