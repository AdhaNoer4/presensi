<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "admin") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}
$judul = "Data Jabatan";
include('../layouts/header.php');
require_once('../../config.php');

$result = mysqli_query($connection, 'SELECT * FROM jabatan ORDER BY id DESC');

?>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <a href="<?= base_url('admin/data_jabatan/tambah.php') ?>" class="btn btn-primary"><span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 5l0 14" />
                    <path d="M5 12l14 0" />
                </svg></span>Tambah Data</a>
        <div class="row row-deck row-cards mt-2">
            <table class="table table-bordered ">
                <tr class="text-center">
                    <th>NO.</th>
                    <th>Nama Jabatan</th>
                    <th>Aksi</th>
                </tr>
                <?php if (mysqli_num_rows($result) === 0): ?>
                    <tr>
                        <td colspan="3">Data masih kososng, silahkan tambahkan data baru</td>
                    </tr>
                <?php else : ?>
                    <?php
                    $no = 1;
                    while ($jabatan = mysqli_fetch_array($result)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $jabatan['jabatan'] ?></td>
                            <td class="text-center">
                                <a href="<?= base_url('admin/data_jabatan/edit.php?id=' . $jabatan['id']) ?>" class="badge rounded-pill text-bg-primary">Edit</a>
                                <a href="<?= base_url('admin/data_jabatan/hapus.php?id=' . $jabatan['id']) ?>" class="badge rounded-pill text-bg-danger tombol-hapus">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile ?>
                <?php endif ?>
            </table>


        </div>
    </div>
</div>

<?php include('../layouts/footer.php') ?>