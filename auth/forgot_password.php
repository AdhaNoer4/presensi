<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Autoload PHPMailer

// Set zona waktu PHP
date_default_timezone_set('Asia/Jakarta');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Buat koneksi ke database
    $pdo = new PDO('mysql:host=localhost;dbname=absen', 'root', '');

    // Periksa apakah email ada di database
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Generate token reset
        $token = bin2hex(random_bytes(16));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Simpan token dan waktu kedaluwarsa
        $stmt = $pdo->prepare('UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?');
        $stmt->execute([$token, $expires, $email]);

        // Kirim email menggunakan PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username     = 'adhanoerhidayah4@gmail.com';
            $mail->Password     = 'xlvp hfsh tgda asbw';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('adhanoerhidayah4@gmail.com', 'Presensi Online');
            $mail->addAddress($email);

            $mail->Subject = 'Reset Password';
            $mail->Body    = "Klik tautan berikut untuk reset password Anda: http://localhost/presensi/auth/reset_password.php?token=$token";

            $mail->send();
            echo "Instruksi reset password telah dikirim ke email Anda.";
        } catch (Exception $e) {
            echo "Gagal mengirim email: {$mail->ErrorInfo}";
        }
    } else {
        echo "Email tidak ditemukan.";
    }
}


?>




<form action="forgot_password.php" method="POST">
    <label for="email">Masukkan Email Anda:</label>
    <input type="email" id="email" name="email" required>
    <button type="submit">Kirim</button>
</form>