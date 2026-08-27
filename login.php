<?php
// login.php - Halaman Login Profesional dengan Slider & Logo
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = escape($_POST['email']);
    $password = $_POST['password'];
    $login_type = $_POST['login_type'] ?? 'guru';
    
    $user = cekLogin($email, $password);
    
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['sekolah_id'] = $user['sekolah_id'];
        $_SESSION['sekolah'] = $user['sekolah'] ?? $user['sekolah_nama'] ?? '';
        
        $role = $user['role'];
        if ($role == 'admin') {
            header('Location: admin_dashboard.php');
        } elseif ($role == 'guru') {
            header('Location: guru_dashboard.php');
        } elseif ($role == 'kepala_sekolah') {
            header('Location: kepsek_approve.php');
        } elseif ($role == 'pengawas') {
            header('Location: pengawas_approve.php');
        } elseif ($role == 'dinas') {
            header('Location: dinas_monitoring.php');
        }
        exit;
    } else {
        $error = '❌ Email atau password salah!';
    }
}

// Data slider dengan gambar
$slides = [
    [
        'image' => 'img/guru.jpg',
        'title' => 'Guru Kreatif',
        'subtitle' => 'Mencerdaskan Generasi Bangsa',
        'desc' => 'Membuat dan mengupload perangkat pembelajaran berkualitas',
        'quote' => '"Guru adalah pahlawan tanpa tanda jasa"',
        'emoji' => '👨‍🏫'
    ],
    [
        'image' => 'img/kepsek.jpg',
        'title' => 'Kepala Sekolah',
        'subtitle' => 'Pemimpin Pembelajaran',
        'desc' => 'Koreksi dan legalisasi dokumen perangkat pembelajaran',
        'quote' => '"Kepala sekolah adalah kunci keberhasilan pendidikan"',
        'emoji' => '👔'
    ],
    [
        'image' => 'img/pengawas.jpg',
        'title' => 'Pengawas Sekolah',
        'subtitle' => 'Pengawas Mutu Pendidikan',
        'desc' => 'Supervisi dan verifikasi dokumen perangkat pembelajaran',
        'quote' => '"Pengawas adalah mitra guru dalam meningkatkan mutu"',
        'emoji' => '🔍'
    ],
    [
        'image' => 'img/dinas.jpg',
        'title' => 'Dinas Pendidikan',
        'subtitle' => 'Penggerak Pendidikan Daerah',
        'desc' => 'Monitoring dan evaluasi perangkat pembelajaran',
        'quote' => '"Dinas pendidikan adalah garda terdepan pendidikan"',
        'emoji' => '📊'
    ],
    [
        'image' => 'img/siswa.jpg',
        'title' => 'Siswa Aktif',
        'subtitle' => 'Generasi Emas Indonesia',
        'desc' => 'Mengakses dan mempelajari perangkat pembelajaran',
        'quote' => '"Siswa adalah investasi terbesar untuk masa depan bangsa"',
        'emoji' => '🎓'
    ]
];

// Data role
$roles = [
    'guru' => ['label' => 'Guru', 'icon' => 'fa-chalkboard-teacher', 'color' => '#059669'],
    'kepala_sekolah' => ['label' => 'Kepala Sekolah', 'icon' => 'fa-user-tie', 'color' => '#1a237e'],
    'admin' => ['label' => 'Admin / Operator', 'icon' => 'fa-user-cog', 'color' => '#2563eb'],
    'tata_usaha' => ['label' => 'Tata Usaha', 'icon' => 'fa-users-cog', 'color' => '#d97706']
];

