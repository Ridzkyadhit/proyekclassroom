<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'; // Gunakan Guest jika username tidak ada di session
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
</head>
<body>
    <header>
        <h1>Google Classroom KW</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <?php if ($user_role == 'guru'): ?>
                <a href="buat_kelas.php" class="create-class-button">+ Buat Kelas</a>
            <?php endif; ?>
            <a href="logout.php">Logout (<?php echo $username; ?>)</a>
        </nav>
    </header>

    <main>
        <div class="dashboard-container">
            <?php if ($user_role == 'guru'): ?>
                <h2>Kelas yang Anda Ajar:</h2>
                <div class="class-grid">
                    <?php
                        // Query untuk mengambil kelas yang diajar oleh guru
                        $stmt = $conn->prepare("SELECT * FROM classes WHERE guru_id = ?");
                        $stmt->bind_param("i", $user_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        while ($row = $result->fetch_assoc()) {
                            echo "<div class='class-card'>";
                            echo "<h3><a href='kelas.php?id={$row['id']}'>{$row['nama_kelas']}</a></h3>";
                            echo "<p>Kode Kelas: {$row['kode_kelas']}</p>";
                            // Tambahkan informasi lain yang relevan (misalnya jumlah siswa, tugas terbaru)
                            echo "</div>";
                        }
                    ?>
                </div>
            <?php elseif ($user_role == 'siswa'): ?>
                <h2>Kelas yang Anda Ikuti:</h2>
                <div class="class-grid">
                    <?php
                        // Query untuk mengambil kelas yang diikuti oleh siswa
                        $stmt = $conn->prepare("SELECT c.* FROM classes c JOIN class_students cs ON c.id = cs.kelas_id WHERE cs.siswa_id = ?");
                        $stmt->bind_param("i", $user_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        while ($row = $result->fetch_assoc()) {
                            echo "<div class='class-card'>";
                            echo "<h3><a href='kelas.php?id={$row['id']}'>{$row['nama_kelas']}</a></h3>";
                            echo "<p>Kode Kelas: {$row['kode_kelas']}</p>";
                            // Tambahkan informasi lain yang relevan (misalnya tugas terbaru)
                            echo "</div>";
                        }
                    ?>
                </div>

                <div class="join-class-section">
                    <h3>Gabung Kelas:</h3>
                    <form method="POST" action="gabung_kelas.php">
                        <input type="text" name="kode_kelas" placeholder="Kode Kelas" required>
                        <button type="submit">Gabung Kelas</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
