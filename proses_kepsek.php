<?php
// proses_kepsek.php - Proses Tanda Tangan Kepala Sekolah
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'kepala_sekolah') {
    header('Location: login.php');
    exit;
}

include_once 'config/database.php';

$id_perangkat = (int)$_POST['id_perangkat'];
$action = $_POST['action'];
$catatan = escape($_POST['catatan'] ?? '');

$id_kepsek = $_SESSION['user_id'];

if ($action == 'approve') {
    // Tanda tangan digital (simpan path)
    $ttd_path = "public/uploads/ttd/ttd_kepsek_" . $id_perangkat . "_" . date('Ymd_His') . ".png";
    
    // (Di sini seharusnya generate tanda tangan digital)
    // Untuk demo, kita simpan teks sebagai tanda tangan
    
    $sql = "UPDATE perangkat SET 
                status = 'pending_pengawas',
                ttd_kepsek = '$ttd_path',
                ttd_kepsek_date = NOW(),
                catatan_kepsek = '$catatan'
            WHERE id = $id_perangkat";
    
    if (query($sql)) {
        // Catat riwayat
        $sql_riwayat = "INSERT INTO riwayat_status (id_perangkat, id_user, status_awal, status_akhir, catatan) 
                        VALUES ($id_perangkat, $id_kepsek, 'pending_kepsek', 'pending_pengawas', 'Kepala Sekolah menyetujui dan menandatangani')";
        query($sql_riwayat);
        
        header('Location: kepsek_approve.php?success=1');
    } else {
        header('Location: kepsek_approve.php?error=Gagal update');
    }
} elseif ($action == 'reject') {
    $sql = "UPDATE perangkat SET 
                status = 'ditolak_kepsek',
                catatan_kepsek = '$catatan'
            WHERE id = $id_perangkat";
    
    if (query($sql)) {
        $sql_riwayat = "INSERT INTO riwayat_status (id_perangkat, id_user, status_awal, status_akhir, catatan) 
                        VALUES ($id_perangkat, $id_kepsek, 'pending_kepsek', 'ditolak_kepsek', 'Ditolak oleh Kepala Sekolah: $catatan')";
        query($sql_riwayat);
        
        header('Location: kepsek_approve.php?success=1');
    } else {
        header('Location: kepsek_approve.php?error=Gagal update');
    }
} else {
    header('Location: kepsek_approve.php');
}
?>