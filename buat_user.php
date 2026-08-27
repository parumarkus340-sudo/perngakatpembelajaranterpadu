<?php
// buat_user.php - Buat user baru dengan password yang benar
include_once 'config/database.php';

// Password yang ingin digunakan
$password = 'guru123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Data user
$name = 'Guru Baru';
$email = 'guru@sekolah.com';
$role = 'guru';
$sekolah = 'SMA Negeri 1';

// Hapus user lama jika ada
query("DELETE FROM users WHERE email = '$email'");

// Insert user baru
$sql = "INSERT INTO users (name, email, password, role, sekolah, is_active) VALUES (
    '$name', '$email', '$hashed_password', '$role', '$sekolah', 1
)";

if (query($sql)) {
    echo "✅ User berhasil dibuat!<br>";
    echo "Email: $email<br>";
    echo "Password: $password<br>";
    echo "<a href='login.php'>Login sekarang</a>";
} else {
    echo "❌ Gagal membuat user: " . mysqli_error($conn);
}
?>