<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "pegawai") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}

$judul = 'Home';
include('../layouts/header.php');
include_once("../../config.php");


$lokasi_presensi = $_SESSION['lokasi_presensi'];
$result = mysqli_query($connection, "SELECT * FROM lokasi_presensi WHERE nama_lokasi = '$lokasi_presensi'");

while ($lokasi = mysqli_fetch_array($result)) {
    $latitude_kantor = $lokasi['latitude'];
    $longitude_kantor = $lokasi['longitude'];
    $radius = $lokasi['radius'];
    $zona_waktu = $lokasi['zona_waktu'];
    $jam_pulang = $lokasi['jam_pulang'];
}
if ($zona_waktu == 'WIB') {
    date_default_timezone_set('Asia/Jakarta');
} elseif ($zona_waktu == 'WITA') {
    date_default_timezone_set('Asia/Makassar');
} elseif ($zona_waktu == 'WIT') {
    date_default_timezone_set('Asia/Jayapura');
}
?>
<style>
    .parent_date {
        display: grid;
        grid-template-columns: auto auto auto auto auto;
    }

    .parent_clock {
        display: grid;
        grid-template-columns: auto auto auto auto auto;
    }
</style>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row justify-content-center align-items-stretch">
            <!-- Card Presensi Masuk -->
            <div class="col-md-4">
    <div class="card h-100">
        <div class="card-header">Presensi Masuk</div>
        <div class="card-body d-flex flex-column justify-content-center align-items-center">

            <?php
            $id_pegawai = $_SESSION['id'];
            $tanggal_hari_ini = date('Y-m-d');

            // Query untuk cek presensi masuk
            $cek_presensi_masuk = mysqli_query($connection, "SELECT * FROM presensi WHERE id_pegawai = '$id_pegawai' AND tanggal_masuk = '$tanggal_hari_ini'");

            // Query untuk cek status dinas
            $cek_status_dinas = mysqli_query($connection, "SELECT status FROM users WHERE id_pegawai = '$id_pegawai' AND status = 'dinas'");
            $is_dinas = mysqli_num_rows($cek_status_dinas) > 0; // True jika status dinas
            ?>
            <?php if (mysqli_num_rows($cek_presensi_masuk) == 0) { ?>

                <!-- Bagian Tanggal -->
                <div class="parent_date fs-2 text-center">
                    <div id="tanggal_masuk"></div>
                    <div class="ms-2"></div>
                    <div id="bulan_masuk"></div>
                    <div class="ms-2"></div>
                    <div id="tahun_masuk"></div>
                </div>
                <!-- Bagian Jam -->
                <div class="parent_clock fs-1 text-center fw-bold">
                    <div id="jam_masuk"></div>:
                    <div id="menit_masuk"></div>:
                    <div id="detik_masuk"></div>
                </div>
        </div>
        <div class="card-footer text-center">
            <!-- Tombol Presensi -->
            <form method="POST" action="<?= base_url('pegawai/presensi/presensi_masuk.php') ?>">
                <input type="hidden" name="latitude_pegawai" id="latitude_pegawai">
                <input type="hidden" name="longitude_pegawai" id="longitude_pegawai">
                <input type="hidden" value="<?= $latitude_kantor ?>" name="latitude_kantor">
                <input type="hidden" value="<?= $longitude_kantor ?>" name="longitude_kantor">
                <input type="hidden" value="<?= $radius ?>" name="radius">
                <input type="hidden" value="<?= $zona_waktu ?>" name="zona_waktu">
                <input type="hidden" value="<?= date('Y-m-d') ?>" name="tanggal_masuk">
                <input type="hidden" value="<?= date('H:i:s') ?>" name="jam_masuk">
                <button type="submit" class="btn btn-success" name="tombol_masuk">Masuk</button>
            </form>

            <!-- Tombol Absen Perjalanan Bisnis -->

            

                <?php if ($is_dinas) { ?>
                <div class="text-center mt-3">
                    <a href="<?= base_url('pegawai/presensi/absen_perjalanan.php') ?>" class="btn btn-primary">Absen Perjalanan Bisnis</a>
                </div>
            <?php } ?>
        <?php } else { ?>
            <i class="fa-regular fa-circle-check fa-4x text-success"></i>
            <h4 class="my-3">Anda telah melakukan presensi masuk</h4>
        <?php } ?>
            
            
        </div>
    </div>
