<?php
ob_start();
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "admin") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}
$judul = "Detail Ketidakhadiran";
include('../layouts/header.php');
require_once('../../config.php');

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $status_pengajuan = $_POST['status_pengajuan'];

    $result = mysqli_query($connection, "UPDATE ketidakhadiran SET status_pengajuan='$status_pengajuan' WHERE id='$id'");
    $_SESSION['berhasil'] = "Status pengajuan berhasil diupdate";
    header("Location: ketidakhadiran.php");
    exit;
}

$id = $_GET['id'];
$result = mysqli_query($connection, "SELECT * FROM ketidakhadiran WHERE id = $id ");

while ($data = mysqli_fetch_array($result)) {
    $keterangan = $data['keterangan'];
    $status_pengajuan = $data['status_pengajuan'];
    $tanggal = $data['tanggal'];
}
?>

<div class="page-body">
    <div class="container-xl">
        <div class="card col-md-6">
            <div class="card-body">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="">Tanggal</label>
                        <input type="date" class="form-control" name="tanggal" value="<?= $tanggal ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="">Keterangan</label>
                        <input type="text" class="form-control" name="keterangan" value="<?= $keterangan ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="">Status Pengajuan</label>
                        <select name="status_pengajuan" class="form-control">
                            <option value="">--Pilih Status Pengajuan--</option>
                            <option <?php if ($status_pengajuan == 'PENDING') echo 'selected'; ?> value="PENDING">PENDING</option>
                            <option <?php if ($status_pengajuan == 'REJECTED') echo 'selected'; ?> value="REJECTED">REJECTED</option>
                            <option <?php if ($status_pengajuan == 'APPROVED') echo 'selected'; ?> value="APPROVED">APPROVED</option>
                        </select>
                    </div>
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <button type="submit" class="btn btn-primary" name="update">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('../layouts/footer.php') ?>
