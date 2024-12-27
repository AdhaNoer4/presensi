<?php
ob_start();
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "admin") {
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
                            <input type="password" name="password_baru" class="form-control">
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