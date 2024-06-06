<?php
session_start();
require_once 'koneksi.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $namaLengkap = $_POST['nama_lengkap'];
    $role = $_POST['role'];

    // Validasi input (tambahkan validasi sesuai kebutuhan)
    if (empty($username) || empty($password) || empty($confirmPassword) || empty($namaLengkap) || empty($role)) {
        $error = "Semua field harus diisi.";
    } elseif ($password != $confirmPassword) {
        $error = "Password dan konfirmasi password tidak cocok.";
    } else {
        // Cek apakah username sudah ada
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $error = "Username sudah terdaftar.";
        } else {
            // Hash password menggunakan password_hash
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Simpan data pengguna ke database
            $stmt = $conn->prepare("INSERT INTO users (username, password, role, nama_lengkap) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $hashedPassword, $role, $namaLengkap);

            if ($stmt->execute()) {
                header("Location: index.php"); // Redirect ke halaman login setelah berhasil mendaftar
                exit();
            } else {
                $error = "Terjadi kesalahan saat mendaftar.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Daftar</title>
    <link rel="stylesheet" href="./assets/css/auth.css">
</head>
<body>
    <div class="container">
        <h2>Daftar</h2>
        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" placeholder="Username" required><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Password" required><br>

            <label for="confirm_password">Konfirmasi Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Konfirmasi Password" required><br>

            <label for="nama_lengkap">Nama Lengkap:</label>
            <input type="text" id="nama_lengkap" name="nama_lengkap" placeholder="Nama Lengkap" required><br>

            <label for="role">Peran:</label>
            <select id="role" name="role" required>
                <option value="">Pilih Peran</option>
                <option value="guru">Guru</option>
                <option value="siswa">Siswa</option>
            </select><br>

            <button type="submit">Daftar</button>
        </form>
        <div class="additional-links">
            <a href="index.php">Kembali</a> | 
            <a href="login.php">Login</a>
        </div>
    </div>
</body>
</html>
