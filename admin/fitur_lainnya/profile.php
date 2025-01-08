<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "admin") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}
$judul = "Profile";
include('../layouts/header.php');
require_once('../../config.php');

$id = $_SESSION['id'];
$result = mysqli_query($connection, "SELECT users.id_pegawai, users.username, users.email, users.status, users.role, pegawai.* FROM users JOIN pegawai ON users.id_pegawai = pegawai.id WHERE pegawai.id = '$id' ");

?>

<?php while ($pegawai = mysqli_fetch_array($result)) : ?>

    <div class="page-body">
        <div class="container-xl">

            <div class="row justify-content-center">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body ">
                            <div class="text-center">

                                <img src="<?= base_url('assets/img/foto_pegawai/') . $pegawai['foto'] ?>" alt="<?= $pegawai['foto'] ?>" class="rounded-circle w-50 ">
                            </div>

                            <table class="table mt-3">
                                <tr>
                                    <td>Nama</td>
                                    <td>: <?= $pegawai['nama']; ?></td>
                                </tr>
                                <tr>
                                    <td>Jenis Kelamain</td>
                                    <td>: <?= $pegawai['jenis_kelamin']; ?></td>
                                </tr>
                                <tr>
                                    <td>alamat</td>
                                    <td>: <?= $pegawai['alamat']; ?></td>
                                </tr>
                                <tr>
                                    <td>No. Handphone</td>
                                    <td>: <?= $pegawai['no_handphone']; ?></td>
                                </tr>
                                <tr>
                                    <td>Jabatan</td>
                                    <td>: <?= $pegawai['jabatan']; ?></td>
                                </tr>
                                <tr>
                                    <td>Username</td>
                                    <td>: <?= $pegawai['username']; ?></td>
                                </tr>
                                <tr>
                                    <td>Role</td>
                                    <td>: <?= $pegawai['role']; ?></td>
                                </tr>
                                <tr>
                                    <td>Lokasi Presensi</td>
                                    <td>: <?= $pegawai['lokasi_presensi']; ?></td>
                                </tr>
                                <tr>
                                    <td>Status</td>
                                    <td>: <?= $pegawai['status']; ?></td>
                                </tr>
                                <tr>
                                    <td>E-mail</td>
                                    <td>: <?= $pegawai['email']; ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<?php endwhile; ?>
<?php include('../layouts/footer.php') ?>