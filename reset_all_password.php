<?php
// reset_all_password.php - Reset password semua user
// ============================================
// PERINGATAN: Script ini akan mereset SEMUA password user!
// ============================================

include_once 'config/database.php';

// Cek apakah user sudah login sebagai admin
// Jika tidak, tetap bisa diakses untuk keperluan maintenance
// Tapi beri peringatan

$is_admin = isset($_SESSION['user_id']) && $_SESSION['role'] == 'admin';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password Semua User</title>
    <link rel="stylesheet" href="/website_perangkat/public/css/style.css">
    <style>
        body {
            background: #f0f4f8;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 25px 30px;
            border-radius: 12px;
            margin-bottom: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 1.8em;
        }
        .header p {
            margin: 8px 0 0 0;
            opacity: 0.9;
        }
        .warning-box {
            background: #fff3cd;
            border: 2px solid #ffc107;
            color: #856404;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        .warning-box .icon {
            font-size: 30px;
        }
        .warning-box .text {
            flex: 1;
        }
        .warning-box .text strong {
            display: block;
            font-size: 16px;
        }
        .warning-box .text small {
            font-size: 13px;
        }
        
        .info-box {
            background: #d4edda;
            border: 2px solid #28a745;
            color: #155724;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        .info-box .icon {
            font-size: 30px;
        }
        .info-box .text {
            flex: 1;
        }
        .info-box .text strong {
            display: block;
            font-size: 16px;
        }
        
        .btn-group {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin: 20px 0;
        }
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: scale(1.02);
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-success:hover {
            background: #218838;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #5a67d8;
        }
        
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
            background: #667eea;
            color: white;
            position: sticky;
            top: 0;
        }
        tr:hover {
            background: #f5f5f5;
        }
        .status-success {
            color: #28a745;
            font-weight: bold;
        }
        .status-failed {
            color: #dc3545;
            font-weight: bold;
        }
        .role-badge {
            display: inline-block;
            padding: 2px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }
        .role-guru { background: #d4edda; color: #155724; }
        .role-kepala_sekolah { background: #cce5ff; color: #004085; }
        .role-pengawas { background: #fff3cd; color: #856404; }
        .role-dinas { background: #d1ecf1; color: #0c5460; }
        .role-admin { background: #f8d7da; color: #721c24; }
        
        .summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .summary .item {
            background: white;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .summary .item .number {
            font-size: 2em;
            font-weight: bold;
        }
        .summary .item .label {
            font-size: 13px;
            color: #666;
        }
        .summary .item.success .number { color: #28a745; }
        .summary .item.failed .number { color: #dc3545; }
        .summary .item.total .number { color: #667eea; }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #888;
            font-size: 13px;
        }
        
        .progress-bar {
            width: 100%;
            height: 6px;
            background: #e9ecef;
            border-radius: 3px;
            margin: 10px 0;
            overflow: hidden;
        }
        .progress-bar .fill {
            height: 100%;
            background: linear-gradient(90deg, #28a745, #667eea);
            border-radius: 3px;
            transition: width 0.5s ease;
            width: 0%;
        }
        
        @media (max-width: 768px) {
            .container { padding: 10px; }
            .header h1 { font-size: 1.3em; }
            table { font-size: 11px; }
            th, td { padding: 6px 8px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🔑 Reset Password Semua User</h1>
            <p>Tool untuk mereset password semua user ke default</p>
        </div>

        <?php
        // Proses reset jika tombol ditekan
        if (isset($_POST['reset']) && $_POST['reset'] == 'yes') {
            
            $new_password = 'password123';
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Ambil semua user
            $users = fetchAll("SELECT id, name, email, role FROM users ORDER BY role, name");
            $total = count($users);
            $success = 0;
            $failed = 0;
            $errors = [];
            
            // Tampilkan progress
            echo '<div class="info-box">';
            echo '<span class="icon">🔄</span>';
            echo '<div class="text">';
            echo '<strong>Memproses reset password...</strong>';
            echo '<small>Merubah ' . $total . ' user ke password: <strong>password123</strong></small>';
            echo '</div>';
            echo '</div>';
            
            echo '<div class="progress-bar"><div class="fill" id="progressFill"></div></div>';
            
            // Proses reset
            echo '<div class="table-container">';
            echo '<table>';
            echo '<thead>';
            echo '<tr>';
            echo '<th>No</th>';
            echo '<th>Nama</th>';
            echo '<th>Email</th>';
            echo '<th>Role</th>';
            echo '<th>Status</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            
            $no = 0;
            foreach ($users as $user) {
                $no++;
                $id = $user['id'];
                $name = htmlspecialchars($user['name']);
                $email = htmlspecialchars($user['email']);
                $role = $user['role'];
                
                $role_label = [
                    'admin' => '⚙️ Admin',
                    'guru' => '👨‍🏫 Guru',
                    'kepala_sekolah' => '👔 Kepala Sekolah',
                    'pengawas' => '🔍 Pengawas',
                    'dinas' => '📊 Dinas'
                ];
                $role_display = $role_label[$role] ?? $role;
                
                $sql = "UPDATE users SET password = '$hashed' WHERE id = $id";
                
                if (query($sql)) {
                    $success++;
                    $status = '<span class="status-success">✅ Berhasil</span>';
                } else {
                    $failed++;
                    $error_msg = mysqli_error($conn);
                    $errors[] = "ID $id: $error_msg";
                    $status = '<span class="status-failed">❌ Gagal</span>';
                }
                
                echo '<tr>';
                echo "<td>$no</td>";
                echo "<td>$name</td>";
                echo "<td>$email</td>";
                echo "<td><span class='role-badge role-$role'>$role_display</span></td>";
                echo "<td>$status</td>";
                echo '</tr>';
                
                // Update progress
                $percent = round(($no / $total) * 100);
                echo "<script>document.getElementById('progressFill').style.width = '$percent%';</script>";
                flush();
            }
            
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
            
            // Summary
            echo '<div class="summary">';
            echo '<div class="item total">';
            echo '<div class="number">' . $total . '</div>';
            echo '<div class="label">📊 Total User</div>';
            echo '</div>';
            echo '<div class="item success">';
            echo '<div class="number">' . $success . '</div>';
            echo '<div class="label">✅ Berhasil</div>';
            echo '</div>';
            echo '<div class="item failed">';
            echo '<div class="number">' . $failed . '</div>';
            echo '<div class="label">❌ Gagal</div>';
            echo '</div>';
            echo '</div>';
            
            // Tampilkan error jika ada
            if (!empty($errors)) {
                echo '<div class="warning-box">';
                echo '<span class="icon">⚠️</span>';
                echo '<div class="text">';
                echo '<strong>Error yang terjadi:</strong>';
                echo '<ul style="margin:5px 0; padding-left:20px;">';
                foreach ($errors as $err) {
                    echo "<li>$err</li>";
                }
                echo '</ul>';
                echo '</div>';
                echo '</div>';
            }
            
            // Tombol setelah reset
            echo '<div class="btn-group">';
            echo '<a href="login.php" class="btn btn-success">🔐 Login Sekarang</a>';
            echo '<a href="index.php" class="btn btn-secondary">🏠 Kembali ke Beranda</a>';
            echo '</div>';
            
            echo '<div class="info-box">';
            echo '<span class="icon">✅</span>';
            echo '<div class="text">';
            echo '<strong>Reset Password Selesai!</strong>';
            echo '<small>Semua user sekarang menggunakan password: <strong>password123</strong></small>';
            echo '</div>';
            echo '</div>';
            
        } else {
            // Tampilkan konfirmasi sebelum reset
            ?>
            
            <!-- Warning -->
            <div class="warning-box">
                <span class="icon">⚠️</span>
                <div class="text">
                    <strong>PERINGATAN!</strong>
                    <small>Anda akan mereset SEMUA password user menjadi <strong>password123</strong>. 
                    Tindakan ini tidak dapat dibatalkan!</small>
                </div>
            </div>

            <!-- Informasi User -->
            <?php
            $total_users = countData("SELECT * FROM users");
            $total_guru = countData("SELECT * FROM users WHERE role = 'guru'");
            $total_kepsek = countData("SELECT * FROM users WHERE role = 'kepala_sekolah'");
            $total_pengawas = countData("SELECT * FROM users WHERE role = 'pengawas'");
            $total_dinas = countData("SELECT * FROM users WHERE role = 'dinas'");
            $total_admin = countData("SELECT * FROM users WHERE role = 'admin'");
            ?>
            
            <div class="summary">
                <div class="item total">
                    <div class="number"><?php echo $total_users; ?></div>
                    <div class="label">👥 Total User</div>
                </div>
                <div class="item success">
                    <div class="number"><?php echo $total_guru; ?></div>
                    <div class="label">👨‍🏫 Guru</div>
                </div>
                <div class="item">
                    <div class="number"><?php echo $total_kepsek; ?></div>
                    <div class="label">👔 Kepala Sekolah</div>
                </div>
                <div class="item">
                    <div class="number"><?php echo $total_pengawas; ?></div>
                    <div class="label">🔍 Pengawas</div>
                </div>
                <div class="item">
                    <div class="number"><?php echo $total_dinas; ?></div>
                    <div class="label">📊 Dinas</div>
                </div>
                <div class="item">
                    <div class="number"><?php echo $total_admin; ?></div>
                    <div class="label">⚙️ Admin</div>
                </div>
            </div>

            <!-- Tabel Preview User -->
            <div class="table-container">
                <h3 style="margin-top:0;">👥 Daftar User</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $users = fetchAll("SELECT id, name, email, role FROM users ORDER BY role, name LIMIT 20");
                        foreach ($users as $user):
                            $role_label = [
                                'admin' => '⚙️ Admin',
                                'guru' => '👨‍🏫 Guru',
                                'kepala_sekolah' => '👔 Kepala Sekolah',
                                'pengawas' => '🔍 Pengawas',
                                'dinas' => '📊 Dinas'
                            ];
                            $role_display = $role_label[$user['role']] ?? $user['role'];
                        ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><span class="role-badge role-<?php echo $user['role']; ?>"><?php echo $role_display; ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if ($total_users > 20): ?>
                        <tr>
                            <td colspan="4" style="text-align:center; color:#888;">
                                ... dan <?php echo $total_users - 20; ?> user lainnya
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Tombol Aksi -->
            <div class="btn-group">
                <form method="POST" onsubmit="return confirmReset()">
                    <input type="hidden" name="reset" value="yes">
                    <button type="submit" class="btn btn-danger">🔑 Reset Semua Password</button>
                </form>
                <a href="login.php" class="btn btn-secondary">🔐 Login</a>
                <a href="index.php" class="btn btn-secondary">🏠 Beranda</a>
            </div>

            <div class="info-box">
                <span class="icon">ℹ️</span>
                <div class="text">
                    <strong>Informasi:</strong>
                    <small>Password baru untuk SEMUA user adalah: <strong>password123</strong></small>
                </div>
            </div>

            <script>
                function confirmReset() {
                    return confirm('⚠️ PERINGATAN!\n\nAnda akan mereset SEMUA password user menjadi password123.\nTindakan ini tidak dapat dibatalkan!\n\nApakah Anda yakin ingin melanjutkan?');
                }
            </script>

            <?php
        }
        ?>

        <div class="footer">
            <p>&copy; 2026 Pusat Perangkat Pembelajaran - Reset Password Tool</p>
            <p style="font-size: 11px; color: #aaa;">Script ini akan mereset semua password user menjadi password123</p>
        </div>
    </div>
</body>
</html>