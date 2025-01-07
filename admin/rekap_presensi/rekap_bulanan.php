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

$judul = "Rekap Presensi Bulanan";
include('../layouts/header.php');
include_once("../../config.php");

// Inisialisasi search_name
$search_name = isset($_GET['search_name']) ? mysqli_real_escape_string($connection, $_GET['search_name']) : '';

// Modifikasi query untuk mencakup pencarian nama
if (empty($_GET['filter_bulan'])) {
    $bulan_sekarang = date('Y-m');
    $query = "SELECT presensi.*, pegawai.nama, pegawai.lokasi_presensi 
             FROM presensi 
             JOIN pegawai ON presensi.id_pegawai = pegawai.id 
             WHERE DATE_FORMAT(tanggal_masuk, '%Y-%m') = '$bulan_sekarang'";

    if (!empty($search_name)) {
        $query .= " AND pegawai.nama LIKE '%$search_name%'";
    }

    $query .= " ORDER BY tanggal_masuk DESC";
    $result = mysqli_query($connection, $query);
} else {
    $filter_tahun_bulan = $_GET['filter_tahun'] . '-' . $_GET['filter_bulan'];
    $query = "SELECT presensi.*, pegawai.nama, pegawai.lokasi_presensi 
             FROM presensi 
             JOIN pegawai ON presensi.id_pegawai = pegawai.id 
             WHERE DATE_FORMAT(tanggal_masuk, '%Y-%m') = '$filter_tahun_bulan'";

    if (!empty($search_name)) {
        $query .= " AND pegawai.nama LIKE '%$search_name%'";
    }

    $query .= " ORDER BY tanggal_masuk DESC";
    $result = mysqli_query($connection, $query);
}


if (empty($_GET['filter_bulan'])) {
    $bulan = date('Y-m');
} else {
    $bulan = $_GET['filter_tahun'] . '-' . $_GET['filter_bulan'];
}

?><div class="page-body">
    <div class="container-xl">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <!-- Tombol Export Excel -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                Export Excel
            </button>
            <!-- Form Pencarian Nama -->
            <form method="GET" class="d-flex align-items-center">
    <!-- Filter Bulan -->
    <select name="filter_bulan" class="form-select me-2" style="width: auto;">
        <option value="">--Pilih Bulan--</option>
        <?php
        $bulan_array = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];
        foreach ($bulan_array as $value => $label): ?>
            <option value="<?= $value ?>" <?= (isset($_GET['filter_bulan']) && $_GET['filter_bulan'] == $value) ? 'selected' : '' ?>><?= $label ?></option>
        <?php endforeach; ?>
    </select>

    <!-- Filter Tahun -->
    <select name="filter_tahun" class="form-select me-2" style="width: auto;">
        <option value="">--Pilih Tahun--</option>
        <?php
        $tahun_array = ['2023', '2024', '2025'];
        foreach ($tahun_array as $tahun): ?>
            <option value="<?= $tahun ?>" <?= (isset($_GET['filter_tahun']) && $_GET['filter_tahun'] == $tahun) ? 'selected' : '' ?>><?= $tahun ?></option>
        <?php endforeach; ?>
    </select>

    <!-- Input Cari Nama -->
    <input type="text" class="form-control me-2" 
           name="search_name" 
           placeholder="Cari nama pegawai...(Opsional)" 
           value="<?= htmlspecialchars($search_name) ?>" 
           style="flex: 1;">

    <!-- Tombol Cari -->
    <button type="submit" class="btn btn-secondary">Cari</button>
</form>

        </div>
        <!-- Rekap Presensi -->
        <span>Rekap Presensi Bulan: <?= date('F Y', strtotime($bulan)) ?></span>
        <table class="table table-bordered mt-2">
            <!-- Tabel Header -->
            <tr class="text-center">
                <th>No.</th>
                <th>Nama</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Jam Pulang</th>
                <th>Total Jam</th>
                <th>Total Terlambat</th>
            </tr>
            <!-- Konten Tabel -->
            <?php if (mysqli_num_rows($result) === 0) { ?>
                <tr>
                    <td colspan="6">Data rekap presensi masih kosong</td>
                </tr>
            <?php } else { ?>
                <?php $no = 1; ?>
                <?php while ($rekap = mysqli_fetch_array($result)) : ?>
                    <!-- Logika Penghitungan -->
                    <?php
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
                <h5 class="modal-title">Export Excel Rekap Presensi Bulanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= base_url('admin/rekap_presensi/rekap_bulanan_excel.php') ?>">
                <div class="modal-body">

                    <div class="mb-3">
                        <label for="">Bulan</label>
                        <select name="filter_bulan" class="form-control">
                            <option value="">--Pilih Bulan--</option>
                            <option value="01">Januari</option>
                            <option value="02">Februari</option>
                            <option value="03">Maret</option>
                            <option value="04">April</option>
                            <option value="05">Mei</option>
                            <option value="06">Juni</option>
                            <option value="07">Juli</option>
                            <option value="08">Agustus</option>
                            <option value="09">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>

                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="">Tahun</label>
                        <select name="filter_tahun" class="form-control">
                            <option value="">--Pilih Tahun--</option>
                            <option value="2023">2023</option>
                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                        </select>

                    </div>
                    <div class="mb-3">
                        <label for="">Filter Nama (Opsional)</label>
                        <input type="text" name="search_name" class="form-control" placeholder="Masukkan nama pegawai...">
                    </div>
                </div>


                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Export</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include('../layouts/footer.php') ?>