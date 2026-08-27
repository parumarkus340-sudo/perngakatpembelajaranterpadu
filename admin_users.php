<?php
// admin_users.php - Kelola User
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}
include_once 'config/database.php';

$users = fetchAll("SELECT * FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - Admin</title>
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
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #667eea; color: white; }
        .btn-add { background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-action { padding: 5px 12px; border: none; border-radius: 5px; cursor: pointer; font-size: 12px; text-decoration: none; display: inline-block; }
        .btn-edit { background: #ffc107; color: #000; }
        .btn-delete { background: #dc3545; color: #fff; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>👥 Kelola User</h1>
            <p>Kelola semua user sistem</p>
        </header>
        
         <!-- ===== NAVIGASI PAKAI NAVBAR.PHP ===== -->
        <?php include_once 'navbar.php'; ?>
        <!-- ===================================== -->
        <main>
            <div style="margin-bottom:20px;">
                <a href="admin_add_user.php" class="btn-add">➕ Tambah User</a>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Sekolah</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?php echo $u['id']; ?></td>
                            <td><?php echo $u['name']; ?></td>
                            <td><?php echo $u['email']; ?></td>
                            <td><?php echo $u['role']; ?></td>
                            <td><?php echo $u['sekolah'] ?? '-'; ?></td>
                            <td><?php echo $u['is_active'] ? '✅ Aktif' : '❌ Nonaktif'; ?></td>
                            <td>
                                <a href="admin_edit_user.php?id=<?php echo $u['id']; ?>" class="btn-action btn-edit">✏️</a>
                                <a href="admin_delete_user.php?id=<?php echo $u['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Hapus user ini?')">🗑️</a>
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