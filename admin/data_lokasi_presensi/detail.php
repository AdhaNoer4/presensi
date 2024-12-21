<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "admin") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}
$judul = "Detail Lokasi Presensi";
include('../layouts/header.php');
require_once('../../config.php');

$id = $_GET['id'];
$result = mysqli_query($connection, "SELECT * FROM lokasi_presensi WHERE id='$id'");
?>

<?php while ($lokasi = mysqli_fetch_array($result)) : ?>
    <div class="page-body">
        <div class="container-xl">

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <td>Nama Lokasi</td>
                                    <td>: <?= $lokasi['nama_lokasi']; ?></td>
                                </tr>
                                <tr>
                                    <td>Alamat Lokasi</td>
                                    <td>: <?= $lokasi['alamat_lokasi']; ?></td>
                                </tr>
                                <tr>
                                    <td>Tipe Lokasi</td>
                                    <td>: <?= $lokasi['tipe_lokasi']; ?></td>
                                </tr>
                                <tr>
                                    <td>Latitude</td>
                                    <td>: <?= $lokasi['latitude']; ?></td>
                                </tr>
                                <tr>
                                    <td>Longitude</td>
                                    <td>: <?= $lokasi['longitude']; ?></td>
                                </tr>
                                <tr>
                                    <td>Radius</td>
                                    <td>: <?= $lokasi['radius']; ?></td>
                                </tr>
                                <tr>
                                    <td>Zona Waktu</td>
                                    <td>: <?= $lokasi['zona_waktu']; ?></td>
                                </tr>
                                <tr>
                                    <td>Jam Masuk</td>
                                    <td>: <?= $lokasi['jam_masuk']; ?></td>
                                </tr>
                                <tr>
                                    <td>Jam Pulang</td>
                                    <td>: <?= $lokasi['jam_pulang']; ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d855.7608616722085!2d<?= $lokasi['longitude']; ?>!3d<?= $lokasi['latitude']; ?>!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sid!2sid!4v1734757176648!5m2!1sid!2sid" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php endwhile ?>

<?php include('../layouts/footer.php') ?>