<?php
// presensi_guru.php - Halaman Presensi Guru & Kepala Sekolah
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Izinkan semua role (guru, kepala_sekolah, pengawas, dinas)
$allowed_roles = ['guru', 'kepala_sekolah', 'pengawas', 'dinas'];
if (!in_array($_SESSION['role'], $allowed_roles)) {
    header('Location: index.php');
    exit;
}

include_once 'config/database.php';

$id_user = $_SESSION['user_id'];
$role = $_SESSION['role'];
$tanggal = date('Y-m-d');

// Cek presensi hari ini
$cek = fetchOne("SELECT * FROM presensi_guru WHERE id_guru = $id_user AND tanggal = '$tanggal'");

// Proses presensi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $sekolah_id = $_SESSION['sekolah_id'] ?? 0;
    $jam = date('H:i:s');
    
    if ($action == 'masuk') {
        if (!$cek) {
            $sql = "INSERT INTO presensi_guru (id_sekolah, id_guru, tanggal, jam_masuk, status) 
                    VALUES ($sekolah_id, $id_user, '$tanggal', '$jam', 'hadir')";
            if (query($sql)) {
                $success = "✅ Presensi masuk berhasil! Jam: $jam";
                $cek = fetchOne("SELECT * FROM presensi_guru WHERE id_guru = $id_user AND tanggal = '$tanggal'");
            }
        }
    } elseif ($action == 'keluar') {
        if ($cek && !$cek['jam_keluar']) {
            $sql = "UPDATE presensi_guru SET jam_keluar = '$jam' WHERE id = " . $cek['id'];
            if (query($sql)) {
                $success = "✅ Presensi keluar berhasil! Jam: $jam";
                $cek = fetchOne("SELECT * FROM presensi_guru WHERE id_guru = $id_user AND tanggal = '$tanggal'");
            }
        }
    }
}

// Riwayat presensi
$riwayat = fetchAll("
    SELECT * FROM presensi_guru 
    WHERE id_guru = $id_user 
    ORDER BY tanggal DESC 
    LIMIT 30
");

// Role label
$role_label = [
    'guru' => '👨‍🏫 Guru',
    'kepala_sekolah' => '👔 Kepala Sekolah',
    'pengawas' => '🔍 Pengawas',
    'dinas' => '📊 Dinas',
    'admin' => '⚙️ Admin'
];
$role_display = $role_label[$role] ?? $role;

// Header color berdasarkan role
$header_colors = [
    'guru' => 'linear-gradient(135deg, #059669 0%, #10b981 100%)',
    'kepala_sekolah' => 'linear-gradient(135deg, #1a237e 0%, #0d47a1 100%)',
    'pengawas' => 'linear-gradient(135deg, #6f42c1 0%, #8b5cf6 100%)',
    'dinas' => 'linear-gradient(135deg, #0d9488 0%, #14b8a6 100%)'
];
$header_color = $header_colors[$role] ?? 'linear-gradient(135deg, #059669 0%, #10b981 100%)';
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
                <h1><i class="fas fa-clipboard-check"></i> Presensi <?php echo $role_display; ?></h1>
                <p>Absensi kehadiran <?php echo strtolower($role_display); ?></p>
            </div>
            <div>
                <i class="fas fa-user"></i> <?php echo $_SESSION['name']; ?>
            </div>
        </header>

        <?php include_once 'navbar.php'; ?>

        <main>
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Presensi Hari Ini -->
            <div class="presensi-card">
                <div class="status">
                    <?php if ($cek): ?>
                        <?php if ($cek['jam_masuk']): ?>
                            ✅
                        <?php else: ?>
                            ⏳
                        <?php endif; ?>
                    <?php else: ?>
                        ⏳
                    <?php endif; ?>
                </div>
                <div>
                    <span class="role-badge role-<?php echo $role; ?>">
                        <?php echo $role_display; ?>
                    </span>
                </div>
                <div class="time">
                    <?php echo date('H:i:s'); ?>
                </div>
                <div class="date">
                    <i class="far fa-calendar-alt"></i> <?php echo date('l, d F Y'); ?>
                </div>
                <div style="margin-bottom:10px;">
                    <?php if ($cek): ?>
                        <?php if ($cek['jam_masuk']): ?>
                            <span style="color:#059669;">✅ Masuk: <?php echo $cek['jam_masuk']; ?></span>
                        <?php endif; ?>
                        <?php if ($cek['jam_keluar']): ?>
                            <span style="color:#dc3545; margin-left:16px;">🚪 Keluar: <?php echo $cek['jam_keluar']; ?></span>
                        <?php endif; ?>
                    <?php else: ?>
                        <span style="color:#6b7280;">Belum presensi hari ini</span>
                    <?php endif; ?>
                </div>
                <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
                    <?php if (!$cek || !$cek['jam_masuk']): ?>
                        <form method="POST">
                            <input type="hidden" name="action" value="masuk">
                            <button type="submit" class="btn-presensi btn-masuk">
                                <i class="fas fa-sign-in-alt"></i> Presensi Masuk
                            </button>
                        </form>
                    <?php endif; ?>
                    
                    <?php if ($cek && $cek['jam_masuk'] && !$cek['jam_keluar']): ?>
                        <form method="POST">
                            <input type="hidden" name="action" value="keluar">
                            <button type="submit" class="btn-presensi btn-keluar">
                                <i class="fas fa-sign-out-alt"></i> Presensi Keluar
                            </button>
                        </form>
                    <?php endif; ?>
                    
                    <?php if ($cek && $cek['jam_masuk'] && $cek['jam_keluar']): ?>
                        <button class="btn-presensi btn-disabled" disabled>
                            <i class="fas fa-check-circle"></i> Selesai
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Riwayat Presensi -->
            <h3 style="margin-bottom:12px;">📋 Riwayat Presensi</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jam Masuk</th>
                            <th>Jam Keluar</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($riwayat) > 0): ?>
                            <?php foreach ($riwayat as $r): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($r['tanggal'])); ?></td>
                                <td><?php echo $r['jam_masuk'] ?? '-'; ?></td>
                                <td><?php echo $r['jam_keluar'] ?? '-'; ?></td>
                                <td class="status-<?php echo $r['status']; ?>">
                                    <?php echo strtoupper($r['status']); ?>
                                </td>
                                <td><?php echo $r['keterangan'] ?? '-'; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" style="text-align:center; color:#888;">Belum ada riwayat presensi</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>

        <footer class="footer">
            <p>&copy; <?php echo date('Y'); ?> Pusat Perangkat Pembelajaran</p>
        </footer>
    </div>
</body>
</html>