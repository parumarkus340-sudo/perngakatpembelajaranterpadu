<?php
// config/database.php - Koneksi dan Fungsi Database
// ============================================

// Konfigurasi Database
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'perangkat_pembelajaran';

// Buat koneksi
$conn = mysqli_connect($host, $username, $password, $database);

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset ke UTF-8
mysqli_set_charset($conn, "utf8mb4");

// ============================================
// FUNGSI-FUNGSI DATABASE DASAR
// ============================================

// 1. Jalankan query
function query($sql) {
    global $conn;
    return mysqli_query($conn, $sql);
}

// 2. Ambil semua data (array asosiatif)
function fetchAll($sql) {
    global $conn;
    $result = mysqli_query($conn, $sql);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

// 3. Ambil satu data
function fetchOne($sql) {
    global $conn;
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result);
}

// 4. Hitung jumlah data
function countData($sql) {
    global $conn;
    $result = mysqli_query($conn, $sql);
    return mysqli_num_rows($result);
}

// 5. Escape string (keamanan SQL Injection)
function escape($string) {
    global $conn;
    return mysqli_real_escape_string($conn, $string);
}

// 6. Ambil ID terakhir
function lastId() {
    global $conn;
    return mysqli_insert_id($conn);
}

// 7. Tampilkan error
function showError($sql) {
    global $conn;
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

// ============================================
// FUNGSI KHUSUS USER & SEKOLAH
// ============================================

// 8. Cek login user
function cekLogin($email, $password) {
    $email = escape($email);
    $user = fetchOne("
        SELECT u.*, s.id as sekolah_id, s.nama_sekolah as sekolah_nama 
        FROM users u 
        LEFT JOIN sekolah s ON u.sekolah_id = s.id 
        WHERE u.email = '$email' AND u.is_active = 1
    ");
    
    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
}

// 9. Get user by ID
function getUserById($id) {
    return fetchOne("
        SELECT u.*, s.id as sekolah_id, s.nama_sekolah as sekolah_nama,
               s.npsn, s.kecamatan, s.alamat as sekolah_alamat
        FROM users u 
        LEFT JOIN sekolah s ON u.sekolah_id = s.id 
        WHERE u.id = $id
    ");
}

// 10. Get nama sekolah dari user ID
function getSekolahName($user_id) {
    $data = fetchOne("
        SELECT s.nama_sekolah 
        FROM users u 
        LEFT JOIN sekolah s ON u.sekolah_id = s.id 
        WHERE u.id = $user_id
    ");
    return $data ? $data['nama_sekolah'] : '-';
}

// 11. Get data sekolah dari user ID
function getUserSekolah($user_id) {
    return fetchOne("
        SELECT u.*, s.id as sekolah_id, s.nama_sekolah, s.npsn, s.kecamatan
        FROM users u 
        LEFT JOIN sekolah s ON u.sekolah_id = s.id 
        WHERE u.id = $user_id
    ");
}

// 12. Get guru binaan (guru di sekolah yang sama) - UNTUK KEPALA SEKOLAH
function getGuruBinaan($kepsek_id) {
    $kepsek = fetchOne("SELECT sekolah_id FROM users WHERE id = $kepsek_id");
    if (!$kepsek || !$kepsek['sekolah_id']) {
        return [];
    }
    $sekolah_id = $kepsek['sekolah_id'];
    
    return fetchAll("
        SELECT u.*, 
               COUNT(p.id) as total_dokumen,
               SUM(CASE WHEN p.status = 'terverifikasi' THEN 1 ELSE 0 END) as dokumen_terverifikasi,
               SUM(CASE WHEN p.status = 'pending_kepsek' THEN 1 ELSE 0 END) as dokumen_pending
        FROM users u
        LEFT JOIN perangkat p ON u.id = p.id_guru
        WHERE u.role = 'guru' AND u.sekolah_id = $sekolah_id
        GROUP BY u.id
        ORDER BY u.name
    ");
}

// 13. Get perangkat pending untuk Kepala Sekolah
function getPerangkatPendingKepsek($kepsek_id) {
    $kepsek = fetchOne("SELECT sekolah_id FROM users WHERE id = $kepsek_id");
    if (!$kepsek || !$kepsek['sekolah_id']) {
        return [];
    }
    $sekolah_id = $kepsek['sekolah_id'];
    
    return fetchAll("
        SELECT p.*, u.name as guru_name, u.nip as guru_nip,
               mp.nama_mapel, k.nama_kelas, k.jenjang
        FROM perangkat p
        JOIN users u ON p.id_guru = u.id
        JOIN mata_pelajaran mp ON p.id_mapel = mp.id
        JOIN kelas k ON p.id_kelas = k.id
        WHERE p.status = 'pending_kepsek' 
          AND u.sekolah_id = $sekolah_id
        ORDER BY p.created_at ASC
    ");
}

// 14. Approve perangkat
function approvePerangkat($id_perangkat, $id_user, $role, $catatan = '') {
    global $conn;
    $catatan = mysqli_real_escape_string($conn, $catatan);
    
    if ($role == 'kepala_sekolah') {
        $ttd_path = "public/uploads/ttd/ttd_kepsek_" . $id_perangkat . "_" . date('Ymd_His') . ".png";
        $sql = "UPDATE perangkat SET 
                    status = 'pending_pengawas',
                    ttd_kepsek = '$ttd_path',
                    ttd_kepsek_date = NOW(),
                    catatan_kepsek = '$catatan'
                WHERE id = $id_perangkat";
        $status_akhir = 'pending_pengawas';
        $catatan_riwayat = 'Kepala Sekolah menyetujui dan menandatangani';
    } elseif ($role == 'pengawas') {
        $ttd_path = "public/uploads/ttd/ttd_pengawas_" . $id_perangkat . "_" . date('Ymd_His') . ".png";
        $sql = "UPDATE perangkat SET 
                    status = 'terverifikasi',
                    ttd_pengawas = '$ttd_path',
                    ttd_pengawas_date = NOW(),
                    catatan_pengawas = '$catatan'
                WHERE id = $id_perangkat";
        $status_akhir = 'terverifikasi';
        $catatan_riwayat = 'Pengawas menyetujui dan menandatangani';
    } else {
        return false;
    }
    
    if (query($sql)) {
        $sql_riwayat = "INSERT INTO riwayat_status (id_perangkat, id_user, status_awal, status_akhir, catatan) 
                        VALUES ($id_perangkat, $id_user, 'pending', '$status_akhir', '$catatan_riwayat')";
        query($sql_riwayat);
        return true;
    }
    return false;
}

// 15. Tolak perangkat
function tolakPerangkat($id_perangkat, $id_user, $role, $catatan) {
    global $conn;
    $catatan = mysqli_real_escape_string($conn, $catatan);
    
    if ($role == 'kepala_sekolah') {
        $status = 'ditolak_kepsek';
        $sql = "UPDATE perangkat SET status = '$status', catatan_kepsek = '$catatan' WHERE id = $id_perangkat";
    } elseif ($role == 'pengawas') {
        $status = 'ditolak_pengawas';
        $sql = "UPDATE perangkat SET status = '$status', catatan_pengawas = '$catatan' WHERE id = $id_perangkat";
    } else {
        return false;
    }
    
    if (query($sql)) {
        $sql_riwayat = "INSERT INTO riwayat_status (id_perangkat, id_user, status_awal, status_akhir, catatan) 
                        VALUES ($id_perangkat, $id_user, 'pending', '$status', 'Ditolak oleh $role: $catatan')";
        query($sql_riwayat);
        return true;
    }
    return false;
}

// ============================================
// FUNGSI PENGAWAS SEKOLAH (Many-to-Many)
// ============================================

// 16. Get semua sekolah binaan untuk pengawas
function getSekolahBinaanPengawas($pengawas_id) {
    return fetchAll("
        SELECT s.*, 
               COUNT(DISTINCT u.id) as total_guru,
               COUNT(p.id) as total_dokumen,
               SUM(CASE WHEN p.status = 'terverifikasi' THEN 1 ELSE 0 END) as dokumen_terverifikasi
        FROM sekolah s
        JOIN pengawas_sekolah ps ON s.id = ps.sekolah_id
        LEFT JOIN users u ON u.sekolah_id = s.id AND u.role = 'guru'
        LEFT JOIN perangkat p ON p.id_guru = u.id
        WHERE ps.pengawas_id = $pengawas_id
        GROUP BY s.id
        ORDER BY s.nama_sekolah
    ");
}

// 17. Get ID sekolah binaan untuk pengawas
function getSekolahBinaanIds($pengawas_id) {
    $data = fetchAll("SELECT sekolah_id FROM pengawas_sekolah WHERE pengawas_id = $pengawas_id");
    $ids = [];
    foreach ($data as $d) {
        $ids[] = $d['sekolah_id'];
    }
    return $ids;
}

// 18. Tambah sekolah binaan untuk pengawas
function tambahSekolahBinaan($pengawas_id, $sekolah_id) {
    $cek = fetchOne("SELECT id FROM pengawas_sekolah WHERE pengawas_id = $pengawas_id AND sekolah_id = $sekolah_id");
    if ($cek) {
        return false; // Sudah ada
    }
    $sql = "INSERT INTO pengawas_sekolah (pengawas_id, sekolah_id) VALUES ($pengawas_id, $sekolah_id)";
    return query($sql);
}

// 19. Hapus sekolah binaan untuk pengawas
function hapusSekolahBinaan($pengawas_id, $sekolah_id) {
    $sql = "DELETE FROM pengawas_sekolah WHERE pengawas_id = $pengawas_id AND sekolah_id = $sekolah_id";
    return query($sql);
}

// 20. Hapus semua sekolah binaan untuk pengawas
function hapusSemuaSekolahBinaan($pengawas_id) {
    $sql = "DELETE FROM pengawas_sekolah WHERE pengawas_id = $pengawas_id";
    return query($sql);
}

// 21. Get filter SQL untuk pengawas
function getPengawasFilter($pengawas_id) {
    $ids = getSekolahBinaanIds($pengawas_id);
    if (empty($ids)) {
        return "AND 1=0";
    }
    $ids_str = implode(',', $ids);
    return "AND u.sekolah_id IN ($ids_str)";
}

// 22. Get perangkat pending untuk Pengawas (Multi Sekolah)
function getPerangkatPendingPengawas($pengawas_id) {
    $ids = getSekolahBinaanIds($pengawas_id);
    if (empty($ids)) {
        return [];
    }
    $ids_str = implode(',', $ids);
    
    return fetchAll("
        SELECT p.*, u.name as guru_name, u.sekolah as guru_sekolah,
               mp.nama_mapel, k.nama_kelas, k.jenjang,
               s.nama_sekolah as sekolah_nama
        FROM perangkat p
        JOIN users u ON p.id_guru = u.id
        JOIN mata_pelajaran mp ON p.id_mapel = mp.id
        JOIN kelas k ON p.id_kelas = k.id
        LEFT JOIN sekolah s ON u.sekolah_id = s.id
        WHERE p.status = 'pending_pengawas' AND u.sekolah_id IN ($ids_str)
        ORDER BY p.created_at ASC
    ");
}

// ============================================
// FUNGSI UNTUK STATISTIK & LABEL
// ============================================

// 23. Get dashboard stats untuk admin
function getAdminStats() {
    return fetchOne("
        SELECT 
            (SELECT COUNT(*) FROM sekolah) as total_sekolah,
            (SELECT COUNT(*) FROM users WHERE role = 'guru') as total_guru,
            (SELECT COUNT(*) FROM users WHERE role = 'kepala_sekolah') as total_kepsek,
            (SELECT COUNT(*) FROM users WHERE role = 'pengawas') as total_pengawas,
            (SELECT COUNT(*) FROM users WHERE role = 'dinas') as total_dinas,
            (SELECT COUNT(*) FROM perangkat) as total_perangkat,
            (SELECT COUNT(*) FROM perangkat WHERE status = 'terverifikasi') as total_terverifikasi,
            (SELECT COUNT(*) FROM perangkat WHERE status = 'pending_kepsek' OR status = 'pending_pengawas') as total_pending,
            (SELECT COUNT(*) FROM perangkat WHERE status = 'ditolak_kepsek' OR status = 'ditolak_pengawas') as total_ditolak
    ");
}

// 24. Get status label
function getStatusLabel($status) {
    $labels = [
        'draft' => '📝 Draft',
        'pending_kepsek' => '⏳ Menunggu Kepsek',
        'ditolak_kepsek' => '❌ Ditolak Kepsek',
        'pending_pengawas' => '⏳ Menunggu Pengawas',
        'ditolak_pengawas' => '❌ Ditolak Pengawas',
        'terverifikasi' => '✅ Terverifikasi'
    ];
    return $labels[$status] ?? $status;
}

// 25. Get role label
function getRoleLabel($role) {
    $labels = [
        'admin' => '⚙️ Admin',
        'guru' => '👨‍🏫 Guru',
        'kepala_sekolah' => '👔 Kepala Sekolah',
        'pengawas' => '🔍 Pengawas',
        'dinas' => '📊 Dinas'
    ];
    return $labels[$role] ?? $role;
}

// 26. Get role badge class
function getRoleBadge($role) {
    $classes = [
        'admin' => 'role-admin',
        'guru' => 'role-guru',
        'kepala_sekolah' => 'role-kepsek',
        'pengawas' => 'role-pengawas',
        'dinas' => 'role-dinas'
    ];
    return $classes[$role] ?? '';
}

// 27. Format tanggal Indonesia
function formatTanggal($tanggal) {
    if (empty($tanggal) || $tanggal == '0000-00-00 00:00:00') {
        return '-';
    }
    $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $t = strtotime($tanggal);
    return date('d', $t) . ' ' . $bulan[date('n', $t)-1] . ' ' . date('Y H:i', $t);
}

// 28. Format tanggal pendek
function formatTanggalPendek($tanggal) {
    if (empty($tanggal) || $tanggal == '0000-00-00 00:00:00') {
        return '-';
    }
    return date('d/m/Y H:i', strtotime($tanggal));
}

?>