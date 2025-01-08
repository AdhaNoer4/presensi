<?php
ob_start();
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "pegawai") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}
$judul = "Ubah Password";
include('../layouts/header.php');
require_once('../../config.php');

$id = $_SESSION['id'];

if (isset($_POST['update'])) {
    $password_baru = password_hash($_POST['password_baru'], PASSWORD_DEFAULT);
    $ulangi_password_baru = password_hash($_POST['ulangi_password_baru'], PASSWORD_DEFAULT);

    $icon_validasi = "<svg  xmlns='http://www.w3.org/2000/svg'  width='24'  height='24'  viewBox='0 0 24 24'  fill='none'  stroke='currentColor'  stroke-width='2'  stroke-linecap='round'  stroke-linejoin='round'  class='icon icon-tabler icons-tabler-outline icon-tabler-check'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><path d='M5 12l5 5l10 -10' /></svg>";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (empty($_POST['password_baru'])) {
            $pesan_kesalahan[] = "$icon_validasi Password baru wajib diisi!";
        }
        if (empty($_POST['ulangi_password_baru'])) {
            $pesan_kesalahan[] = "$icon_validasi Ulangi password wajib diisi!";
        }

        if ($_POST['password_baru'] !== $_POST['ulangi_password_baru']) {
            $pesan_kesalahan[] = "$icon_validasi Password tidak cocok!";
        }

        if (!empty($pesan_kesalahan)) {
            $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
        } else {
            $pegawai = mysqli_query($connection, "UPDATE users SET
            password = '$password_baru'
            WHERE id = '$id';
            ");

            $_SESSION['berhasil'] = 'Password berhasil diubah!';
            header('Location: ../home/home.php');
            exit;
        }
    }
}

?>



<div class="page-body">
    <div class="container-xl">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form action="" method="post">
                        <div class="mb-3">
                            <label for="password_baru">Password Baru</label>
                            <div class="input-group input-group-flat">
                                <input type="password" name="password_baru" id="password" class="form-control">
                                <span class="input-group-text">
                                    <a href="#" class="link-secondary" title="Show password" data-bs-toggle="tooltip" id="togglePassword"><!-- Download SVG icon from http://tabler-icons.io/i/eye -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                            <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                        </svg>
                                    </a>
                                </span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password_baru">Ulangi Password</label>
                            <input type="password" name="ulangi_password_baru" class="form-control">
                        </div>
                        <input type="hidden" name="id" value="<?= $id ?>">
                        <button type="submit" class="btn btn-primary" name="update">Ubah</button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>



<?php include('../layouts/footer.php') ?>