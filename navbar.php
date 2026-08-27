<?php
// navbar.php - Navigasi Global Profesional
// Digunakan di semua halaman dengan include_once 'navbar.php';
?>

<style>
    /* ============================================
       NAVBAR PROFESIONAL
    ============================================ */
    .navbar {
        background: white;
        border-radius: 16px;
        margin-bottom: 25px;
        padding: 0 25px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        border: 1px solid rgba(0,0,0,0.04);
        position: sticky;
        top: 20px;
        z-index: 999;
        backdrop-filter: blur(10px);
        background: rgba(255,255,255,0.95);
        transition: all 0.3s ease;
    }
    
    .navbar-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 70px;
        max-width: 1280px;
        margin: 0 auto;
    }
    
    /* ============================================
       BRAND / LOGO
    ============================================ */
    .navbar-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        flex-shrink: 0;
    }
    
    .navbar-brand .brand-icon {
        width: 42px;
        height: 42px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        transition: transform 0.3s ease;
    }
    
    .navbar-brand:hover .brand-icon {
        transform: scale(1.05) rotate(-5deg);
    }
    
    .navbar-brand .brand-text {
        display: flex;
        flex-direction: column;
    }
    
    .navbar-brand .brand-text .brand-title {
        font-size: 16px;
        font-weight: 700;
        color: #0f2b5c;
        letter-spacing: -0.3px;
        line-height: 1.2;
    }
    
    .navbar-brand .brand-text .brand-sub {
        font-size: 10px;
        color: #9ca3af;
        font-weight: 400;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }
    
    .navbar-brand .brand-text .brand-sub i {
        font-size: 8px;
        color: #667eea;
        margin: 0 4px;
    }
    
    /* ============================================
       MENU UTAMA
    ============================================ */
    .navbar-menu {
        display: flex;
        align-items: center;
        gap: 4px;
        list-style: none;
        margin: 0;
        padding: 0;
    }
    
    .navbar-menu li {
        position: relative;
    }
    
    .navbar-menu a {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 10px;
        color: #4a5568;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        white-space: nowrap;
    }
    
    .navbar-menu a i {
        font-size: 15px;
        color: #9ca3af;
        transition: color 0.3s ease;
    }
    
    .navbar-menu a:hover {
        background: #f3f4f6;
        color: #667eea;
        transform: translateY(-1px);
    }
    
    .navbar-menu a:hover i {
        color: #667eea;
    }
    
    .navbar-menu a.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 16px rgba(102, 126, 234, 0.25);
    }
    
    .navbar-menu a.active i {
        color: white;
    }
    
    .navbar-menu a.active::after {
        display: none;
    }
    
    /* Menu dengan dropdown */
    .navbar-menu .has-dropdown > a::after {
        content: '\f078';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        font-size: 8px;
        margin-left: 4px;
        opacity: 0.5;
        transition: transform 0.3s ease;
    }
    
    .navbar-menu .has-dropdown:hover > a::after {
        transform: rotate(180deg);
    }
    
    /* ============================================
       DROPDOWN
    ============================================ */
    .navbar-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        left: 50%;
        transform: translateX(-50%) translateY(8px);
        background: white;
        min-width: 220px;
        border-radius: 12px;
        box-shadow: 0 12px 48px rgba(0,0,0,0.12);
        padding: 8px;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(0,0,0,0.04);
    }
    
    .navbar-menu .has-dropdown:hover .navbar-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) translateY(0);
    }
    
    .navbar-dropdown a {
        padding: 8px 14px;
        border-radius: 8px;
        white-space: nowrap;
        font-size: 13px;
        color: #4a5568;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.2s ease;
    }
    
    .navbar-dropdown a:hover {
        background: #f3f4f6;
        color: #667eea;
    }
    
    .navbar-dropdown a i {
        font-size: 14px;
        width: 20px;
        text-align: center;
    }
    
    /* Menu Perangkat - Large Dropdown */
    .navbar-dropdown-large {
        position: absolute;
        top: calc(100% + 8px);
        left: 50%;
        transform: translateX(-50%) translateY(8px);
        background: white;
        min-width: 500px;
        border-radius: 12px;
        box-shadow: 0 12px 48px rgba(0,0,0,0.12);
        padding: 12px;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(0,0,0,0.04);
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4px;
    }
    
    .navbar-menu .has-dropdown:hover .navbar-dropdown-large {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) translateY(0);
    }
    
    .navbar-dropdown-large a {
        padding: 8px 14px;
        border-radius: 8px;
        white-space: nowrap;
        font-size: 12px;
        color: #4a5568;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.2s ease;
    }
    
    .navbar-dropdown-large a:hover {
        background: #f3f4f6;
        color: #667eea;
    }
    
    .navbar-dropdown-large a i {
        font-size: 14px;
        width: 20px;
        text-align: center;
    }
    
    /* ============================================
       USER PROFILE
    ============================================ */
    .navbar-profile {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-shrink: 0;
        position: relative;
    }
    
    .navbar-profile .profile-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 6px 12px 6px 6px;
        border-radius: 30px;
        border: 2px solid transparent;
        background: transparent;
        cursor: pointer;
        transition: all 0.3s ease;
        font-family: 'Inter', sans-serif;
        position: relative;
    }
    
    .navbar-profile .profile-btn:hover {
        border-color: #e5e7eb;
        background: #f8fafc;
    }
    
    .navbar-profile .profile-btn .avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 14px;
        flex-shrink: 0;
    }
    
    .navbar-profile .profile-btn .profile-info {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        line-height: 1.3;
    }
    
    .navbar-profile .profile-btn .profile-info .name {
        font-size: 13px;
        font-weight: 600;
        color: #0f2b5c;
    }
    
    .navbar-profile .profile-btn .profile-info .role {
        font-size: 10px;
        color: #9ca3af;
        font-weight: 400;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    
    .navbar-profile .profile-btn .profile-info .role .badge {
        display: inline-block;
        padding: 1px 8px;
        border-radius: 10px;
        font-size: 9px;
        font-weight: 600;
        margin-left: 4px;
    }
    
    .badge-guru { background: #d4edda; color: #155724; }
    .badge-kepsek { background: #cce5ff; color: #004085; }
    .badge-pengawas { background: #fff3cd; color: #856404; }
    .badge-dinas { background: #d1ecf1; color: #0c5460; }
    .badge-admin { background: #f8d7da; color: #721c24; }
    
    .navbar-profile .profile-btn .chevron {
        font-size: 10px;
        color: #9ca3af;
        transition: transform 0.3s ease;
        margin-left: 2px;
    }
    
    .navbar-profile .profile-btn:hover .chevron {
        transform: rotate(180deg);
    }
    
    /* Profile Dropdown */
    .profile-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        background: white;
        min-width: 240px;
        border-radius: 12px;
        box-shadow: 0 12px 48px rgba(0,0,0,0.12);
        padding: 8px;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(0,0,0,0.04);
        z-index: 1000;
    }
    
    .navbar-profile .profile-dropdown.open {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    
    .profile-dropdown .dropdown-header {
        padding: 10px 14px;
        border-bottom: 1px solid #f3f4f6;
        margin-bottom: 4px;
    }
    
    .profile-dropdown .dropdown-header .name {
        font-weight: 600;
        color: #0f2b5c;
        font-size: 14px;
    }
    
    .profile-dropdown .dropdown-header .email {
        font-size: 12px;
        color: #9ca3af;
    }
    
    .profile-dropdown a {
        padding: 8px 14px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #4a5568;
        text-decoration: none;
        font-size: 13px;
        transition: all 0.2s ease;
    }
    
    .profile-dropdown a:hover {
        background: #f3f4f6;
        color: #667eea;
    }
    
    .profile-dropdown a i {
        font-size: 14px;
        width: 20px;
        text-align: center;
        color: #9ca3af;
    }
    
    .profile-dropdown a:hover i {
        color: #667eea;
    }
    
    .profile-dropdown .divider {
        height: 1px;
        background: #f3f4f6;
        margin: 4px 8px;
    }
    
    /* ============================================
       HAMBURGER MENU (Mobile)
    ============================================ */
    .navbar-hamburger {
        display: none;
        flex-direction: column;
        gap: 5px;
        background: none;
        border: none;
        padding: 6px;
        cursor: pointer;
        width: 36px;
        height: 36px;
        justify-content: center;
        align-items: center;
        border-radius: 8px;
        transition: background 0.3s ease;
    }
    
    .navbar-hamburger:hover {
        background: #f3f4f6;
    }
    
    .navbar-hamburger span {
        display: block;
        width: 24px;
        height: 2.5px;
        background: #4a5568;
        border-radius: 2px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        transform-origin: center;
    }
    
    .navbar-hamburger.active span:nth-child(1) {
        transform: translateY(7.5px) rotate(45deg);
    }
    
    .navbar-hamburger.active span:nth-child(2) {
        opacity: 0;
        transform: scaleX(0);
    }
    
    .navbar-hamburger.active span:nth-child(3) {
        transform: translateY(-7.5px) rotate(-45deg);
    }
    
    /* ============================================
       RESPONSIVE
    ============================================ */
    @media (max-width: 1024px) {
        .navbar-menu a {
            font-size: 12px;
            padding: 6px 12px;
        }
        .navbar-menu a i {
            font-size: 13px;
        }
        .navbar-dropdown-large {
            min-width: 300px;
            grid-template-columns: 1fr 1fr;
        }
    }
    
    @media (max-width: 768px) {
        .navbar {
            padding: 0 16px;
            border-radius: 12px;
            top: 10px;
        }
        
        .navbar-inner {
            height: 60px;
        }
        
        .navbar-brand .brand-text .brand-title {
            font-size: 14px;
        }
        
        .navbar-hamburger {
            display: flex;
        }
        
        .navbar-menu {
            position: absolute;
            top: calc(100% + 8px);
            left: 8px;
            right: 8px;
            background: white;
            flex-direction: column;
            align-items: stretch;
            padding: 8px;
            border-radius: 12px;
            box-shadow: 0 12px 48px rgba(0,0,0,0.12);
            border: 1px solid rgba(0,0,0,0.04);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-8px) scale(0.98);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            gap: 2px;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .navbar-menu.open {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
        }
        
        .navbar-menu a {
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
        }
        
        .navbar-menu .has-dropdown .navbar-dropdown,
        .navbar-menu .has-dropdown .navbar-dropdown-large {
            position: static;
            transform: none;
            box-shadow: none;
            padding: 0 0 0 16px;
            background: transparent;
            border: none;
            opacity: 1;
            visibility: visible;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            min-width: auto;
            grid-template-columns: 1fr;
        }
        
        .navbar-menu .has-dropdown:hover .navbar-dropdown,
        .navbar-menu .has-dropdown:hover .navbar-dropdown-large {
            max-height: 500px;
        }
        
        .navbar-profile .profile-btn .profile-info .name {
            font-size: 12px;
        }
        .navbar-profile .profile-btn .profile-info .role {
            font-size: 9px;
        }
        .navbar-profile .profile-btn .avatar {
            width: 30px;
            height: 30px;
            font-size: 12px;
        }
    }
    
    @media (max-width: 480px) {
        .navbar {
            padding: 0 12px;
            border-radius: 10px;
        }
        
        .navbar-inner {
            height: 54px;
        }
        
        .navbar-brand .brand-icon {
            width: 34px;
            height: 34px;
            font-size: 14px;
        }
        
        .navbar-brand .brand-text .brand-title {
            font-size: 12px;
        }
        
        .navbar-brand .brand-text .brand-sub {
            font-size: 8px;
        }
        
        .navbar-profile .profile-btn {
            padding: 4px 8px 4px 4px;
        }
        
        .navbar-profile .profile-btn .avatar {
            width: 26px;
            height: 26px;
            font-size: 10px;
        }
        
        .navbar-profile .profile-btn .profile-info .name {
            font-size: 11px;
        }
        .navbar-profile .profile-btn .profile-info .role {
            font-size: 8px;
        }
        .navbar-profile .profile-btn .chevron {
            font-size: 8px;
        }
        
        .navbar-hamburger {
            width: 30px;
            height: 30px;
        }
        
        .navbar-hamburger span {
            width: 18px;
            height: 2px;
        }
        
        .navbar-hamburger.active span:nth-child(1) {
            transform: translateY(6px) rotate(45deg);
        }
        
        .navbar-hamburger.active span:nth-child(3) {
            transform: translateY(-6px) rotate(-45deg);
        }
    }
</style>

<!-- ========================================== -->
<!-- NAVBAR HTML -->
<!-- ========================================== -->
<nav class="navbar" id="navbar">
    <div class="navbar-inner">

        <!-- Hamburger Menu (Mobile) -->
        <button class="navbar-hamburger" id="hamburgerBtn" aria-label="Toggle navigation">
            <span></span>
            <span></span>
            <span></span>
        </button>
        
        <!-- Menu Utama -->
        <ul class="navbar-menu" id="navbarMenu">
            <!-- Beranda -->
            <li>
                <a href="/website_perangkat/index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Beranda
                </a>
            </li>
            
            <!-- Katalog -->
            <li>
                <a href="/website_perangkat/katalog.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'katalog.php' ? 'active' : ''; ?>">
                    <i class="fas fa-book"></i> Katalog
                </a>
            </li>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                
                <!-- ========================================== -->
                <!-- MENU PERANGKAT PEMBELAJARAN (DROPDOWN) -->
                <!-- ========================================== -->
                <li class="has-dropdown">
                    <a href="/website_perangkat/perangkat_dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'perangkat_dashboard.php' ? 'active' : ''; ?>">
                        <i class="fas fa-folder-open"></i> Perangkat
                    </a>
                    <div class="navbar-dropdown-large">
                        <a href="/website_perangkat/perangkat_list.php?jenis=cp">
                            <i class="fas fa-file-alt" style="color:#4f46e5;"></i> 📋 CP
                        </a>
                        <a href="/website_perangkat/perangkat_list.php?jenis=atp">
                            <i class="fas fa-chart-line" style="color:#0d9488;"></i> 📊 ATP
                        </a>
                        <a href="/website_perangkat/perangkat_list.php?jenis=prota">
                            <i class="fas fa-calendar" style="color:#2563eb;"></i> 📅 PROTA
                        </a>
                        <a href="/website_perangkat/perangkat_list.php?jenis=promes">
                            <i class="fas fa-calendar-alt" style="color:#7c3aed;"></i> 📆 PROMES
                        </a>
                        <a href="/website_perangkat/perangkat_list.php?jenis=jurnal">
                            <i class="fas fa-book" style="color:#d97706;"></i> 📓 Jurnal
                        </a>
                        <a href="/website_perangkat/perangkat_list.php?jenis=rpp">
                            <i class="fas fa-file-pdf" style="color:#dc2626;"></i> 📄 RPP
                        </a>
                        <a href="/website_perangkat/perangkat_list.php?jenis=modul">
                            <i class="fas fa-book-open" style="color:#059669;"></i> 📘 Modul
                        </a>
                        <a href="/website_perangkat/perangkat_list.php?jenis=penilaian">
                            <i class="fas fa-check-double" style="color:#6f42c1;"></i> 📝 Penilaian
                        </a>
                        <a href="/website_perangkat/perangkat_list.php?jenis=album">
                            <i class="fas fa-images" style="color:#ec4899;"></i> 🖼️ Album
                        </a>
                        <a href="/website_perangkat/perangkat_list.php?jenis=catatan">
                            <i class="fas fa-sticky-note" style="color:#f59e0b;"></i> 📒 Catatan
                        </a>
                        <a href="/website_perangkat/perangkat_list.php?jenis=raport">
                            <i class="fas fa-file-alt" style="color:#3b82f6;"></i> 📊 Raport
                        </a>
                        <a href="/website_perangkat/perangkat_upload.php">
                            <i class="fas fa-upload" style="color:#059669;"></i> 📤 Upload Dokumen
                        </a>
                    </div>
                </li>
                
                <?php if ($_SESSION['role'] == 'guru'): ?>
                    <!-- Guru Dashboard -->
                    <li>
                        <a href="/website_perangkat/guru_dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'guru_dashboard.php' ? 'active' : ''; ?>">
                            <i class="fas fa-chart-bar"></i> Dashboard
                        </a>
                    </li>
                    
                    <!-- Upload (sudah ada di dropdown perangkat, tapi tetap untuk akses cepat) -->
                    <li>
                        <a href="/website_perangkat/upload.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'upload.php' ? 'active' : ''; ?>">
                            <i class="fas fa-upload"></i> Upload RPP
                        </a>
                    </li>
                    
                    <!-- Presensi (Dropdown) -->
                    <li class="has-dropdown">
                        <a href="#">
                            <i class="fas fa-clipboard-check"></i> Presensi
                        </a>
                        <div class="navbar-dropdown">
                            <a href="/website_perangkat/presensi_guru.php">
                                <i class="fas fa-user-check"></i> Presensi Guru
                            </a>
                            <a href="/website_perangkat/presensi_siswa.php">
                                <i class="fas fa-user-graduate"></i> Presensi Siswa
                            </a>
                        </div>
                    </li>
                    
                <?php elseif ($_SESSION['role'] == 'kepala_sekolah'): ?>
                    <!-- Kepsek Dashboard (Verifikasi) -->
                    <li>
                        <a href="/website_perangkat/kepsek_approve.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'kepsek_approve.php' ? 'active' : ''; ?>">
                            <i class="fas fa-check-double"></i> Verifikasi
                        </a>
                    </li>
                    
                    <!-- Presensi Saya (Kepala Sekolah) -->
                    <li>
                        <a href="/website_perangkat/presensi_guru.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'presensi_guru.php' ? 'active' : ''; ?>">
                            <i class="fas fa-clipboard-check"></i> Presensi Saya
                        </a>
                    </li>
                    
                    <!-- Monitoring Presensi -->
                    <li>
                        <a href="/website_perangkat/dashboard_presensi.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard_presensi.php' ? 'active' : ''; ?>">
                            <i class="fas fa-chart-pie"></i> Monitoring Presensi
                        </a>
                    </li>
                    
                <?php elseif ($_SESSION['role'] == 'pengawas'): ?>
                    <!-- Pengawas Dashboard -->
                    <li>
                        <a href="/website_perangkat/pengawas_approve.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'pengawas_approve.php' ? 'active' : ''; ?>">
                            <i class="fas fa-user-shield"></i> Supervisi
                        </a>
                    </li>
                    
                    <!-- Presensi Saya (Pengawas) -->
                    <li>
                        <a href="/website_perangkat/presensi_guru.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'presensi_guru.php' ? 'active' : ''; ?>">
                            <i class="fas fa-clipboard-check"></i> Presensi Saya
                        </a>
                    </li>
                    
                    <!-- Monitoring Presensi -->
                    <li>
                        <a href="/website_perangkat/dashboard_presensi.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard_presensi.php' ? 'active' : ''; ?>">
                            <i class="fas fa-chart-pie"></i> Monitoring Presensi
                        </a>
                    </li>
                    
                <?php elseif ($_SESSION['role'] == 'dinas'): ?>
                    <!-- Dinas Monitoring -->
                    <li>
                        <a href="/website_perangkat/dinas_monitoring.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dinas_monitoring.php' ? 'active' : ''; ?>">
                            <i class="fas fa-chart-line"></i> Monitoring
                        </a>
                    </li>
                    
                    <!-- Presensi Saya (Dinas) -->
                    <li>
                        <a href="/website_perangkat/presensi_guru.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'presensi_guru.php' ? 'active' : ''; ?>">
                            <i class="fas fa-clipboard-check"></i> Presensi Saya
                        </a>
                    </li>
                    
                    <!-- Monitoring Presensi -->
                    <li>
                        <a href="/website_perangkat/dashboard_presensi.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard_presensi.php' ? 'active' : ''; ?>">
                            <i class="fas fa-chart-pie"></i> Monitoring Presensi
                        </a>
                    </li>
                    
                <?php elseif ($_SESSION['role'] == 'admin'): ?>
                    <!-- Admin Dashboard -->
                    <li>
                        <a href="/website_perangkat/admin_dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'active' : ''; ?>">
                            <i class="fas fa-cog"></i> Admin
                        </a>
                    </li>
                    
                    <!-- Presensi Saya (Admin) -->
                    <li>
                        <a href="/website_perangkat/presensi_guru.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'presensi_guru.php' ? 'active' : ''; ?>">
                            <i class="fas fa-clipboard-check"></i> Presensi Saya
                        </a>
                    </li>
                    
                    <!-- Monitoring Presensi -->
                    <li>
                        <a href="/website_perangkat/dashboard_presensi.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard_presensi.php' ? 'active' : ''; ?>">
                            <i class="fas fa-chart-pie"></i> Monitoring Presensi
                        </a>
                    </li>
                <?php endif; ?>
                
                <!-- Logout -->
                <li>
                    <a href="/website_perangkat/logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
                
            <?php else: ?>
                <!-- Login & Register (belum login) -->
                <li>
                    <a href="/website_perangkat/login.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : ''; ?>">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                </li>
                <li>
                    <a href="/website_perangkat/register.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'register.php' ? 'active' : ''; ?>">
                        <i class="fas fa-user-plus"></i> Register
                    </a>
                </li>
            <?php endif; ?>
        </ul>
        
        <!-- User Profile -->
        <?php if (isset($_SESSION['user_id'])): 
            $role_badge = [
                'guru' => 'badge-guru',
                'kepala_sekolah' => 'badge-kepsek',
                'pengawas' => 'badge-pengawas',
                'dinas' => 'badge-dinas',
                'admin' => 'badge-admin'
            ];
            $role_label = [
                'guru' => '👨‍🏫 Guru',
                'kepala_sekolah' => '👔 Kepsek',
                'pengawas' => '🔍 Pengawas',
                'dinas' => '📊 Dinas',
                'admin' => '⚙️ Admin'
            ];
            $badge_class = $role_badge[$_SESSION['role']] ?? 'badge-guru';
            $role_display = $role_label[$_SESSION['role']] ?? $_SESSION['role'];
        ?>
            <div class="navbar-profile">
                <button class="profile-btn" id="profileBtn" onclick="toggleProfileDropdown()">
                    <span class="avatar"><?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?></span>
                    <span class="profile-info">
                        <span class="name"><?php echo $_SESSION['name']; ?></span>
                        <span class="role">
                            <?php echo $role_display; ?>
                            <span class="badge <?php echo $badge_class; ?>"><?php echo strtoupper($_SESSION['role']); ?></span>
                        </span>
                    </span>
                    <span class="chevron"><i class="fas fa-chevron-down"></i></span>
                </button>
                
                <!-- Profile Dropdown -->
                <div class="profile-dropdown" id="profileDropdown">
                    <div class="dropdown-header">
                        <div class="name"><?php echo $_SESSION['name']; ?></div>
                        <div class="email"><?php echo $_SESSION['email']; ?></div>
                    </div>
                    <div class="divider"></div>
                    <a href="/website_perangkat/profil.php">
                        <i class="fas fa-user"></i> Profil Saya
                    </a>
                    <?php if ($_SESSION['role'] == 'guru'): ?>
                        <a href="/website_perangkat/guru_dashboard.php">
                            <i class="fas fa-chart-bar"></i> Dashboard
                        </a>
                        <a href="/website_perangkat/upload.php">
                            <i class="fas fa-upload"></i> Upload
                        </a>
                    <?php elseif ($_SESSION['role'] == 'kepala_sekolah'): ?>
                        <a href="/website_perangkat/kepsek_approve.php">
                            <i class="fas fa-check-double"></i> Verifikasi
                        </a>
                    <?php elseif ($_SESSION['role'] == 'pengawas'): ?>
                        <a href="/website_perangkat/pengawas_approve.php">
                            <i class="fas fa-user-shield"></i> Supervisi
                        </a>
                    <?php elseif ($_SESSION['role'] == 'dinas'): ?>
                        <a href="/website_perangkat/dinas_monitoring.php">
                            <i class="fas fa-chart-line"></i> Monitoring
                        </a>
                    <?php elseif ($_SESSION['role'] == 'admin'): ?>
                        <a href="/website_perangkat/admin_dashboard.php">
                            <i class="fas fa-cog"></i> Admin Panel
                        </a>
                    <?php endif; ?>
                    <div class="divider"></div>
                    <a href="/website_perangkat/logout.php" style="color:#dc3545;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        <?php endif; ?>
        
    </div>
</nav>

<!-- ========================================== -->
<!-- SCRIPT NAVBAR -->
<!-- ========================================== -->
<script>
    // ============================================
    // HAMBURGER MENU TOGGLE
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        const hamburger = document.getElementById('hamburgerBtn');
        const menu = document.getElementById('navbarMenu');
        
        if (hamburger && menu) {
            hamburger.addEventListener('click', function(e) {
                e.stopPropagation();
                this.classList.toggle('active');
                menu.classList.toggle('open');
            });
        }
        
        // Tutup menu saat klik di luar
        document.addEventListener('click', function(e) {
            const navbar = document.getElementById('navbar');
            if (navbar && !navbar.contains(e.target)) {
                if (hamburger) hamburger.classList.remove('active');
                if (menu) menu.classList.remove('open');
            }
        });
        
        // Tutup profile dropdown saat klik di luar
        document.addEventListener('click', function(e) {
            const profile = document.querySelector('.navbar-profile');
            const dropdown = document.getElementById('profileDropdown');
            if (profile && dropdown && !profile.contains(e.target)) {
                dropdown.classList.remove('open');
            }
        });
    });
    
    // ============================================
    // PROFILE DROPDOWN TOGGLE
    // ============================================
    function toggleProfileDropdown() {
        const dropdown = document.getElementById('profileDropdown');
        if (dropdown) {
            dropdown.classList.toggle('open');
        }
    }
    
    // ============================================
    // SCROLL EFFECT
    // ============================================
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('navbar');
        if (navbar) {
            if (window.scrollY > 50) {
                navbar.style.boxShadow = '0 8px 32px rgba(0,0,0,0.10)';
                navbar.style.background = 'rgba(255,255,255,0.98)';
            } else {
                navbar.style.boxShadow = '0 4px 20px rgba(0,0,0,0.06)';
                navbar.style.background = 'rgba(255,255,255,0.95)';
            }
        }
    });
</script>