$selected_role = isset($_GET['role']) ? $_GET['role'] : 'guru';
if (!isset($roles[$selected_role])) {
    $selected_role = 'guru';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Manajemen Perangkat Pembelajaran</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/website_perangkat/public/css/style.css">
    <style>
        /* ============================================
           LOGIN PAGE - SIMANTAP STYLE
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

        .login-wrapper {
            display: flex;
            width: 1100px;
            max-width: 100%;
            min-height: 650px;
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
           LEFT PANEL - SLIDER DENGAN TEKS DI ATAS
        ============================================ */
        .login-left {
            width: 45%;
            background: linear-gradient(135deg, #0f2b5c 0%, #1a4a8a 50%, #2563eb 100%);
            padding: 25px 25px 20px 25px;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }

        .login-left::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -30%;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.04);
            border-radius: 50%;
        }

        .login-left::after {
            content: '';
            position: absolute;
            bottom: -20%;
            left: -20%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50%;
        }

        /* Brand / Teks di Atas Slider */
        .login-left .brand-top {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
            flex-shrink: 0;
        }

        .login-left .brand-top .logo-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #60a5fa;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .login-left .brand-top .logo-text h1 {
            color: white;
            font-size: 16px;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .login-left .brand-top .logo-text p {
            color: rgba(255, 255, 255, 0.5);
            font-size: 10px;
            margin: 0;
        }

        /* Tagline di atas slider */
        .login-left .tagline {
            position: relative;
            z-index: 2;
            flex-shrink: 0;
            margin-bottom: 8px;
        }

        .login-left .tagline .welcome-text {
            color: rgba(255, 255, 255, 0.7);
            font-size: 13px;
            font-weight: 400;
            letter-spacing: 0.5px;
        }

        .login-left .tagline .welcome-text strong {
            color: white;
            font-weight: 600;
        }

        .login-left .tagline .sub-text {
            color: rgba(255, 255, 255, 0.5);
            font-size: 11px;
            margin-top: 2px;
        }

        /* Slider Container - Tanpa Teks di dalamnya */
        .slider-container {
            position: relative;
            z-index: 1;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 250px;
        }

        .slider-wrapper {
            width: 100%;
            height: 320px;
            position: relative;
            overflow: hidden;
            border-radius: 14px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
        }

        .slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            transform: scale(1.05);
            border-radius: 14px;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .slide.active {
            opacity: 1;
            transform: scale(1);
        }

        /* Hanya overlay tipis tanpa teks */
        .slide::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(0deg, rgba(0, 0, 0, 0.3) 0%, rgba(0, 0, 0, 0.05) 50%, rgba(0, 0, 0, 0.02) 100%);
            border-radius: 14px;
            z-index: 1;
        }

        /* Slide dots di dalam gambar */
        .slide .slide-dots {
            position: absolute;
            bottom: 12px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2;
            display: flex;
            gap: 6px;
        }

        .slide .slide-dots .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.4);
            transition: all 0.3s;
        }

        .slide .slide-dots .dot.active {
            background: white;
            width: 20px;
            border-radius: 3px;
        }

        /* Slide Navigation di bawah slider */
        .slider-nav {
            position: relative;
            z-index: 2;
            display: flex;
            gap: 10px;
            margin-top: 10px;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .slider-nav .nav-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.15);
            background: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.6);
            cursor: pointer;
            transition: all 0.3s;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .slider-nav .nav-btn:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
            color: white;
        }

        .slider-indicators {
            display: flex;
            gap: 5px;
            align-items: center;
        }

        .slider-indicators .indicator {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            padding: 0;
        }

        .slider-indicators .indicator.active {
            background: white;
            width: 20px;
            border-radius: 3px;
        }

        .slider-indicators .indicator:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        /* Footer Kiri */
        .login-left .footer-left {
            position: relative;
            z-index: 1;
            color: rgba(255, 255, 255, 0.35);
            font-size: 10px;
            text-align: center;
            padding-top: 10px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            margin-top: 10px;
            line-height: 1.5;
            flex-shrink: 0;
        }

        .login-left .footer-left .version {
            display: inline-block;
            background: rgba(255, 255, 255, 0.05);
            padding: 1px 10px;
            border-radius: 10px;
            font-size: 10px;
            color: rgba(255, 255, 255, 0.4);
        }

        /* ============================================
           RIGHT PANEL - LOGIN FORM
        ============================================ */
        .login-right {
            width: 55%;
            padding: 40px 35px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: white;
        }

        .login-right .login-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .login-right .login-header .logo-img {
            max-width: 80px;
            max-height: 80px;
            margin: 0 auto 12px;
            display: block;
            border-radius: 12px;
        }

        .login-right .login-header .welcome {
            font-size: 12px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        .login-right .login-header h2 {
            font-size: 26px;
            font-weight: 700;
            color: #0f2b5c;
            margin: 4px 0 2px 0;
        }

        .login-right .login-header p {
            color: #6b7280;
            font-size: 14px;
            margin: 0;
        }

        /* Role Selector */
        .role-selector {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 25px;
        }

        .role-selector .role-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
            text-align: left;
        }

        .role-selector .role-btn:hover {
            border-color: #2563eb;
            background: #f8faff;
        }

        .role-selector .role-btn.active {
            border-color: #2563eb;
            background: #eff6ff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08);
        }

        .role-selector .role-btn .icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
        }

        .role-selector .role-btn .icon.blue {
            background: #dbeafe;
            color: #2563eb;
        }
        .role-selector .role-btn .icon.green {
            background: #d1fae5;
            color: #059669;
        }
        .role-selector .role-btn .icon.purple {
            background: #ede9fe;
            color: #7c3aed;
        }
        .role-selector .role-btn .icon.orange {
            background: #fef3c7;
            color: #d97706;
        }

        .role-selector .role-btn .role-label {
            font-size: 13px;
            font-weight: 500;
            color: #1f2937;
        }

        .role-selector .role-btn .role-sub {
            font-size: 10px;
            color: #9ca3af;
            font-weight: 400;
            display: block;
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

        .form-group input {
            width: 100%;
            padding: 11px 15px 11px 45px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f9fafb;
            font-family: 'Inter', sans-serif;
        }

        .form-group input:focus {
            border-color: #2563eb;
            outline: none;
            background: white;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.08);
        }

        .form-group .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            font-size: 16px;
            padding: 4px;
            z-index: 2;
        }

        .form-group .toggle-password:hover {
            color: #4a5568;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
            font-size: 13px;
        }

        .form-options .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #4a5568;
            cursor: pointer;
        }

        .form-options .remember input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #2563eb;
            cursor: pointer;
        }

        .form-options .forgot {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
        }

        .form-options .forgot:hover {
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 13px;
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
            gap: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(37, 99, 235, 0.35);
        }

        .alert-error {
            background: #fef2f2;
            color: #dc2626;
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 18px;
            border-left: 4px solid #dc2626;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-error i {
            font-size: 18px;
        }

        .register-link {
            text-align: center;
            margin-top: 18px;
            color: #6b7280;
            font-size: 14px;
        }

        .register-link a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .login-footer {
            text-align: center;
            margin-top: 18px;
            padding-top: 14px;
            border-top: 1px solid #f3f4f6;
            font-size: 11px;
            color: #9ca3af;
            line-height: 1.6;
        }

        .login-footer strong {
            color: #4a5568;
        }

        /* ============================================
           RESPONSIVE
        ============================================ */
        @media (max-width: 960px) {
            .login-wrapper {
                flex-direction: column;
                max-width: 480px;
                min-height: auto;
                border-radius: 16px;
            }

            .login-left {
                width: 100%;
                padding: 18px 16px;
                min-height: 230px;
            }

            .slider-wrapper {
                height: 220px;
            }

            .login-left .brand-top .logo-text h1 {
                font-size: 14px;
            }

            .login-right {
                width: 100%;
                padding: 25px 20px;
            }

            .login-right .login-header h2 {
                font-size: 22px;
            }

            .login-right .login-header .logo-img {
                max-width: 60px;
                max-height: 60px;
            }

            .role-selector {
                grid-template-columns: 1fr 1fr;
            }

            .login-left .tagline .welcome-text {
                font-size: 12px;
            }
            .login-left .tagline .sub-text {
                font-size: 10px;
            }
        }

        @media (max-width: 480px) {
            .login-wrapper {
                max-width: 100%;
                border-radius: 12px;
            }

            .login-left {
                padding: 12px 12px;
                min-height: 180px;
            }

            .slider-wrapper {
                height: 160px;
                border-radius: 10px;
            }

            .login-left .brand-top .logo-icon {
                width: 32px;
                height: 32px;
                font-size: 16px;
            }

            .login-left .brand-top .logo-text h1 {
                font-size: 12px;
            }

            .login-left .brand-top .logo-text p {
                font-size: 8px;
            }

            .login-left .tagline .welcome-text {
                font-size: 10px;
            }
            .login-left .tagline .sub-text {
                font-size: 9px;
            }

            .login-right {
                padding: 16px 12px;
            }

            .login-right .login-header h2 {
                font-size: 18px;
            }

            .login-right .login-header .logo-img {
                max-width: 50px;
                max-height: 50px;
            }

            .role-selector {
                grid-template-columns: 1fr 1fr;
                gap: 5px;
            }

            .role-selector .role-btn {
                padding: 6px 8px;
                font-size: 11px;
            }
            .role-selector .role-btn .icon {
                width: 26px;
                height: 26px;
                font-size: 11px;
            }
            .role-selector .role-btn .role-label {
                font-size: 10px;
            }
            .role-selector .role-btn .role-sub {
                font-size: 8px;
            }

            .form-group input {
                font-size: 13px;
                padding: 9px 12px 9px 36px;
            }

            .btn-login {
                font-size: 14px;
                padding: 11px;
            }

            .slider-nav .nav-btn {
                width: 26px;
                height: 26px;
                font-size: 10px;
            }

            .slider-indicators .indicator {
                width: 5px;
                height: 5px;
            }
            .slider-indicators .indicator.active {
                width: 14px;
            }

            .login-left .footer-left {
                font-size: 8px;
                padding-top: 8px;
                margin-top: 8px;
            }

            .slide .slide-dots .dot {
                width: 5px;
                height: 5px;
            }
            .slide .slide-dots .dot.active {
                width: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <!-- ========================================== -->
        <!-- LEFT PANEL - SLIDER DENGAN TEKS DI ATAS -->
        <!-- ========================================== -->
        <div class="login-left">
            <!-- Brand Top -->
            <div class="brand-top">
                <div class="logo-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="logo-text">
                    <h1>Sistem Perangkat Pembelajaran</h1>
                    <p>Dinas Pendidikan dan Kebudayaan</p>
                </div>
            </div>

            <!-- Tagline / Teks di atas slider -->
            <div class="tagline">
                <div class="welcome-text">
                    Selamat datang di <strong>Sistem Manajemen Perangkat Pembelajaran</strong>
                </div>
                <div class="sub-text">
                    Platform terpadu untuk guru, kepala sekolah, dan pengawas
                </div>
            </div>

            <!-- Slider Gambar (tanpa teks di dalam) -->
            <div class="slider-container">
                <div class="slider-wrapper" id="sliderWrapper">
                    <?php foreach ($slides as $index => $slide):
                        $image_path = $slide['image'];
                        if (!file_exists($image_path)) {
                            $bg_style = 'background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);';
                        } else {
                            $bg_style = 'background-image: url(\'' . $slide['image'] . '\'); background-size: cover; background-position: center;';
                        }
                    ?>
                        <div class="slide <?php echo $index === 0 ? 'active' : ''; ?>" 
                             data-index="<?php echo $index; ?>"
                             style="<?php echo $bg_style; ?>">
                            <!-- Dots di dalam slide -->
                            <div class="slide-dots">
                                <?php for ($i = 0; $i < count($slides); $i++): ?>
                                    <span class="dot <?php echo $i === $index ? 'active' : ''; ?>"></span>
                                <?php endfor; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Navigasi Slider -->
            <div class="slider-nav">
                <button class="nav-btn" id="prevSlide" aria-label="Previous">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="slider-indicators" id="sliderIndicators">
                    <?php for ($i = 0; $i < count($slides); $i++): ?>
                        <button class="indicator <?php echo $i === 0 ? 'active' : ''; ?>" 
                                data-index="<?php echo $i; ?>" 
                                aria-label="Slide <?php echo $i + 1; ?>"></button>
                    <?php endfor; ?>
                </div>
                <button class="nav-btn" id="nextSlide" aria-label="Next">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            <!-- Footer Kiri -->
            <div class="footer-left">
                <span class="version">v1.0.4</span>
                <span style="margin-left: 10px;">&copy; <?php echo date('Y'); ?></span>
                <br>
                Dinas Pendidikan dan Kebudayaan<br>
                Bidang Ketenagaan
            </div>
        </div>

        <!-- ========================================== -->
        <!-- RIGHT PANEL - LOGIN FORM -->
        <!-- ========================================== -->
        <div class="login-right">
            <div class="login-header">
                <?php 
                $logo_path = 'logo/logo.jpg';
                if (file_exists($logo_path)): 
                ?>
                    <img src="<?php echo $logo_path; ?>" alt="Logo" class="logo-img">
                <?php endif; ?>
                <div class="welcome">Selamat Datang di</div>
                <h2>SIMANTAP</h2>
                <p>Sistem Manajemen Perangkat Pembelajaran Terpadu</p>
            </div>

            <?php if ($error): ?>
                <div class="alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- Role Selector -->
            <div class="role-selector" id="roleSelector">
                <?php foreach ($roles as $key => $r): 
                    $is_active = ($selected_role == $key);
                    $icon_color = '';
                    if ($key == 'guru') $icon_color = 'green';
                    elseif ($key == 'kepala_sekolah') $icon_color = 'purple';
                    elseif ($key == 'admin') $icon_color = 'blue';
                    elseif ($key == 'tata_usaha') $icon_color = 'orange';
                ?>
                    <button class="role-btn <?php echo $is_active ? 'active' : ''; ?>" 
                            data-role="<?php echo $key; ?>" 
                            onclick="selectRole('<?php echo $key; ?>')">
                        <span class="icon <?php echo $icon_color; ?>">
                            <i class="fas <?php echo $r['icon']; ?>"></i>
                        </span>
                        <span>
                            <span class="role-label"><?php echo $r['label']; ?></span>
                            <span class="role-sub">Login sebagai <?php echo strtolower($r['label']); ?></span>
                        </span>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Form Login -->
            <form method="POST" id="loginForm" autocomplete="off">
                <input type="hidden" name="login_type" id="loginType" value="<?php echo $selected_role; ?>">

                <div class="form-group">
                    <label>📧 Masukkan Email</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" id="email" required placeholder="Masukkan email Anda" 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>🔑 Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="password" required placeholder="Masukkan password">
                        <button type="button" class="toggle-password" onclick="togglePassword()" aria-label="Toggle password">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="form-options">
                    <label class="remember">
                        <input type="checkbox" name="remember">
                        <span>Ingat saya</span>
                    </label>
                    <a href="#" class="forgot">Lupa password?</a>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Masuk Ke SIMANTAP
                </button>
            </form>

            <div class="register-link">
                Belum Terdaftar? <a href="register.php">Pendaftaran Guru Baru</a>
            </div>

            <div class="login-footer">
                &copy; <?php echo date('Y'); ?> <strong>Dinas Pendidikan dan Kebudayaan</strong><br>
                Bidang Ketenagaan
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SCRIPT -->
    <!-- ========================================== -->
    <script>
        // ============================================
        // SLIDER
        // ============================================
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slide');
        const indicators = document.querySelectorAll('.indicator');
        const totalSlides = slides.length;
        let autoplayInterval;

        function goToSlide(index) {
            if (index < 0) index = totalSlides - 1;
            if (index >= totalSlides) index = 0;

            slides.forEach((slide, i) => {
                slide.classList.remove('active');
                if (i === index) slide.classList.add('active');
            });

            indicators.forEach((ind, i) => {
                ind.classList.remove('active');
                if (i === index) ind.classList.add('active');
            });

            document.querySelectorAll('.slide .dot').forEach((dot, i) => {
                dot.classList.remove('active');
                if (i === index) dot.classList.add('active');
            });

            currentSlide = index;
        }

        function nextSlide() {
            goToSlide(currentSlide + 1);
        }

        function prevSlide() {
            goToSlide(currentSlide - 1);
        }

        function startAutoplay() {
            stopAutoplay();
            autoplayInterval = setInterval(nextSlide, 4000);
        }

        function stopAutoplay() {
            if (autoplayInterval) {
                clearInterval(autoplayInterval);
                autoplayInterval = null;
            }
        }

        document.getElementById('nextSlide').addEventListener('click', function() {
            stopAutoplay();
            nextSlide();
            startAutoplay();
        });

        document.getElementById('prevSlide').addEventListener('click', function() {
            stopAutoplay();
            prevSlide();
            startAutoplay();
        });

        indicators.forEach((indicator) => {
            indicator.addEventListener('click', function() {
                stopAutoplay();
                const index = parseInt(this.dataset.index);
                goToSlide(index);
                startAutoplay();
            });
        });

        document.querySelector('.slider-wrapper').addEventListener('mouseenter', stopAutoplay);
        document.querySelector('.slider-wrapper').addEventListener('mouseleave', startAutoplay);

        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                stopAutoplay();
                prevSlide();
                startAutoplay();
            } else if (e.key === 'ArrowRight') {
                stopAutoplay();
                nextSlide();
                startAutoplay();
            }
        });

        startAutoplay();

        // ============================================
        // ROLE SELECTOR
        // ============================================
        function selectRole(role) {
            document.querySelectorAll('.role-btn').forEach(btn => {
                btn.classList.remove('active');
                if (btn.dataset.role === role) {
                    btn.classList.add('active');
                }
            });

            document.getElementById('loginType').value = role;

            const emailInput = document.getElementById('email');
            const placeholders = {
                'guru': 'guru@sekolah.com',
                'kepala_sekolah': 'kepsek@sekolah.com',
                'admin': 'admin@sekolah.com',
                'tata_usaha': 'tu@sekolah.com'
            };
            emailInput.placeholder = placeholders[role] || 'Masukkan email Anda';
        }

        // ============================================
        // PASSWORD TOGGLE
        // ============================================
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                eyeIcon.className = 'fas fa-eye';
            }
        }

        // ============================================
        // KEYBOARD SHORTCUT
        // ============================================
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && !e.shiftKey) {
                if (e.key === '1') { e.preventDefault();
                    selectRole('guru'); } else if (e.key === '2') { e.preventDefault();
                    selectRole('kepala_sekolah'); } else if (e.key === '3') { e.preventDefault();
                    selectRole('admin'); } else if (e.key === '4') { e.preventDefault();
                    selectRole('tata_usaha'); }
            }
        });
    </script>
</body>
</html>