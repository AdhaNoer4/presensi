<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "pegawai") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}
include('../layouts/header.php');
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

        <div class="row justify-content-center align-items-center">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Presensi Masuk</div>
                    <div class="card-body">
                        <div class="parent_date fs-2 text-center justify-content-center">
                            <div id="tanggal_masuk"></div>
                            <div class="ms-2"></div>
                            <div id="bulan_masuk"></div>
                            <div class="ms-2"></div>
                            <div id="tahun_masuk"></div>
                        </div>

                        <div class="parent_clock fs-1 text-center fw-bold justify-content-center">
                            <div id="jam_masuk"></div>
                            :
                            <div id="menit_masuk"></div>
                            :
                            <div id="detik_masuk"></div>
                        </div>
                        <form action="" class="text-center mt-3">
                            <button type="submit" class="btn btn-success">Masuk</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Presensi Keluar</div>
                    <div class="card-body">
                        <div class="parent_date fs-2 text-center justify-content-center">
                            <div id="tanggal_keluar"></div>
                            <div class="ms-2"></div>
                            <div id="bulan_keluar"></div>
                            <div class="ms-2"></div>
                            <div id="tahun_keluar"></div>
                        </div>

                        <div class="parent_clock fs-1 text-center fw-bold justify-content-center">
                            <div id="jam_keluar"></div>
                            :
                            <div id="menit_keluar"></div>
                            :
                            <div id="detik_keluar"></div>
                        </div>
                        <form action="" class="text-center mt-3">
                            <button type="submit" class="btn btn-danger">Keluar</button>
                        </form>
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
</script>

<?php include('../layouts/footer.php') ?>