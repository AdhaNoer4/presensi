<?php
session_start();
ob_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "admin") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}
$judul = "Tambah Lokasi Presensi";
include('../layouts/header.php');
require_once('../../config.php');

if (isset($_POST['submit'])) {
    $nama_lokasi = htmlspecialchars($_POST['nama_lokasi']);
    $alamat_lokasi = htmlspecialchars($_POST['alamat_lokasi']);
    $tipe_lokasi = htmlspecialchars($_POST['tipe_lokasi']);
    $latitude = htmlspecialchars($_POST['latitude']);
    $longitude = htmlspecialchars($_POST['longitude']);
    $radius = htmlspecialchars($_POST['radius']);
    $zona_waktu = htmlspecialchars($_POST['zona_waktu']);
    $jam_masuk = htmlspecialchars($_POST['jam_masuk']);
    $jam_pulang = htmlspecialchars($_POST['jam_pulang']);

    $icon_validasi = "<svg  xmlns='http://www.w3.org/2000/svg'  width='24'  height='24'  viewBox='0 0 24 24'  fill='none'  stroke='currentColor'  stroke-width='2'  stroke-linecap='round'  stroke-linejoin='round'  class='icon icon-tabler icons-tabler-outline icon-tabler-check'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><path d='M5 12l5 5l10 -10' /></svg>";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (empty($nama_lokasi)) {
            $pesan_kesalahan[] = "$icon_validasi Nama lokasi wajib diisi!";
        }
        if (empty($alamat_lokasi)) {
            $pesan_kesalahan[] = "$icon_validasi Alamat lokasi wajib diisi!";
        }
        if (empty($tipe_lokasi)) {
            $pesan_kesalahan[] = "$icon_validasi Tipe lokasi wajib diisi!";
        }
        if (empty($latitude)) {
            $pesan_kesalahan[] = "$icon_validasi Latitude wajib diisi!";
        }
        if (empty($longitude)) {
            $pesan_kesalahan[] = "$icon_validasi Longitude wajib diisi!";
        }
        if (empty($radius)) {
            $pesan_kesalahan[] = "$icon_validasi Radius wajib diisi!";
        }
        if (empty($zona_waktu)) {
            $pesan_kesalahan[] = "$icon_validasi Zona waktu wajib diisi!";
        }
        if (empty($jam_masuk)) {
            $pesan_kesalahan[] = "$icon_validasi Jam masuk wajib diisi!";
        }
        if (empty($jam_pulang)) {
            $pesan_kesalahan[] = "$icon_validasi Jam pulang wajib diisi!";
        }

        if (!empty($pesan_kesalahan)) {
            $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
        } else {
            $result = mysqli_query($connection, "INSERT INTO lokasi_presensi(nama_lokasi, alamat_lokasi, tipe_lokasi, latitude, longitude, radius, zona_waktu, jam_masuk, jam_pulang) VALUES ('$nama_lokasi','$alamat_lokasi','$tipe_lokasi','$latitude','$longitude','$radius','$zona_waktu','$jam_masuk','$jam_pulang')");

            $_SESSION['berhasil'] = 'Data berhasil disimpan';
            header('Location: lokasi_presensi.php');
            exit;
        }
    }
}
?>

<div class="page-body">
    <div class="container-xl">
        <div class="card col-md-6">
            <div class="card-body">
                <form action="<?= base_url('admin/data_lokasi_presensi/tambah.php') ?>" method="POST">
                    <div class="mb-3">
                        <label for="nama_lokasi">Nama Lokasi</label>
                        <input type="text" name="nama_lokasi" class="form-control" value="<?php if (isset($_POST['nama_lokasi'])) echo $_POST['nama_lokasi'] ?>">
                    </div>
                    <div class="mb-3">
                        <label for="alamat_lokasi">Alamat Lokasi</label>
                        <input type="text" name="alamat_lokasi" class="form-control" value="<?php if (isset($_POST['alamat_lokasi'])) echo $_POST['alamat_lokasi'] ?>">
                    </div>
                    <div class="mb-3">
                        <label for="tipe_lokasi">Tipe Lokasi</label>
                        <select name="tipe_lokasi" class="form-control">
                            <option value="">--Pilih tipe lokasi--</option>
                            <option <?php if (isset($_POST['tipe_lokasi']) && $_POST['tipe_lokasi'] == 'Pusat') {
                                        echo 'selected';
                                    } ?> value="Pusat">Pusat</option>
                            <option <?php if (isset($_POST['tipe_lokasi']) && $_POST['tipe_lokasi'] == 'Cabang') {
                                        echo 'selected';
                                    } ?> value="Cabang">Cabang</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="latitude">Latitude</label>
                        <input type="text" name="latitude" class="form-control" value="<?php if (isset($_POST['latitude'])) echo $_POST['latitude'] ?>">
                    </div>
                    <div class="mb-3">
                        <label for="longitude">Longitude</label>
                        <input type="text" name="longitude" class="form-control" value="<?php if (isset($_POST['longitude'])) echo $_POST['longitude'] ?>">
                    </div>
                    <div class="mb-3">
                        <label for="radius">Radius</label>
                        <input type="number" name="radius" class="form-control" value="<?php if (isset($_POST['radius'])) echo $_POST['radius'] ?>">
                    </div>
                    <div class="mb-3">
                        <label for="zona_waktu">Zona Waktu</label>
                        <select name="zona_waktu" class="form-control">
                            <option value="">--Pilih Zona waktu--</option>
                            <option <?php if (isset($_POST['zona_waktu']) && $_POST['zona_waktu'] == 'WIB') {
                                        echo 'selected';
                                    } ?> value="WIB">WIB</option>
                            <option <?php if (isset($_POST['zona_waktu']) && $_POST['zona_waktu'] == 'WITA') {
                                        echo 'selected';
                                    } ?> value="WITA">WITA</option>
                            <option <?php if (isset($_POST['zona_waktu']) && $_POST['zona_waktu'] == 'WIT') {
                                        echo 'selected';
                                    } ?> value="WIT">WIT</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="jam_masuk">Jam masuk</label>
                        <input type="time" name="jam_masuk" class="form-control" value="<?php if (isset($_POST['jam_masuk'])) echo $_POST['jam_masuk'] ?>">
                    </div>
                    <div class="mb-3">
                        <label for="jam_pulang">Jam Pulang</label>
                        <input type="time" name="jam_pulang" class="form-control" value="<?php if (isset($_POST['jam_pulang'])) echo $_POST['jam_pulang'] ?>">
                    </div>
                    <button type="submit" class="btn btn-primary" name="submit">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>


<?php include('../layouts/footer.php') ?>