<?php
// register.php - Halaman Pendaftaran Akun Baru
session_start();

// Jika sudah login, redirect sesuai role
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'];
    if ($role == 'admin') header('Location: admin_dashboard.php');
    elseif ($role == 'guru') header('Location: guru_dashboard.php');
    elseif ($role == 'kepala_sekolah') header('Location: kepsek_approve.php');
    elseif ($role == 'pengawas') header('Location: pengawas_approve.php');
    elseif ($role == 'dinas') header('Location: dinas_monitoring.php');
    exit;
}

include_once 'config/database.php';

$error = '';
$success = '';

// Proses registrasi (ditutup untuk umum)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $show_modal = true;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Pusat Perangkat Pembelajaran</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/website_perangkat/public/css/style.css">
    <style>
        /* ============================================
           REGISTER PAGE
        ============================================ */
        body {
            background: #f0f4f8;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            font-family: 'Inter', sans-serif;
        }

        .register-wrapper {
            display: flex;
            width: 900px;
            max-width: 100%;
            min-height: 600px;
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.10);
            overflow: hidden;
            animation: slideUp 0.6s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ============================================
           LEFT PANEL - BRANDING
        ============================================ */
        .register-left {
            width: 40%;
            background: linear-gradient(135deg, #0f2b5c 0%, #1a4a8a 50%, #2563eb 100%);
            padding: 40px 30px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .register-left::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -30%;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.04);
            border-radius: 50%;
        }

        .register-left::after {
            content: '';
            position: absolute;
            bottom: -20%;
            left: -20%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50%;
        }

        .register-left .brand {
            position: relative;
            z-index: 1;
        }

        .register-left .brand .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .register-left .brand .logo .logo-icon {
            width: 44px;
            height: 44px;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #60a5fa;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .register-left .brand .logo .logo-text h1 {
            color: white;
            font-size: 16px;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }

        .register-left .brand .logo .logo-text p {
            color: rgba(255, 255, 255, 0.5);
            font-size: 10px;
            margin: 0;
        }

        .register-left .brand .tagline {
            color: white;
            margin-top: 10px;
        }

        .register-left .brand .tagline h2 {
            font-size: 24px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 10px;
        }

        .register-left .brand .tagline h2 .highlight {
            color: #60a5fa;
        }

        .register-left .brand .tagline p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 13px;
            line-height: 1.7;
        }

        .register-left .brand .info-list {
            margin-top: 16px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .register-left .brand .info-list .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 12px;
        }

        .register-left .brand .info-list .info-item i {
            color: #60a5fa;
            width: 16px;
            font-size: 12px;
        }

        /* Gambar di bawah info list */
        .register-left .brand .info-image {
            margin-top: 16px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        }

        .register-left .brand .info-image img {
            width: 100%;
            height: auto;
            display: block;
            border-radius: 12px;
            transition: transform 0.3s ease;
        }

        .register-left .brand .info-image img:hover {
            transform: scale(1.02);
        }

        .register-left .footer-left {
            position: relative;
            z-index: 1;
            color: rgba(255, 255, 255, 0.35);
            font-size: 10px;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            line-height: 1.6;
        }

        .register-left .footer-left strong {
            color: rgba(255, 255, 255, 0.6);
        }

        /* ============================================
           RIGHT PANEL - REGISTER FORM
        ============================================ */
        .register-right {
            width: 60%;
            padding: 40px 35px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: white;
        }

        .register-right .register-header {
            margin-bottom: 25px;
        }

        .register-right .register-header .welcome {
            font-size: 12px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        .register-right .register-header h2 {
            font-size: 26px;
            font-weight: 700;
            color: #0f2b5c;
            margin: 4px 0 6px 0;
        }

        .register-right .register-header p {
            color: #6b7280;
            font-size: 14px;
        }

        /* Info Box */
        .info-box-register {
            background: #e8f0fe;
            padding: 16px 20px;
            border-radius: 10px;
            border-left: 4px solid #2563eb;
            margin-bottom: 25px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .info-box-register .icon {
            font-size: 20px;
            color: #2563eb;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .info-box-register .text {
            font-size: 13px;
            color: #4a5568;
            line-height: 1.6;
        }

        .info-box-register .text strong {
            color: #0f2b5c;
        }

        .info-box-register .text ul {
            margin: 6px 0 0 20px;
            padding: 0;
        }

        .info-box-register .text ul li {
            margin-bottom: 4px;
        }

        /* Form disabled */
        .form-disabled {
            opacity: 0.6;
            pointer-events: none;
            cursor: not-allowed;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            font-size: 13px;
            color: #4a5568;
            margin-bottom: 4px;
        }

        .form-group label .required {
            color: #dc3545;
        }

        .form-group .input-wrapper {
            position: relative;
        }

        .form-group .input-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 16px;
            z-index: 2;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 11px 15px 11px 45px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f9fafb;
            font-family: 'Inter', sans-serif;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #2563eb;
            outline: none;
            background: white;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.08);
        }

        .form-group .help-text {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 4px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .btn-register {
            width: 100%;
            background: #9ca3af;
            color: white;
            padding: 13px;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: not-allowed;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            opacity: 0.7;
        }

        .btn-register i {
            font-size: 16px;
        }

        .login-link {
            text-align: center;
            margin-top: 18px;
            color: #6b7280;
            font-size: 14px;
        }

        .login-link a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .register-footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 14px;
            border-top: 1px solid #f3f4f6;
            font-size: 11px;
            color: #9ca3af;
            line-height: 1.6;
        }

        .register-footer strong {
            color: #4a5568;
        }

        /* ============================================
           MODAL NOTIFIKASI
        ============================================ */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(4px);
            animation: fadeIn 0.3s ease;
        }

        .modal-overlay.active {
            display: flex;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .modal-box {
            background: white;
            border-radius: 20px;
            max-width: 520px;
            width: 95%;
            padding: 35px 30px 30px 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            animation: slideDown 0.4s ease;
            position: relative;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-box .modal-icon {
            text-align: center;
            font-size: 56px;
            margin-bottom: 16px;
        }

        .modal-box .modal-icon .icon-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #fef3c7;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .modal-box .modal-icon .icon-circle i {
            color: #d97706;
            font-size: 36px;
        }

        .modal-box h3 {
            font-size: 20px;
            font-weight: 700;
            color: #0f2b5c;
            text-align: center;
            margin-bottom: 12px;
        }

        .modal-box .modal-body {
            color: #4a5568;
            font-size: 14px;
            line-height: 1.8;
            margin-bottom: 20px;
        }

        .modal-box .modal-body ul {
            margin: 10px 0 0 20px;
            padding: 0;
        }

        .modal-box .modal-body ul li {
            margin-bottom: 6px;
        }

        .modal-box .modal-body .highlight-text {
            background: #f8fafc;
            padding: 12px 16px;
            border-radius: 8px;
            border-left: 3px solid #2563eb;
            margin: 12px 0;
            font-size: 13px;
        }

        .modal-box .modal-body .highlight-text i {
            color: #2563eb;
            margin-right: 8px;
        }

        .btn-close-modal {
            width: 100%;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 12px;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-close-modal:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(37, 99, 235, 0.35);
        }

        .btn-close-modal i {
            font-size: 16px;
        }

        /* ============================================
           RESPONSIVE
        ============================================ */
        @media (max-width: 860px) {
            .register-wrapper {
                flex-direction: column;
                max-width: 480px;
                min-height: auto;
                border-radius: 16px;
            }

            .register-left {
                width: 100%;
                padding: 25px 20px;
                min-height: 200px;
            }

            .register-left .brand .tagline h2 {
                font-size: 20px;
            }

            .register-left .brand .info-list {
                display: none;
            }

            .register-left .brand .info-image {
                display: none;
            }

            .register-right {
                width: 100%;
                padding: 25px 20px;
            }

            .register-right .register-header h2 {
                font-size: 20px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .register-wrapper {
                max-width: 100%;
                border-radius: 12px;
            }

            .register-left {
                padding: 18px 16px;
                min-height: 160px;
            }

            .register-left .brand .logo .logo-icon {
                width: 36px;
                height: 36px;
                font-size: 18px;
            }

            .register-left .brand .logo .logo-text h1 {
                font-size: 15px;
            }

            .register-left .brand .tagline h2 {
                font-size: 17px;
            }

            .register-left .brand .tagline p {
                font-size: 12px;
            }

            .register-left .brand .info-image {
                display: none;
            }

            .register-right {
                padding: 18px 14px;
            }

            .register-right .register-header h2 {
                font-size: 17px;
            }

            .modal-box {
                padding: 25px 18px;
            }

            .modal-box h3 {
                font-size: 17px;
            }

            .modal-box .modal-body {
                font-size: 13px;
            }

            .info-box-register {
                padding: 12px 14px;
                font-size: 12px;
            }

            .form-group input {
                font-size: 13px;
                padding: 9px 12px 9px 38px;
            }
        }
    </style>
</head>
<body>
    <div class="register-wrapper">
        <!-- ========================================== -->
        <!-- LEFT PANEL - BRANDING -->
        <!-- ========================================== -->
        <div class="register-left">
            <div class="brand">
                <div class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="logo-text">
                        <h1>Pusat Perangkat Pembelajaran</h1>
                        <p>Dinas Pendidikan dan Kebudayaan Kabupaten Ende</p>
                    </div>
                </div>
                <div class="tagline">
                    <h2>Bergabung dengan <span class="highlight">Sistem</span> Kami</h2>
                    <p>Platform terpadu untuk pengelolaan perangkat pembelajaran</p>
                </div>
                <div class="info-list">
                    <div class="info-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Akses perangkat pembelajaran terverifikasi</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Kolaborasi guru, kepala sekolah, pengawas, dan dinas pendidikan</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Monitoring dan evaluasi terintegrasi</span>
                    </div>
                </div>
                <!-- Gambar Apel -->
                <div class="info-image">
                    <img src="img/apel.jpg" alt="Apel - Simbol Pendidikan" loading="lazy">
                </div>
            </div>
            <div class="footer-left">
                &copy; <?php echo date('Y'); ?> <strong>Dinas Pendidikan dan Kebudayaan Kabupaten Ende</strong><br>
                Bidang Ketenagaan
            </div>
        </div>

        <!-- ========================================== -->
        <!-- RIGHT PANEL - REGISTER FORM -->
        <!-- ========================================== -->
        <div class="register-right">
            <div class="register-header">
                <div class="welcome">Pendaftaran Akun</div>
                <h2>Daftar Akun Baru</h2>
                <p>Silakan daftar untuk mengakses sistem</p>
            </div>

            <!-- Info Box -->
            <div class="info-box-register">
                <div class="icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="text">
                    <strong>Informasi Pendaftaran:</strong>
                    <ul>
                        <li>Pendaftaran akun baru bagi <strong>Guru</strong> wajib melalui Kepala Sekolah masing-masing</li>
                        <li>Penambahan <strong>Sekolah Binaan</strong> bagi Pengawas melalui Admin Dinas</li>
                        <li>Untuk bantuan, silakan hubungi operator sekolah atau admin dinas</li>
                    </ul>
                </div>
            </div>

            <!-- Form Registrasi (Disabled) -->
            <form method="POST" class="form-disabled">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nama Lengkap <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <i class="fas fa-user"></i>
                            <input type="text" placeholder="Nama lengkap" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Email <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope"></i>
                            <input type="email" placeholder="email@sekolah.com" disabled>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Password <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" placeholder="Minimal 6 karakter" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Konfirmasi Password <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" placeholder="Ulangi password" disabled>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Role / Jabatan <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <i class="fas fa-user-tag"></i>
                            <select disabled>
                                <option value="">-- Pilih Role --</option>
                                <option value="guru">👨‍🏫 Guru</option>
                                <option value="kepala_sekolah">👔 Kepala Sekolah</option>
                                <option value="pengawas">🔍 Pengawas</option>
                                <option value="dinas">📊 Dinas</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Sekolah / Instansi</label>
                        <div class="input-wrapper">
                            <i class="fas fa-school"></i>
                            <input type="text" placeholder="Nama sekolah" disabled>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn-register" onclick="showModal()">
                    <i class="fas fa-user-plus"></i> Daftar Sekarang
                </button>
            </form>

            <div class="login-link">
                Sudah punya akun? <a href="login.php">Login di sini</a>
            </div>

            <div class="register-footer">
                &copy; <?php echo date('Y'); ?> <strong>Dinas Pendidikan dan Kebudayaan Kabupaten Ende</strong><br>
                Bidang Ketenagaan
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL NOTIFIKASI -->
    <!-- ========================================== -->
    <div class="modal-overlay" id="notificationModal">
        <div class="modal-box">
            <div class="modal-icon">
                <div class="icon-circle">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
            <h3>Perhatian!</h3>
            <div class="modal-body">
                <p><strong>Pendaftaran akun baru bagi guru wajib melalui akun Kepala Sekolah masing-masing.</strong></p>
                
                <div class="highlight-text">
                    <i class="fas fa-user-tie"></i> 
                    <strong>Untuk Guru:</strong> Silakan hubungi Kepala Sekolah untuk pendaftaran akun.
                </div>

                <div class="highlight-text" style="border-left-color: #d97706;">
                    <i class="fas fa-user-shield"></i> 
                    <strong>Untuk Pengawas:</strong> Penambahan Sekolah Binaan, silahkan hubungi Admin Dinas.
                </div>

                <p style="margin-top:12px; font-size:13px; color:#6b7280;">
                    <i class="fas fa-info-circle"></i> 
                    Jika Anda adalah Kepala Sekolah atau Admin Dinas, silakan login menggunakan akun yang telah disediakan.
                </p>
            </div>
            <button class="btn-close-modal" onclick="closeModal()">
                <i class="fas fa-check-circle"></i> Saya Mengerti, Tutup
            </button>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SCRIPT -->
    <!-- ========================================== -->
    <script>
        function showModal() {
            document.getElementById('notificationModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('notificationModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // Tutup modal jika klik di luar
        document.getElementById('notificationModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Tutup modal dengan tombol ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        // Tampilkan modal saat halaman dimuat (jika ada parameter)
        <?php if (isset($show_modal) && $show_modal): ?>
        window.onload = function() {
            showModal();
        };
        <?php endif; ?>
    </script>
</body>
</html>