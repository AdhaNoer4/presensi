<?php require_once('../config.php'); ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <!-- Sweetalert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Favicon -->
    <link rel="icon" href="<?= base_url('assets/img/login-pic.png') ?>" type="image/png">
    <link href="<?= base_url('assets/css/tabler.min.css?1692870487') ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/css/tabler-vendors.min.css?1692870487') ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/css/demo.min.css?1692870487') ?>" rel="stylesheet" />

</head>

<body class=" d-flex flex-column">
    <?php


    // Koneksi ke database
    $pdo = new PDO('mysql:host=localhost;dbname=presensi', 'root', '');

    // Validasi token hanya pada GET request
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (!isset($_GET['token'])) {
            echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Token Tidak Ditemukan',
            text: 'Token tidak ditemukan di URL.',
        }).then(() => {
            window.location.href = 'forgot_password.php';
        });
    </script>";
            exit;
        }

        $token = $_GET['token'];

        // Periksa token di database
        $stmt = $pdo->prepare('SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW()');
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if (!$user) {
            echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Token Tidak Valid',
                text: 'Token tidak valid atau telah kedaluwarsa.',
            }).then(() => {
                window.location.href = 'forgot_password.php';
            });
        </script>";
            exit;
        }
    }

    // Tangani form submit (POST request)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        // Perbarui password di database dan hapus token
        $stmt = $pdo->prepare('UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?');
        $stmt->execute([$password, $_POST['user_id']]);

        echo "<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: 'Password Anda telah berhasil direset.',
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'login.php';
        }
    });
</script>";
        exit;
    }
    ?>
    <script src="<?= base_url('assets/js/demo-theme.min.js?1692870487') ?>"></script>
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
            </div>
            <div class="card card-md">
                <div class="card-body">
                    <h2 class="h2 text-center mb-4">Reset Password</h2>
                    <form method="POST" autocomplete="off" novalidate>
                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                        <div class="mb-3">
                            <label class="form-label">Masukan Password Baru:</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password Baru" autocomplete="off" required>
                        </div>

                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary w-100">Reset Password</button>
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



</body>

</html>