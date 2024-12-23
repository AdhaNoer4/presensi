<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "admin") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}
$judul = "Detail Pegawai";
include('../layouts/header.php');
require_once('../../config.php');


$id = $_GET['id'];
$result = mysqli_query($connection, "SELECT users.id_pegawai, users.username, users.password, users.status, users.role, users.email, pegawai.* FROM users JOIN pegawai ON users.id_pegawai = pegawai.id WHERE pegawai.id = '$id'");
?>

<?php while ($pegawai = mysqli_fetch_array($result)) : ?>

    <div class="page-body">
        <div class="container-xl">

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <td>Nama</td>
                                    <td>: <?= $pegawai['nama'] ?></td>
                                </tr>
                                <tr>
                                    <td>Jenis Kelamin</td>
                                    <td>: <?= $pegawai['jenis_kelamin'] ?></td>
                                </tr>
                                <tr>
                                    <td>Alamat</td>
                                    <td>: <?= $pegawai['alamat'] ?></td>
                                </tr>
                                <tr>
                                    <td>No. Handphone</td>
                                    <td>: <?= $pegawai['no_handphone'] ?></td>
                                </tr>
                                <tr>
                                    <td>Jabatan</td>
                                    <td>: <?= $pegawai['jabatan'] ?></td>
                                </tr>
                                <tr>
                                    <td>Username</td>
                                    <td>: <?= $pegawai['username'] ?></td>
                                </tr>
                                <tr>
                                    <td>Role</td>
                                    <td>: <?= $pegawai['role'] ?></td>
                                </tr>
                                <tr>
                                    <td>Lokasi Presensi</td>
                                    <td>: <?= $pegawai['lokasi_presensi'] ?></td>
                                </tr>
                                <tr>
                                    <td>Status</td>
                                    <td>: <?= $pegawai['status'] ?></td>
                                </tr>
                                <tr>
                                    <td>E-mail</td>
                                    <td>: <?= $pegawai['email'] ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <img src="<?= base_url('assets/img/foto_pegawai/' . $pegawai['foto']) ?>" alt="foto <?= $pegawai['nama'] ?>" class="w-50 rounded-4">
                </div>
            </div>
        </div>
    </div>

<?php endwhile; ?>

<?php include('../layouts/footer.php') ?>