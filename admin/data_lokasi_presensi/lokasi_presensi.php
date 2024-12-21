<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "admin") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}
$judul = "Data Lokasi Presensi";
include('../layouts/header.php');
require_once('../../config.php');

$result = mysqli_query($connection, 'SELECT * FROM lokasi_presensi ORDER BY id DESC');

?>

<div class="page-body">
    <div class="container-xl">
        <a href="<?= base_url('admin/data_lokasi_presensi/tambah.php') ?>" class="btn btn-primary"><span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 5l0 14" />
                    <path d="M5 12l14 0" />
                </svg></span>Tambah Data</a>
        <table class="table table-bordered mt-3">
            <tr class="text-center">
                <th>No</th>
                <th>Nama Lokasi</th>
                <th>Tipe Lokasi</th>
                <th>Latitude / Longtitude</th>
                <th>Radius</th>
                <th>Aksi</th>
            </tr>
            <?php if (mysqli_num_rows($result) === 0) { ?>
                <tr>
                    <td colspan="6">Data kosong, Silahkan tambah data baru</td>
                </tr>
            <?php } else { ?>
                <?php $no = 1;
                while ($lokasi = mysqli_fetch_array($result)) : ?>
                    <tr class="text-center">
                        <td><?= $no++ ?></td>
                        <td><?= $lokasi['nama_lokasi']; ?></td>
                        <td><?= $lokasi['tipe_lokasi']; ?></td>
                        <td><?= $lokasi['latitude'] . '/' . $lokasi['longitude'] ?></td>
                        <td><?= $lokasi['radius'] ?></td>
                        <td>
                            <a href="<?= base_url('admin/data_lokasi_presensi/detail.php?id=' . $lokasi['id']) ?>" class="badge rounded-pill text-bg-primary">Detail</a>
                            <a href="<?= base_url('admin/data_lokasi_presensi/edit.php?id=' . $lokasi['id']) ?>" class="badge rounded-pill text-bg-success">Edit</a>
                            <a href="<?= base_url('admin/data_lokasi_presensi/hapus.php?id=' . $lokasi['id']) ?>" class="badge rounded-pill text-bg-danger tombol-hapus">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php } ?>
        </table>
    </div>
</div>



<?php include('../layouts/footer.php') ?>