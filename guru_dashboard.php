<?php
// guru_dashboard.php - Dashboard Guru
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'guru') {
    header('Location: login.php');
    exit;
}

include_once 'config/database.php';

$id_guru = $_SESSION['user_id'];
$nama_guru = $_SESSION['name'];

// ============================================
// AMBIL DATA DOKUMEN GURU
// ============================================
$dokumen = fetchAll("
    SELECT p.*, 
           mp.nama_mapel, 
           k.nama_kelas, k.jenjang,
           u.name as guru_name,
           DATE_FORMAT(p.created_at, '%d/%m/%Y %H:%i') as tgl_upload,
           DATE_FORMAT(p.ttd_kepsek_date, '%d/%m/%Y %H:%i') as tgl_kepsek,
           DATE_FORMAT(p.ttd_pengawas_date, '%d/%m/%Y %H:%i') as tgl_pengawas
    FROM perangkat p
    LEFT JOIN mata_pelajaran mp ON p.id_mapel = mp.id
    LEFT JOIN kelas k ON p.id_kelas = k.id
    LEFT JOIN users u ON p.id_guru = u.id
    WHERE p.id_guru = $id_guru
    ORDER BY p.created_at DESC
");

// ============================================
// STATISTIK
// ============================================
$total_dokumen = count($dokumen);
$total_draft = 0;
$total_pending_kepsek = 0;
$total_pending_pengawas = 0;
$total_terverifikasi = 0;
$total_ditolak = 0;

foreach ($dokumen as $d) {
    switch ($d['status']) {
        case 'draft': $total_draft++; break;
        case 'pending_kepsek': $total_pending_kepsek++; break;
        case 'pending_pengawas': $total_pending_pengawas++; break;
        case 'terverifikasi': $total_terverifikasi++; break;
        case 'ditolak_kepsek':
        case 'ditolak_pengawas': $total_ditolak++; break;
    }
}

// ============================================
// STATUS LABEL
// ============================================
function getStatusInfo($status) {
    $info = [
        'draft' => [
            'label' => '📝 Draft',
            'class' => 'status-draft',
            'step' => 1,
            'icon' => 'fa-file-pen',
            'color' => '#6c757d'
        ],
        'pending_kepsek' => [
            'label' => '⏳ Menunggu Kepala Sekolah',
            'class' => 'status-pending-kepsek',
            'step' => 2,
            'icon' => 'fa-hourglass-half',
            'color' => '#ffc107'
        ],
        'ditolak_kepsek' => [
            'label' => '❌ Ditolak Kepala Sekolah',
            'class' => 'status-ditolak',
            'step' => 2,
            'icon' => 'fa-times-circle',
            'color' => '#dc3545'
        ],
        'pending_pengawas' => [
            'label' => '⏳ Menunggu Pengawas',
            'class' => 'status-pending-pengawas',
            'step' => 3,
            'icon' => 'fa-hourglass-half',
            'color' => '#ff9800'
        ],
        'ditolak_pengawas' => [
            'label' => '❌ Ditolak Pengawas',
            'class' => 'status-ditolak',
            'step' => 3,
            'icon' => 'fa-times-circle',
            'color' => '#dc3545'
        ],
        'terverifikasi' => [
            'label' => '✅ Terverifikasi',
            'class' => 'status-terverifikasi',
            'step' => 4,
            'icon' => 'fa-check-circle',
            'color' => '#28a745'
        ]
    ];
    return $info[$status] ?? $info['draft'];
}

// ============================================
// STEP FLOW
// ============================================
function getFlowSteps($status) {
    $steps = [
        1 => ['label' => 'Draft', 'icon' => 'fa-file-pen', 'color' => '#6c757d'],
        2 => ['label' => 'Kepala Sekolah', 'icon' => 'fa-user-tie', 'color' => '#ffc107'],
        3 => ['label' => 'Pengawas', 'icon' => 'fa-user-shield', 'color' => '#ff9800'],
        4 => ['label' => 'Terverifikasi', 'icon' => 'fa-check-circle', 'color' => '#28a745']
    ];
    
    $step_map = [
        'draft' => 1,
        'pending_kepsek' => 2,
        'ditolak_kepsek' => 2,
        'pending_pengawas' => 3,
        'ditolak_pengawas' => 3,
        'terverifikasi' => 4
    ];
    
    $current = $step_map[$status] ?? 1;
    return ['steps' => $steps, 'current' => $current];
}

// ============================================
// PROSES AI - BUAT PERANGKAT AJAR
// ============================================
$ai_result = '';
$ai_jenis = '';
$ai_judul = '';
$ai_deskripsi = '';
$ai_tujuan = '';
$ai_langkah = '';
$ai_penilaian = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ai_generate'])) {
    $ai_jenis = mysqli_real_escape_string($conn, $_POST['ai_jenis']);
    $ai_topik = mysqli_real_escape_string($conn, $_POST['ai_topik']);
    $ai_kelas = mysqli_real_escape_string($conn, $_POST['ai_kelas']);
    $ai_mapel = mysqli_real_escape_string($conn, $_POST['ai_mapel']);
    
    // Simulasi AI - Generate konten berdasarkan input
    // Dalam implementasi nyata, ini bisa terintegrasi dengan API AI (OpenAI, Gemini, dll)
    
    $ai_judul = "Perangkat Pembelajaran " . ucfirst($ai_jenis) . " - " . ucfirst($ai_topik);
    
    // Deskripsi
    $ai_deskripsi = "Dokumen " . $ai_jenis . " ini membahas tentang " . ucfirst($ai_topik) . 
                    " untuk kelas " . $ai_kelas . " mata pelajaran " . $ai_mapel . 
                    ". Disusun untuk memenuhi kebutuhan pembelajaran yang efektif dan menyenangkan.";
    
    // Tujuan Pembelajaran
    $ai_tujuan = "1. Peserta didik dapat memahami konsep " . ucfirst($ai_topik) . "\n" .
                 "2. Peserta didik dapat menerapkan " . ucfirst($ai_topik) . " dalam kehidupan sehari-hari\n" .
                 "3. Peserta didik dapat menganalisis " . ucfirst($ai_topik) . " secara kritis\n" .
                 "4. Peserta didik dapat mengevaluasi " . ucfirst($ai_topik) . " dengan tepat";
    
    // Langkah Pembelajaran
    $ai_langkah = "1. Pendahuluan (10 menit)\n" .
                  "   - Guru membuka pembelajaran dengan salam dan doa\n" .
                  "   - Guru menyampaikan tujuan pembelajaran\n" .
                  "   - Guru melakukan apersepsi terkait " . ucfirst($ai_topik) . "\n\n" .
                  "2. Kegiatan Inti (60 menit)\n" .
                  "   - Guru menjelaskan konsep " . ucfirst($ai_topik) . " secara interaktif\n" .
                  "   - Peserta didik berdiskusi dalam kelompok\n" .
                  "   - Peserta didik mempresentasikan hasil diskusi\n" .
                  "   - Guru memberikan penguatan materi\n\n" .
                  "3. Penutup (20 menit)\n" .
                  "   - Guru dan peserta didik menyimpulkan pembelajaran\n" .
                  "   - Guru memberikan refleksi dan tindak lanjut\n" .
                  "   - Guru menutup pembelajaran dengan salam";
    
    // Penilaian
    $ai_penilaian = "1. Penilaian Sikap: Observasi selama pembelajaran\n" .
                    "2. Penilaian Pengetahuan: Tes tertulis (10 soal pilihan ganda)\n" .
                    "3. Penilaian Keterampilan: Presentasi kelompok\n" .
                    "4. Penilaian Produk: Laporan hasil diskusi";
    
    $ai_result = 'success';
}
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
    <style>
        /* ============================================
           AI GENERATOR MODAL
        ============================================ */
        .ai-modal-overlay {
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
        .ai-modal-overlay.active {
            display: flex;
        }

        .ai-modal-box {
            background: white;
            border-radius: 20px;
            max-width: 700px;
            width: 95%;
            max-height: 90vh;
            overflow-y: auto;
            padding: 35px 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            animation: slideDown 0.4s ease;
        }

        .ai-modal-box .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f3f4f6;
        }

        .ai-modal-box .modal-header h3 {
            font-size: 20px;
            font-weight: 700;
            color: #0f2b5c;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .ai-modal-box .modal-header h3 .ai-badge {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            font-size: 10px;
            padding: 2px 12px;
            border-radius: 20px;
            font-weight: 600;
        }

        .ai-modal-box .modal-header .close-btn {
            background: none;
            border: none;
            font-size: 28px;
            color: #aaa;
            cursor: pointer;
            transition: color 0.3s;
        }

        .ai-modal-box .modal-header .close-btn:hover {
            color: #333;
        }

        .ai-modal-box .form-group {
            margin-bottom: 16px;
        }

        .ai-modal-box .form-group label {
            display: block;
            font-weight: 500;
            font-size: 13px;
            color: #4a5568;
            margin-bottom: 4px;
        }

        .ai-modal-box .form-group label .required {
            color: #dc3545;
        }

        .ai-modal-box .form-group select,
        .ai-modal-box .form-group input {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f9fafb;
            font-family: 'Inter', sans-serif;
        }

        .ai-modal-box .form-group select:focus,
        .ai-modal-box .form-group input:focus {
            border-color: #667eea;
            outline: none;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.08);
        }

        .ai-modal-box .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .ai-modal-box .btn-ai-generate {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }

        .ai-modal-box .btn-ai-generate:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.35);
        }

        .ai-modal-box .btn-ai-generate i {
            font-size: 18px;
        }

        .ai-modal-box .ai-result {
            margin-top: 20px;
            padding: 20px;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            display: <?php echo $ai_result ? 'block' : 'none'; ?>;
        }

        .ai-modal-box .ai-result .result-title {
            font-size: 16px;
            font-weight: 600;
            color: #0f2b5c;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ai-modal-box .ai-result .result-title i {
            color: #667eea;
        }

        .ai-modal-box .ai-result .result-item {
            margin-bottom: 12px;
        }

        .ai-modal-box .ai-result .result-item .label {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .ai-modal-box .ai-result .result-item .value {
            font-size: 14px;
            color: #1f2937;
            padding: 8px 12px;
            background: white;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            margin-top: 4px;
            white-space: pre-wrap;
            min-height: 30px;
        }

        .ai-modal-box .btn-save-result {
            background: #28a745;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
        }

        .ai-modal-box .btn-save-result:hover {
            background: #218838;
            transform: scale(1.02);
        }

        .ai-modal-box .btn-edit-result {
            background: #ffc107;
            color: #000;
            padding: 12px 30px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
            margin-left: 8px;
        }

        .ai-modal-box .btn-edit-result:hover {
            background: #e0a800;
            transform: scale(1.02);
        }

        /* ============================================
           DASHBOARD STYLE
        ============================================ */
        .btn-ai {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 16px rgba(102, 126, 234, 0.3);
        }

        .btn-ai:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 24px rgba(102, 126, 234, 0.4);
        }

        .btn-ai i {
            font-size: 18px;
        }

        .btn-ai .sparkle {
            animation: sparkle 2s infinite;
        }

        @keyframes sparkle {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        .header-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
        }

        @media (max-width: 768px) {
            .ai-modal-box {
                padding: 20px 16px;
            }
            .ai-modal-box .form-row {
                grid-template-columns: 1fr;
            }
            .header-actions {
                flex-direction: column;
                align-items: stretch;
            }
            .btn-ai {
                justify-content: center;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
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
    </style>
</head>
<body>
    <div class="container">
        <!-- HEADER -->
        <header class="header header-guru">
            <div>
                <h1><i class="fas fa-chalkboard-teacher"></i> Dashboard Guru</h1>
                <p>Kelola perangkat pembelajaran Anda</p>
            </div>
            <div class="header-actions">
                <button class="btn-ai" onclick="openAIModal()">
                    <i class="fas fa-robot sparkle"></i> 
                    Buat Perangkat Ajar dengan AI
                    <i class="fas fa-sparkles" style="font-size:12px;"></i>
                </button>
                <a href="/website_perangkat/upload.php" class="btn-upload" style="display:inline-block;">
                    <i class="fas fa-upload"></i> Upload Manual
                </a>
            </div>
        </header>

        <!-- NAVBAR -->
        <?php include_once 'navbar.php'; ?>

        <!-- MAIN -->
        <main>
            <!-- STATISTIK -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="icon-circle green"><i class="fas fa-file-alt"></i></div>
                    <div class="number"><?php echo $total_dokumen; ?></div>
                    <div class="label">📄 Total Dokumen</div>
                </div>
                <div class="stat-card">
                    <div class="icon-circle orange"><i class="fas fa-file-pen"></i></div>
                    <div class="number orange"><?php echo $total_draft; ?></div>
                    <div class="label">📝 Draft</div>
                </div>
                <div class="stat-card">
                    <div class="icon-circle blue"><i class="fas fa-hourglass-half"></i></div>
                    <div class="number blue"><?php echo $total_pending_kepsek + $total_pending_pengawas; ?></div>
                    <div class="label">⏳ Pending</div>
                    <div class="sub-label">Kepsek: <?php echo $total_pending_kepsek; ?> · Pengawas: <?php echo $total_pending_pengawas; ?></div>
                </div>
                <div class="stat-card">
                    <div class="icon-circle purple"><i class="fas fa-check-circle"></i></div>
                    <div class="number purple"><?php echo $total_terverifikasi; ?></div>
                    <div class="label">✅ Terverifikasi</div>
                </div>
                <div class="stat-card">
                    <div class="icon-circle red"><i class="fas fa-times-circle"></i></div>
                    <div class="number red"><?php echo $total_ditolak; ?></div>
                    <div class="label">❌ Ditolak</div>
                </div>
            </div>

            <!-- DAFTAR DOKUMEN -->
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 15px;">
                <div class="section-title" style="margin:0;">
                    <span>📋 Riwayat Pengajuan Dokumen <span class="badge-count"><?php echo $total_dokumen; ?></span></span>
                </div>
                <button class="btn-ai" style="padding:8px 18px; font-size:13px;" onclick="openAIModal()">
                    <i class="fas fa-robot"></i> AI Generate
                </button>
            </div>

            <!-- TABEL DOKUMEN -->
            <div class="table-container">
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th style="width:40px;">#</th>
                                <th>Judul Dokumen</th>
                                <th>Mapel</th>
                                <th>Kelas</th>
                                <th>Status</th>
                                <th>Alur Persetujuan</th>
                                <th style="text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($dokumen) > 0): ?>
                                <?php $no = 1; foreach ($dokumen as $d): 
                                    $status_info = getStatusInfo($d['status']);
                                    $flow = getFlowSteps($d['status']);
                                    $is_rejected = in_array($d['status'], ['ditolak_kepsek', 'ditolak_pengawas']);
                                ?>
                                <tr>
                                    <td style="text-align:center; font-weight:600; color:#6b7280;"><?php echo $no++; ?></td>
                                    <td>
                                        <strong><?php echo $d['judul']; ?></strong>
                                        <div style="font-size:11px; color:#999; margin-top:2px;">
                                            <i class="far fa-calendar-alt"></i> <?php echo $d['tgl_upload']; ?>
                                        </div>
                                    </td>
                                    <td><?php echo $d['nama_mapel'] ?? '-'; ?></td>
                                    <td>
                                        <?php if ($d['nama_kelas']): ?>
                                            Kelas <?php echo $d['nama_kelas']; ?> (<?php echo $d['jenjang']; ?>)
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-badge <?php echo $status_info['class']; ?>">
                                            <?php echo $status_info['label']; ?>
                                        </span>
                                        <?php if ($is_rejected && !empty($d['catatan_kepsek']) || !empty($d['catatan_pengawas'])): ?>
                                            <div style="font-size:10px; color:#dc3545; margin-top:2px;">
                                                <i class="fas fa-comment"></i> <?php echo $d['catatan_kepsek'] ?? $d['catatan_pengawas']; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="flow-container">
                                            <?php 
                                            $steps = $flow['steps'];
                                            $current = $flow['current'];
                                            foreach ($steps as $step_num => $step):
                                                $is_active = $step_num <= $current;
                                                $is_current = $step_num == $current;
                                                $step_class = '';
                                                if ($is_rejected && $step_num == $current) {
                                                    $step_class = 'rejected';
                                                } elseif ($is_active) {
                                                    $step_class = $is_current ? 'active' : 'done';
                                                }
                                            ?>
                                                <span class="flow-step <?php echo $step_class; ?>">
                                                    <i class="fas <?php echo $step['icon']; ?>"></i>
                                                    <?php echo $step['label']; ?>
                                                </span>
                                                <?php if ($step_num < 4): ?>
                                                    <span class="flow-arrow <?php echo $step_num < $current ? 'active' : ''; ?>">
                                                        <i class="fas fa-chevron-right"></i>
                                                    </span>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                    <td style="text-align:center;">
                                        <div style="display:flex; gap:5px; justify-content:center; flex-wrap:wrap;">
                                            <a href="detail.php?id=<?php echo $d['id']; ?>" class="btn-action btn-view" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($d['status'] == 'draft'): ?>
                                                <a href="edit_perangkat.php?id=<?php echo $d['id']; ?>" class="btn-action btn-edit" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="delete_perangkat.php?id=<?php echo $d['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Hapus dokumen ini?')" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                            <?php if (in_array($d['status'], ['pending_kepsek', 'pending_pengawas'])): ?>
                                                <span style="font-size:11px; color:#999; display:block; margin-top:2px;">
                                                    <i class="fas fa-hourglass-half"></i> Menunggu
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($d['status'] == 'terverifikasi' && file_exists($d['file_path'])): ?>
                                                <a href="<?php echo $d['file_path']; ?>" download class="btn-action btn-view" title="Download" style="background:#28a745;">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7">
                                        <div class="empty-state">
                                            <i class="fas fa-file-alt"></i>
                                            <h3>Belum Ada Dokumen</h3>
                                            <p>Anda belum mengupload perangkat pembelajaran.</p>
                                            <p style="margin-top:10px;">
                                                <button class="btn-ai" onclick="openAIModal()">
                                                    <i class="fas fa-robot"></i> Buat dengan AI
                                                </button>
                                                <a href="/website_perangkat/upload.php" class="btn-upload" style="display:inline-block; margin-left:8px;">
                                                    <i class="fas fa-upload"></i> Upload Manual
                                                </a>
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- INFORMASI ALUR -->
            <div style="margin-top:20px; background:#f8fafc; padding:16px 20px; border-radius:12px; border-left:4px solid #667eea;">
                <p style="font-size:13px; color:#4a5568; margin:0;">
                    <i class="fas fa-info-circle" style="color:#667eea;"></i>
                    <strong>Alur Persetujuan:</strong>
                    <span style="margin-left:8px;">
                        <span style="background:#f3f4f6; padding:2px 10px; border-radius:12px; font-size:12px;">📝 Draft</span>
                        <i class="fas fa-arrow-right" style="font-size:12px; color:#d1d5db;"></i>
                        <span style="background:#fef3c7; padding:2px 10px; border-radius:12px; font-size:12px;">⏳ Kepala Sekolah</span>
                        <i class="fas fa-arrow-right" style="font-size:12px; color:#d1d5db;"></i>
                        <span style="background:#fef3c7; padding:2px 10px; border-radius:12px; font-size:12px;">⏳ Pengawas</span>
                        <i class="fas fa-arrow-right" style="font-size:12px; color:#d1d5db;"></i>
                        <span style="background:#d1fae5; padding:2px 10px; border-radius:12px; font-size:12px;">✅ Terverifikasi</span>
                    </span>
                </p>
                <p style="font-size:12px; color:#6b7280; margin:6px 0 0 0;">
                    💡 <strong>Fitur AI:</strong> Buat perangkat ajar dengan cepat menggunakan bantuan kecerdasan buatan.
                </p>
            </div>
        </main>

        <!-- FOOTER -->
        <footer class="footer">
            <p>&copy; <?php echo date('Y'); ?> Pusat Perangkat Pembelajaran</p>
        </footer>
    </div>

    <!-- ========================================== -->
    <!-- AI MODAL -->
    <!-- ========================================== -->
    <div class="ai-modal-overlay" id="aiModal">
        <div class="ai-modal-box">
            <div class="modal-header">
                <h3>
                    <i class="fas fa-robot" style="color:#667eea;"></i> 
                    Buat Perangkat Ajar dengan AI
                    <span class="ai-badge">BETA</span>
                </h3>
                <button class="close-btn" onclick="closeAIModal()">&times;</button>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label>📋 Jenis Perangkat <span class="required">*</span></label>
                    <select name="ai_jenis" required>
                        <option value="">-- Pilih Jenis --</option>
                        <option value="rpp">📄 RPP (Rencana Pelaksanaan Pembelajaran)</option>
                        <option value="modul">📘 Modul Ajar</option>
                        <option value="ppt">📊 PPT / Slide Presentasi</option>
                        <option value="soal">📝 Soal / Evaluasi</option>
                        <option value="video">🎬 Video Pembelajaran</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>📝 Topik / Materi <span class="required">*</span></label>
                    <input type="text" name="ai_topik" required placeholder="Contoh: Sistem Persamaan Linear, Hukum Newton, Teks Eksposisi">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>🏫 Kelas <span class="required">*</span></label>
                        <select name="ai_kelas" required>
                            <option value="">-- Pilih Kelas --</option>
                            <option value="1">Kelas 1 SD</option>
                            <option value="2">Kelas 2 SD</option>
                            <option value="3">Kelas 3 SD</option>
                            <option value="4">Kelas 4 SD</option>
                            <option value="5">Kelas 5 SD</option>
                            <option value="6">Kelas 6 SD</option>
                            <option value="7">Kelas 7 SMP</option>
                            <option value="8">Kelas 8 SMP</option>
                            <option value="9">Kelas 9 SMP</option>
                            <option value="10">Kelas 10 SMA</option>
                            <option value="11">Kelas 11 SMA</option>
                            <option value="12">Kelas 12 SMA</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>📚 Mata Pelajaran <span class="required">*</span></label>
                        <select name="ai_mapel" required>
                            <option value="">-- Pilih Mapel --</option>
                            <option value="Matematika">Matematika</option>
                            <option value="Bahasa Indonesia">Bahasa Indonesia</option>
                            <option value="Bahasa Inggris">Bahasa Inggris</option>
                            <option value="IPA">IPA</option>
                            <option value="IPS">IPS</option>
                            <option value="Pendidikan Agama">Pendidikan Agama</option>
                            <option value="PKN">PKN</option>
                            <option value="Seni Budaya">Seni Budaya</option>
                            <option value="PJOK">PJOK</option>
                            <option value="Informatika">Informatika</option>
                        </select>
                    </div>
                </div>

                <button type="submit" name="ai_generate" class="btn-ai-generate">
                    <i class="fas fa-wand-magic-sparkles"></i> 
                    Generate dengan AI
                </button>
            </form>

            <!-- Hasil AI -->
            <div class="ai-result" id="aiResult">
                <div class="result-title">
                    <i class="fas fa-check-circle" style="color:#28a745;"></i>
                    Hasil Generate AI
                </div>

                <div class="result-item">
                    <div class="label">📝 Judul</div>
                    <div class="value" id="aiJudul"><?php echo $ai_judul; ?></div>
                </div>

                <div class="result-item">
                    <div class="label">📄 Deskripsi</div>
                    <div class="value" id="aiDeskripsi"><?php echo $ai_deskripsi; ?></div>
                </div>

                <div class="result-item">
                    <div class="label">🎯 Tujuan Pembelajaran</div>
                    <div class="value" id="aiTujuan"><?php echo $ai_tujuan; ?></div>
                </div>

                <div class="result-item">
                    <div class="label">📋 Langkah Pembelajaran</div>
                    <div class="value" id="aiLangkah"><?php echo $ai_langkah; ?></div>
                </div>

                <div class="result-item">
                    <div class="label">📊 Penilaian</div>
                    <div class="value" id="aiPenilaian"><?php echo $ai_penilaian; ?></div>
                </div>

                <div style="display:flex; flex-wrap:wrap; gap:8px; margin-top:12px;">
                    <button class="btn-save-result" onclick="saveAIResult()">
                        <i class="fas fa-save"></i> Simpan ke Draft
                    </button>
                    <button class="btn-edit-result" onclick="editAIResult()">
                        <i class="fas fa-edit"></i> Edit Manual
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SCRIPT -->
    <!-- ========================================== -->
    <script>
        // ============================================
        // AI MODAL
        // ============================================
        function openAIModal() {
            document.getElementById('aiModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeAIModal() {
            document.getElementById('aiModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        document.getElementById('aiModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAIModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAIModal();
            }
        });

        // ============================================
        // SAVE AI RESULT
        // ============================================
        function saveAIResult() {
            alert('✅ Dokumen hasil AI akan disimpan ke draft!\n\nSilakan cek di daftar dokumen Anda.');
            // Di implementasi nyata, ini akan menyimpan ke database
            // Untuk sekarang, hanya menampilkan notifikasi
            closeAIModal();
        }

        function editAIResult() {
            alert('✏️ Silakan edit konten yang dihasilkan AI sesuai kebutuhan Anda.\n\nKlik "Simpan ke Draft" setelah selesai mengedit.');
        }

        // ============================================
        // TAMPILKAN AI RESULT JIKA ADA
        // ============================================
        <?php if ($ai_result): ?>
        window.onload = function() {
            openAIModal();
            document.getElementById('aiResult').style.display = 'block';
            // Scroll ke hasil
            setTimeout(function() {
                document.getElementById('aiResult').scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 500);
        };
        <?php endif; ?>
    </script>
</body>
</html>