</div>


            <!-- Card Presensi Keluar -->
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-header text-center">Presensi Keluar</div>
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <?php
                        $ambil_data_presensi  = mysqli_query($connection, "SELECT * FROM presensi WHERE id_pegawai = '$id_pegawai' AND tanggal_masuk = '$tanggal_hari_ini'");
                        ?>
                        <?php $waktu_sekarang = date('H:i:s');

                        if (strtotime($waktu_sekarang) <= strtotime($jam_pulang)) { ?>
                            <i class="fa-regular fa-circle-xmark fa-4x text-danger"></i>
                            <h4 class="my-3">Belum waktunya pulang</h4>


                        <?php } elseif (strtotime($waktu_sekarang) >= strtotime($jam_pulang) && mysqli_num_rows($ambil_data_presensi) == 0) { ?>
                            <i class="fa-regular fa-circle-xmark fa-4x text-danger"></i>
                            <h4 class="my-3">Silahkan melakukan presensi masuk terlebih dahulu</h4>

                        <?php } else { ?>

                            <?php while ($cek_presensi_keluar = mysqli_fetch_array($ambil_data_presensi)) { ?>
                                <?php if (($cek_presensi_keluar['tanggal_masuk']) && $cek_presensi_keluar['tanggal_keluar'] == '0000-00-00') { ?>

                                    <!-- Bagian Tanggal -->
                                    <div class="parent_date fs-2 text-center">
                                        <div id="tanggal_keluar"></div>
                                        <div class="ms-2"></div>
                                        <div id="bulan_keluar"></div>
                                        <div class="ms-2"></div>
                                        <div id="tahun_keluar"></div>
                                    </div>
                                    <!-- Bagian Jam -->
                                    <div class="parent_clock fs-1 text-center fw-bold">
                                        <div id="jam_keluar"></div>:
                                        <div id="menit_keluar"></div>:
                                        <div id="detik_keluar"></div>
                                    </div>
                    </div>
                    <div class="card-footer text-center">
                        <!-- Tombol Presensi -->
                        <form method="POST" action="<?= base_url('pegawai/presensi/presensi_keluar.php') ?>">
                            <input type="hidden" name='id' value="<?= $cek_presensi_keluar['id'] ?>">
                            <input type="hidden" name="latitude_pegawai" id="latitude_pegawai">
                            <input type="hidden" name="longitude_pegawai" id="longitude_pegawai">
                            <input type="hidden" value="<?= $latitude_kantor ?>" name="latitude_kantor">
                            <input type="hidden" value="<?= $longitude_kantor ?>" name="longitude_kantor">
                            <input type="hidden" value="<?= $radius ?>" name="radius">
                            <input type="hidden" value="<?= $zona_waktu ?>" name="zona_waktu">
                            <input type="hidden" value="<?= date('Y-m-d') ?>" name="tanggal_keluar">
                            <input type="hidden" value="<?= date('H:i:s') ?>" name="jam_keluar">
                            <button type="submit" name="tombol-keluar" class="btn btn-danger">Keluar</button>
                        </form>
                        <?php if($cek_presensi_keluar['jenis_absensi'] == 'Perjalanan Bisnis'){ ?>
                        <div class="text-center mt-3">
                            <a href="<?= base_url('pegawai/presensi/absen_perjalanan_keluar.php') ?>" class="btn btn-primary">Absen Keluar Perjalanan Bisnis</a>
                        </div>
                        <?php } ?>
                    <?php } else { ?>
                        <i class="fa-regular fa-circle-check fa-4x text-success"></i>
                        <h4 class="my-3">Anda telah melakukan presensi keluar</h4>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



</div>
</div>
</div>

<!-- Script Waktu real time -->
<script>
    // Set waktu di card Presensi masuk
    window.setTimeout("waktuMasuk()", 1000);
    namaBulan = [
        "Januari",
        "Februari",
        "Maret",
        "April",
        "Mei",
        "Juni",
        "Juli",
        "Agustus",
        "September",
        "Oktober",
        "November",
        "Desember",
    ];

    function waktuMasuk() {
        const waktu = new Date();
        setTimeout("waktuMasuk()", 1000);
        document.getElementById("tanggal_masuk").innerHTML = waktu.getDate();
        document.getElementById("bulan_masuk").innerHTML = namaBulan[waktu.getMonth()];
        document.getElementById("tahun_masuk").innerHTML = waktu.getFullYear();
        document.getElementById("jam_masuk").innerHTML = waktu.getHours();
        document.getElementById("menit_masuk").innerHTML = waktu.getMinutes();
        document.getElementById("detik_masuk").innerHTML = waktu.getSeconds();
    }
    // Set waktu di card Presensi keluar
    window.setTimeout("waktuKeluar()", 1000);

    function waktuKeluar() {
        const waktu = new Date();
        setTimeout("waktuKeluar()", 1000);
        document.getElementById("tanggal_keluar").innerHTML = waktu.getDate();
        document.getElementById("bulan_keluar").innerHTML = namaBulan[waktu.getMonth()];
        document.getElementById("tahun_keluar").innerHTML = waktu.getFullYear();
        document.getElementById("jam_keluar").innerHTML = waktu.getHours();
        document.getElementById("menit_keluar").innerHTML = waktu.getMinutes();
        document.getElementById("detik_keluar").innerHTML = waktu.getSeconds();
    }

    getLocation();

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else {
            alert("Browser anda tidak mendukung Geolocation")
        }
    }

    function showPosition(position) {
        $("#latitude_pegawai").val(position.coords.latitude);
        $("#longitude_pegawai").val(position.coords.longitude);


    }
</script>

<?php include('../layouts/footer.php') ?>