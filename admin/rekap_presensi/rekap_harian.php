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

$judul = "Rekap Presensi Harian";
include('../layouts/header.php');
include_once("../../config.php");

$tanggal_hari_ini = date('Y-m-d');
$search_name = isset($_GET['search_name']) ? mysqli_real_escape_string($connection, $_GET['search_name']) : '';
$tanggal_dari = isset($_GET['tanggal_dari']) ? $_GET['tanggal_dari'] : '';
$tanggal_sampai = isset($_GET['tanggal_sampai']) ? $_GET['tanggal_sampai'] : '';

if (empty($tanggal_dari)) {
    $query = "SELECT presensi.*, pegawai.nama, pegawai.lokasi_presensi 
        FROM presensi 
        JOIN pegawai ON presensi.id_pegawai = pegawai.id 
        WHERE tanggal_masuk = '$tanggal_hari_ini'";

    if (!empty($search_name)) {
        $query .= " AND pegawai.nama LIKE '%$search_name%'";
    }

    $query .= " ORDER BY tanggal_masuk DESC";
} else {
    $query = "SELECT presensi.*, pegawai.nama, pegawai.lokasi_presensi 
        FROM presensi 
        JOIN pegawai ON presensi.id_pegawai = pegawai.id 
        WHERE tanggal_masuk BETWEEN '$tanggal_dari' AND '$tanggal_sampai'";

    if (!empty($search_name)) {
        $query .= " AND pegawai.nama LIKE '%$search_name%'";
    }

    $query .= " ORDER BY tanggal_masuk DESC";
}

$result = mysqli_query($connection, $query);
?>
<div class="page-body">
    <div class="container-xl">
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <!-- Tombol Export Excel -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            Export Excel
                        </button>

                        <!-- Form Cari Nama -->
                        <form method="GET" class="d-flex align-items-center">
                            <!-- Hidden inputs untuk mempertahankan filter tanggal -->
                            <?php if (!empty($tanggal_dari) && !empty($tanggal_sampai)) : ?>
                                <input type="hidden" name="tanggal_dari" value="<?= $tanggal_dari ?>">
                                <input type="hidden" name="tanggal_sampai" value="<?= $tanggal_sampai ?>">
                            <?php endif; ?>

                            <!-- Input dan Button Cari Nama -->
                            <div class="input-group">
                                <input type="text" class="form-control" name="search_name" placeholder="Cari nama pegawai..." value="<?= htmlspecialchars($search_name) ?>">
                                <button type="submit" class="btn btn-secondary">Cari Nama</button>
                            </div>
                        </form>
                    </div>

                    <div class="card-body">
                        <form method="GET">
                            <div class="row g-3 align-items-center mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Tanggal Dari</label>
                                    <input type="date" class="form-control" name="tanggal_dari" value="<?= $tanggal_dari ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tanggal Sampai</label>
                                    <input type="date" class="form-control" name="tanggal_sampai" value="<?= $tanggal_sampai ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary w-100">Filter Tanggal</button>
                                </div>
                            </div>
                        </form>


                    </div>
                </div>

                <?php if (empty($tanggal_dari)) : ?>
                    <span>Rekap Presensi Tanggal: <?= date('d F Y') ?></span>
                <?php else : ?>
                    <span>Rekap Presensi Tanggal: <?= date('d F Y', strtotime($tanggal_dari)) . ' sampai ' . date('d F Y', strtotime($tanggal_sampai)) ?></span>
                <?php endif; ?>

                <table class="table table-bordered mt-2">
                    <tr class="text-center">
                        <th>No.</th>
                        <th>Nama</th>
                        <th>Tanggal</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Total Jam</th>
                        <th>Total Terlambat</th>
                    </tr>

                    <?php if (mysqli_num_rows($result) === 0) { ?>
                        <tr>
                            <td colspan="7" class="text-center">Data rekap presensi masih kosong</td>
                        </tr>
                    <?php } else { ?>
                        <?php $no = 1;
                        while ($rekap = mysqli_fetch_array($result)) :
                            $jam_tanggal_masuk = date('Y-m-d H:i:s', strtotime($rekap['tanggal_masuk'] . ' ' . $rekap['jam_masuk']));
                            $jam_tanggal_keluar = date('Y-m-d H:i:s', strtotime($rekap['tanggal_keluar'] . ' ' . $rekap['jam_keluar']));
                            $timestamp_masuk = strtotime($jam_tanggal_masuk);
                            $timestamp_keluar = strtotime($jam_tanggal_keluar);
                            $selisih = $timestamp_keluar - $timestamp_masuk;
                            $total_jam_kerja = floor($selisih / 3600);
                            $selisih -= $total_jam_kerja * 3600;
                            $selisih_menit_kerja = floor($selisih / 60);

                            $lokasi_presensi = $rekap['lokasi_presensi'];
                            $lokasi = mysqli_query($connection, "SELECT * FROM lokasi_presensi WHERE nama_lokasi = '$lokasi_presensi'");

                            while ($lokasi_result = mysqli_fetch_array($lokasi)) :
                                $jam_masuk_kantor = date('H:i:s', strtotime($lokasi_result['jam_masuk']));
                            endwhile;
                            $jam_masuk = date('H:i:s', strtotime($rekap['jam_masuk']));
                            $timestamp_jam_masuk_real = strtotime($jam_masuk);
                            $timestamp_jam_masuk_kantor = strtotime($jam_masuk_kantor);
                            $terlambat = $timestamp_jam_masuk_real - $timestamp_jam_masuk_kantor;

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
                                <td><?= $no++ ?></td>
                                <td><?= $rekap['nama'] ?></td>
                                <td><?= date('d F Y', strtotime($rekap['tanggal_masuk'])) ?></td>
                                <td class="text-center"><?= $rekap['jam_masuk'] ?></td>
                                <td class="text-center"><?= $rekap['jam_keluar'] ?></td>
                                <td class="text-center">
                                    <?php if ($rekap['tanggal_keluar'] == '0000-00-00') : ?>
                                        <span>0 Jam 0 Menit</span>
                                    <?php else : ?>
                                        <?= $total_jam_kerja . ' Jam ' . $selisih_menit_kerja . ' Menit' ?>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($is_ontime) : ?>
                                        <span class="badge bg-success">On Time</span>
                                    <?php else : ?>
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
                <h5 class="modal-title">Export Excel Rekap Presensi Harian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= base_url('admin/rekap_presensi/rekap_harian_excel.php') ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="">Tanggal Awal</label>
                        <input type="date" name="tanggal_dari" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="">Tanggal Akhir</label>
                        <input type="date" name="tanggal_sampai" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="">Filter Nama (Opsional)</label>
                        <input type="text" name="search_name" class="form-control" placeholder="Masukkan nama pegawai...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Export</button>
                </div>
            </form>
        </div>
    </div>
</div>

        <?php include('../layouts/footer.php') ?>