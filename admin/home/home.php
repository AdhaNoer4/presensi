<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "admin") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}
$judul = "Home";
include('../layouts/header.php');
require_once('../../config.php');

// Atur timezone (WIB, WITA, WIT)
date_default_timezone_set('Asia/Jakarta'); // WIB
// date_default_timezone_set('Asia/Makassar'); // WITA
// date_default_timezone_set('Asia/Jayapura'); // WIT

// Query total pegawai aktif
$pegawai = mysqli_query($connection, "SELECT pegawai.*, users.status FROM pegawai JOIN users ON pegawai.id = users.id_pegawai WHERE status = 'Aktif'");

$total_pegawai_aktif = mysqli_num_rows($pegawai);

// query jumlah hadir
$jml_hadir_query = mysqli_query($connection, "SELECT COUNT(*) AS jumlah_hadir FROM presensi WHERE DATE(tanggal_masuk) = CURDATE()");
// Ambil hasil
if ($jml_hadir_query) {
    $data = $jml_hadir_query->fetch_assoc();
    $jumlah_hadir = $data['jumlah_hadir'];
} else {
    $jumlah_hadir = 0; // Jika query gagal, tampilkan 0
}

// Query untuk menghitung jumlah alpa
$query_alpa = "SELECT COUNT(*) AS jumlah_alpa
    FROM users p
    LEFT JOIN presensi pr ON p.id_pegawai = pr.id_pegawai 
                            AND DATE(pr.tanggal_masuk) = CURDATE()
    WHERE pr.tanggal_masuk IS NULL";
$result_alpa = mysqli_query($connection, $query_alpa);
$data_alpa = $result_alpa->fetch_assoc();
$jumlah_alpa = $data_alpa['jumlah_alpa'];

// Query untuk menghitung jumlah ketidakhadiran dengan keterangan (sakit, izin, atau cuti)
$query_ketidakhadiran = "SELECT COUNT(*) AS jumlah_ketidakhadiran FROM ketidakhadiran WHERE DATE(tanggal) = CURDATE() AND keterangan IN ('sakit', 'izin', 'cuti')";
$result_ketidakhadiran = mysqli_query($connection, $query_ketidakhadiran);
$data_ketidakhadiran = $result_ketidakhadiran->fetch_assoc();
$jumlah_ketidakhadiran = $data_ketidakhadiran['jumlah_ketidakhadiran'];

?>


<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row row-deck row-cards">

            <div class="col-12">
                <div class="row row-cards">
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="bg-primary text-white avatar">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                                <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="font-weight-medium">
                                            Jumlah Pegawai Aktif
                                        </div>
                                        <div class="text-secondary">
                                            <?= $total_pegawai_aktif . ' Pegawai'; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="bg-green text-white avatar">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-users-plus">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M5 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                                <path d="M3 21v-2a4 4 0 0 1 4 -4h4c.96 0 1.84 .338 2.53 .901" />
                                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                                <path d="M16 19h6" />
                                                <path d="M19 16v6" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="font-weight-medium">
                                            Jumlah Hadir
                                        </div>
                                        <div class="text-secondary">
                                            <?= $jumlah_hadir . ' Pegawai'; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="bg-twitter text-white avatar">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user-x">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                                <path d="M6 21v-2a4 4 0 0 1 4 -4h3.5" />
                                                <path d="M22 22l-5 -5" />
                                                <path d="M17 22l5 -5" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="font-weight-medium">
                                            Jumlah Alpa
                                        </div>
                                        <div class="text-secondary">
                                            <?= $jumlah_alpa . ' Pegawai'; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="bg-facebook text-white avatar">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user-question">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                                <path d="M6 21v-2a4 4 0 0 1 4 -4h3.5" />
                                                <path d="M19 22v.01" />
                                                <path d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="font-weight-medium">
                                            Jumlah Sakit, Izin & Cuti
                                        </div>
                                        <div class="text-secondary">
                                            <?= $jumlah_ketidakhadiran . ' Pegawai'; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include('../layouts/footer.php') ?>