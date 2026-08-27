<?php
// admin_perangkat.php - Kelola Perangkat
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}
include_once 'config/database.php';

$perangkat = fetchAll("
    SELECT p.*, u.name as guru_name, mp.nama_mapel, k.nama_kelas 
    FROM perangkat p
    LEFT JOIN users u ON p.id_guru = u.id
    LEFT JOIN mata_pelajaran mp ON p.id_mapel = mp.id
    LEFT JOIN kelas k ON p.id_kelas = k.id
    ORDER BY p.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Perangkat - Admin</title>
    <link rel="stylesheet" href="/website_perangkat/public/css/style.css">
    <style>
        .table-container {
            overflow-x: auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-top: 20px;
        }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #6f42c1; color: white; }
        .status-badge { padding: 3px 10px; border-radius: 12px; font-size: 10px; font-weight: bold; display: inline-block; }
        .status-terverifikasi { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-ditolak { background: #f8d7da; color: #721c24; }
        .status-draft { background: #e2e3e5; color: #383d41; }
        .btn-action { padding: 5px 12px; border: none; border-radius: 5px; cursor: pointer; font-size: 11px; text-decoration: none; display: inline-block; }
        .btn-edit { background: #ffc107; color: #000; }
        .btn-delete { background: #dc3545; color: #fff; }
        .btn-view { background: #667eea; color: #fff; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>📄 Kelola Perangkat</h1>
            <p>Kelola semua dokumen perangkat pembelajaran</p>
        </header>
         <!-- ===== NAVIGASI PAKAI NAVBAR.PHP ===== -->
        <?php include_once 'navbar.php'; ?>
        <!-- ===================================== -->
        <main>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Guru</th>
                            <th>Mapel</th>
                            <th>Kelas</th>
                            <th>Status</th>
                            <th>Views</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($perangkat as $p): ?>
                        <tr>
                            <td><?php echo substr($p['judul'], 0, 30); ?>...</td>
                            <td><?php echo $p['guru_name'] ?? '-'; ?></td>
                            <td><?php echo $p['nama_mapel'] ?? '-'; ?></td>
                            <td><?php echo $p['nama_kelas'] ?? '-'; ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $p['status']; ?>">
                                    <?php echo $p['status']; ?>
                                </span>
                            </td>
                            <td><?php echo $p['views']; ?></td>
                            <td>
                                <a href="detail.php?id=<?php echo $p['id']; ?>" class="btn-action btn-view">👁️</a>
                                <a href="admin_delete_perangkat.php?id=<?php echo $p['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Hapus perangkat ini?')">🗑️</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
        <footer>
            <p>&copy; 2026 Pusat Perangkat Pembelajaran</p>
        </footer>
    </div>
</body>
</html>