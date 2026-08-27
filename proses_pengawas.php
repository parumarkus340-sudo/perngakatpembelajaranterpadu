<?php
// proses_pengawas.php - Proses Tanda Tangan Pengawas
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pengawas') {
    header('Location: login.php');
    exit;
}

include_once 'config/database.php';

$id_perangkat = (int)$_POST['id_perangkat'];
$action = $_POST['action'];
$catatan = escape($_POST['catatan'] ?? '');

$id_pengawas = $_SESSION['user_id'];

if ($action == 'approve') {
    $ttd_path = "public/uploads/ttd/ttd_pengawas_" . $id_perangkat . "_" . date('Ymd_His') . ".png";
    
    $sql = "UPDATE perangkat SET 
                status = 'terverifikasi',
                ttd_pengawas = '$ttd_path',
                ttd_pengawas_date = NOW(),
                catatan_pengawas = '$catatan'
            WHERE id = $id_perangkat";
    
    if (query($sql)) {
        $sql_riwayat = "INSERT INTO riwayat_status (id_perangkat, id_user, status_awal, status_akhir, catatan) 
                        VALUES ($id_perangkat, $id_pengawas, 'pending_pengawas', 'terverifikasi', 'Pengawas menyetujui dan menandatangani')";
        query($sql_riwayat);
        
        header('Location: pengawas_approve.php?success=1');
    }
} elseif ($action == 'reject') {
    $sql = "UPDATE perangkat SET 
                status = 'ditolak_pengawas',
                catatan_pengawas = '$catatan'
            WHERE id = $id_perangkat";
    
    if (query($sql)) {
        $sql_riwayat = "INSERT INTO riwayat_status (id_perangkat, id_user, status_awal, status_akhir, catatan) 
                        VALUES ($id_perangkat, $id_pengawas, 'pending_pengawas', 'ditolak_pengawas', 'Ditolak oleh Pengawas: $catatan')";
        query($sql_riwayat);
        
        header('Location: pengawas_approve.php?success=1');
    }
}
?>