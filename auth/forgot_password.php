<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Autoload PHPMailer
require_once('../config.php');

// Set zona waktu PHP
date_default_timezone_set('Asia/Jakarta');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Buat koneksi ke database
    $pdo = new PDO('mysql:host=localhost;dbname=presensi', 'root', '');

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

            $_SESSION['berhasil'] = "Instruksi reset password telah dikirim ke email Anda.";
        } catch (Exception $e) {

            $_SESSION['gagal'] = "Gagal mengirim email: {$mail->ErrorInfo}";
        }
    } else {

        $_SESSION['gagal'] = "Email tidak ditemukan.";
    }
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password</title>
    <link href="<?= base_url('assets/css/tabler.min.css?1692870487') ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/css/tabler-vendors.min.css?1692870487') ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/css/demo.min.css?1692870487') ?>" rel="stylesheet" />
</head>

<body class=" d-flex flex-column">
    <script src="<?= base_url('assets/js/demo-theme.min.js?1692870487') ?>"></script>
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
            </div>
            <div class="card card-md">
                <div class="card-body">
                    <h2 class="h2 text-center mb-4">Lupa Password</h2>
                    <form action="forgot_password.php" method="POST" autocomplete="off" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Masukan e-mail anda yang terdaftar:</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="your@email.com" autocomplete="off" required>
                        </div>

                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary w-100">Kirim</button>
                        </div>
                        <div class="mt-3">
                            <a href="<?= base_url('auth/login.php') ?>">Ingat Passwordnya?</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="<?= base_url('assets/libs/apexcharts/dist/apexcharts.min.js?1692870487') ?>" defer></script>
    <script src="<?= base_url('assets/libs/jsvectormap/dist/js/jsvectormap.min.js?1692870487') ?>" defer></script>
    <script src="<?= base_url('assets/libs/jsvectormap/dist/maps/world.js?1692870487') ?>" defer></script>
    <script src="<?= base_url('assets/libs/jsvectormap/dist/maps/world-merc.js?1692870487') ?>" defer></script>
    <!-- Tabler Core -->
    <script src="<?= base_url('assets/js/tabler.min.js?1692870487') ?>" defer></script>
    <script src="<?= base_url('assets/js/demo.min.js?1692870487') ?>" defer></script>
    <!-- Sweetalert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if (isset($_SESSION["berhasil"])) { ?>
        <script>
            Swal.fire({
                icon: "success",
                title: "Berhasil",
                text: "<?= $_SESSION["berhasil"]; ?>",

            });
        </script>
    <?php unset($_SESSION["berhasil"]);
    } ?>

    <?php if (isset($_SESSION["gagal"])) { ?>
        <script>
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "<?= $_SESSION["gagal"]; ?>",

            });
        </script>
    <?php unset($_SESSION["gagal"]);
    } ?>
</body>

</html>