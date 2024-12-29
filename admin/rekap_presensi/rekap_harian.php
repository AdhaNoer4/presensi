<?php
ob_start();
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
    exit;
} else if ($_SESSION["role"] !== "admin") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}

$judul = "Rekap Presensi";
include('../layouts/header.php');
include_once("../../config.php");



if (empty($_GET['tanggal_dari'])) {
    
    $result = mysqli_query($connection, "SELECT presensi.*, pegawai.nama, pegawai.lokasi_presensi FROM presensi JOIN pegawai ON presensi.id_pegawai = pegawai.id ORDER BY tanggal_masuk DESC");
} else {
    
    $tanggal_dari = $_GET['tanggal_dari'];
    $tanggal_sampai = $_GET['tanggal_sampai'];
    $result = mysqli_query($connection, "SELECT presensi.*, pegawai.nama pegawai.lokasi_presensi FROM presensi JOIN pegawai ON presensi.id_pegawai = pegawai.id WHERE tanggal_masuk BETWEEN '$tanggal_dari' AND '$tanggal_sampai' ORDER BY tanggal_masuk DESC");
}

?>
<div class="page-body">
    <div class="container-xl">

        <div class="row">
            <div class="col-md-2">
                <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    Export Excel
                </button>

            </div>
            <div class="col-md-10">
                <form method="GET">
                    <div class="input-group">

                        <input type="date" class="form-control" name="tanggal_dari">
                        <input type="date" class="form-control" name="tanggal_sampai">
                        <button type="submit" class="btn btn-primary">Tampilkan</button>
                    </div>
                </form>
            </div>
        </div>

        <table class="table table-bordered">
            <tr class="text-center">
                <th>No.</th>
                <th>Nama</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Jam Pulang</th>
                <th>Total Jam</th>
                <th>Total Terlambat</th>
            </tr>

<?php if(mysqli_num_rows($result) === 0){ ?>
    <tr>
<td colspan="6">Data rekap presensi masih kosong</td>

    </tr>
    <?php } else { ?>
    <?php $no = 1;
    while ($rekap = mysqli_fetch_array($result)) :
        //menghitung total jam kerja
        $jam_tanggal_masuk = date('Y-m-d H:i:s', strtotime($rekap['tanggal_masuk'] . ' ' . $rekap['jam_masuk']));
        $jam_tanggal_keluar = date('Y-m-d H:i:s', strtotime($rekap['tanggal_keluar'] . ' ' . $rekap['jam_keluar']));
        $timestamp_masuk = strtotime($jam_tanggal_masuk);
        $timestamp_keluar = strtotime($jam_tanggal_keluar);
        $selisih = $timestamp_keluar - $timestamp_masuk;
        $total_jam_kerja = floor($selisih / 3600);
        $selisih -= $total_jam_kerja * 3600;
        $selisih_menit_kerja = floor($selisih / 60);

        //menghitung total jam terlambat
        $lokasi_presensi = $rekap['lokasi_presensi'];
$lokasi = mysqli_query($connection, "SELECT * FROM lokasi_presensi WHERE nama_lokasi = '$lokasi_presensi'");

while ($lokasi_result = mysqli_fetch_array($lokasi)) :
    $jam_masuk_kantor = date('H:i:s', strtotime($lokasi_result['jam_masuk']));
endwhile;
        $jam_masuk = date('H:i:s', strtotime($rekap['jam_masuk']));
        $timestamp_jam_masuk_real = strtotime($jam_masuk);
        $timestamp_jam_masuk_kantor = strtotime($jam_masuk_kantor);
        $terlambat = $timestamp_jam_masuk_real - $timestamp_jam_masuk_kantor;

        // Set flag untuk status keterlambatan
        $is_ontime = $terlambat <= 0;

        if ($terlambat > 0) {
            $total_jam_terlambat = floor($terlambat / 3600);
            $terlambat -= $total_jam_terlambat * 3600;
            $selisih_menit_terlambat = floor($terlambat / 60);
        } else {
            $total_jam_terlambat = 0;
            $selisih_menit_terlambat = 0;
        }
    ?>

        <tr>
            <td> <?= $no++ ?></td>
            <td><?= $rekap['nama'] ?></td>
            <td> <?= date('d F Y', strtotime($rekap['tanggal_masuk']))  ?></td>
            <td class="text-center"> <?= $rekap['jam_masuk'] ?></td>
            <td class="text-center"> <?= $rekap['jam_keluar'] ?></td>
            <td class="text-center">
                <?php if ($rekap['tanggal_keluar'] == '0000-00-00'): ?>
                    <span>0 Jam 0 Menit</span>
                <?php else: ?>
                    <?= $total_jam_kerja . ' Jam ' . $selisih_menit_kerja . ' Menit' ?>
                <?php endif; ?>
            </td>
            <td class="text-center">
                <?php if ($is_ontime): ?>
                    <span class="badge bg-success">On Time</span>
                <?php else: ?>
                    <?= $total_jam_terlambat . ' Jam ' . $selisih_menit_terlambat . ' Menit' ?>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
<?php } ?>
</table>
</div>
</div>
<div class="modal" id="exampleModal" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Adipisci animi beatae delectus deleniti dolorem eveniet facere fuga iste nemo nesciunt nihil odio perspiciatis, quia quis reprehenderit sit tempora totam unde.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn me-auto" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Save changes</button>
            </div>
        </div>
    </div>
</div>

<?php include('../layouts/footer.php') ?>