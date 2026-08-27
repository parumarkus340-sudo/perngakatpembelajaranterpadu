<?php
// presensi_siswa.php - Halaman Presensi Siswa
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'guru') {
    header('Location: login.php');
    exit;
}

include_once 'config/database.php';

$id_guru = $_SESSION['user_id'];
$sekolah_id = $_SESSION['sekolah_id'] ?? 0;
$tanggal = date('Y-m-d');

// Ambil kelas yang diajar oleh guru
$kelas_list = fetchAll("
    SELECT DISTINCT k.* 
    FROM kelas k
    JOIN jadwal_mengajar j ON k.id = j.id_kelas
    WHERE j.id_guru = $id_guru
    ORDER BY k.jenjang, k.nama_kelas
");

// Ambil semua kelas (jika belum ada jadwal)
if (empty($kelas_list)) {
    $kelas_list = fetchAll("SELECT * FROM kelas ORDER BY jenjang, nama_kelas");
}

// Proses simpan presensi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['simpan'])) {
    $id_kelas = (int)$_POST['id_kelas'];
    $total_siswa = (int)$_POST['total_siswa'];
    $hadir = (int)$_POST['hadir'];
    $sakit = (int)$_POST['sakit'];
    $izin = (int)$_POST['izin'];
    $alpa = (int)$_POST['alpa'];
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    
    // Cek apakah sudah ada presensi hari ini untuk kelas ini
    $cek = fetchOne("SELECT id FROM presensi_siswa WHERE id_sekolah = $sekolah_id AND id_guru = $id_guru AND id_kelas = $id_kelas AND tanggal = '$tanggal'");
    
    if ($cek) {
        $sql = "UPDATE presensi_siswa SET 
                    total_siswa = $total_siswa,
                    hadir = $hadir,
                    sakit = $sakit,
                    izin = $izin,
                    alpa = $alpa,
                    keterangan = '$keterangan'
                WHERE id = " . $cek['id'];
    } else {
        $sql = "INSERT INTO presensi_siswa (id_sekolah, id_guru, id_kelas, tanggal, total_siswa, hadir, sakit, izin, alpa, keterangan) 
                VALUES ($sekolah_id, $id_guru, $id_kelas, '$tanggal', $total_siswa, $hadir, $sakit, $izin, $alpa, '$keterangan')";
    }
    
    if (query($sql)) {
        $success = "✅ Presensi siswa berhasil disimpan!";
    } else {
        $error = "❌ Gagal menyimpan presensi!";
    }
}

// Ambil data presensi hari ini
$presensi_hari_ini = fetchAll("
    SELECT p.*, k.nama_kelas, k.jenjang 
    FROM presensi_siswa p
    JOIN kelas k ON p.id_kelas = k.id
    WHERE p.id_guru = $id_guru AND p.tanggal = '$tanggal'
");
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
</head>
</head>
<body>
    <div class="container">
        <header class="header">
            <div>
                <h1><i class="fas fa-user-graduate"></i> Presensi Siswa</h1>
                <p>Input kehadiran siswa per kelas</p>
            </div>
            <div>
                <i class="fas fa-user"></i> <?php echo $_SESSION['name']; ?>
            </div>
        </header>

         <!-- ===== NAVIGASI PAKAI NAVBAR.PHP ===== -->
        <?php include_once 'navbar.php'; ?>
        <!-- ===================================== -->

        <main>
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Form Presensi Siswa -->
            <div class="form-card">
                <div class="form-title">📋 Input Presensi Siswa - <?php echo date('l, d F Y'); ?></div>
                <form method="POST">
                    <div class="form-group">
                        <label>Pilih Kelas</label>
                        <select name="id_kelas" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($kelas_list as $k): ?>
                                <option value="<?php echo $k['id']; ?>">
                                    Kelas <?php echo $k['nama_kelas']; ?> (<?php echo $k['jenjang']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Total Siswa</label>
                            <input type="number" name="total_siswa" min="0" required placeholder="Jumlah">
                        </div>
                        <div class="form-group">
                            <label>Hadir</label>
                            <input type="number" name="hadir" min="0" required placeholder="Hadir">
                        </div>
                        <div class="form-group">
                            <label>Sakit</label>
                            <input type="number" name="sakit" min="0" placeholder="Sakit">
                        </div>
                        <div class="form-group">
                            <label>Izin</label>
                            <input type="number" name="izin" min="0" placeholder="Izin">
                        </div>
                        <div class="form-group">
                            <label>Alpa</label>
                            <input type="number" name="alpa" min="0" placeholder="Alpa">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" rows="2" placeholder="Catatan tambahan..."></textarea>
                    </div>
                    
                    <button type="submit" name="simpan" class="btn-simpan">
                        <i class="fas fa-save"></i> Simpan Presensi
                    </button>
                </form>
            </div>

            <!-- Presensi Hari Ini -->
            <h3 style="margin-bottom:12px;">📊 Presensi Hari Ini</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Kelas</th>
                            <th>Total</th>
                            <th>✅ Hadir</th>
                            <th>🤒 Sakit</th>
                            <th>📝 Izin</th>
                            <th>❌ Alpa</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($presensi_hari_ini) > 0): ?>
                            <?php foreach ($presensi_hari_ini as $p): ?>
                            <tr>
                                <td><strong>Kelas <?php echo $p['nama_kelas']; ?> (<?php echo $p['jenjang']; ?>)</strong></td>
                                <td><?php echo $p['total_siswa']; ?></td>
                                <td style="color:#28a745;"><?php echo $p['hadir']; ?></td>
                                <td style="color:#d97706;"><?php echo $p['sakit']; ?></td>
                                <td style="color:#2563eb;"><?php echo $p['izin']; ?></td>
                                <td style="color:#dc3545;"><?php echo $p['alpa']; ?></td>
                                <td><?php echo $p['keterangan'] ?? '-'; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" style="text-align:center; color:#888;">Belum ada presensi hari ini</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>

        <footer class="footer">
            <p>&copy; <?php echo date('Y'); ?> Pusat Perangkat Pembelajaran</p>
        </footer>
    </div>
</body>
</html>