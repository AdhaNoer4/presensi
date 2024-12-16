<?php
// Koneksi ke database
$pdo = new PDO('mysql:host=localhost;dbname=absen', 'root', '');

// Validasi token hanya pada GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['token'])) {
        die('Token tidak ditemukan.');
    }

    $token = $_GET['token'];

    // Periksa token di database
    $stmt = $pdo->prepare('SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW()');
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if (!$user) {
        die('Token tidak valid atau telah kedaluwarsa.');
    }
}

// Tangani form submit (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Perbarui password di database dan hapus token
    $stmt = $pdo->prepare('UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?');
    $stmt->execute([$password, $_POST['user_id']]);

    echo "Password Anda telah berhasil direset.";
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Reset Password</title>
</head>

<body>
    <form method="POST">
        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
        <label for="password">Password Baru:</label>
        <input type="password" name="password" required>
        <button type="submit">Reset Password</button>
    </form>
</body>

</html>