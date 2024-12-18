<?php
session_start();
ob_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "admin") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}
$judul = "Tambah Data Jabatan";
include('../layouts/header.php');
require_once('../../config.php');

if (isset($_POST['submit'])) {
    $jabatan = htmlspecialchars($_POST['jabatan']);

    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        if (empty($jabatan)) {
            $pesan_kesalahan = "Nama jabatan wajib diisi!";
        }

        if (!empty($pesan_kesalahan)) {
            $_SESSION["validasi"] = $pesan_kesalahan;
        } else {
            $result = mysqli_query($connection, "INSERT INTO jabatan(jabatan) VALUES ('$jabatan')");
            $_SESSION['berhasil'] = "Data berhasil disimpan";
            header("Location: jabatan.php");
            exit;
        }
    }
}

?>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="card col-md-6">
            <div class="card-body">
                <form action="<?= base_url('admin/data_jabatan/tambah.php') ?>" method="post">
                    <div class="mb-3">
                        <label for="jabatan">Nama Jabatan</label>
                        <input type="text" class="form-control" name="jabatan" id="jabatan">
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>

    </div>
</div>

<?php include('../layouts/footer.php') ?>