<?php
ob_start();
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "pegawai") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}

$judul = 'Pengajuan Ketidakhadiran';
include('../layouts/header.php');
include_once("../../config.php");


if (isset($_POST['submit'])) {
        $id = $_POST['id_pegawai'];
        $keterangan = $_POST['keterangan'];
        $tanggal = $_POST['tanggal'];
        $deskripsi = $_POST['deskripsi'];
        $status_pengajuan = 'PENDING';

        if (isset($_FILES['file'])) {
            $file = $_FILES['file'];
            $nama_file = $file['name'];
            $file_tmp = $file['tmp_name'];
            $ukuran_file = $file['size'];
            $file_direktori = "../../assets/file_ketidakhadiran/" . $nama_file;
    
            $ambil_ekstensi = pathinfo($nama_file, PATHINFO_EXTENSION);
            $ekstensi_diizinkan = ["jpg", "png", "jpeg", "pdf"];
            $max_ukuran_file = 10 * 1024 * 1024;
    
            move_uploaded_file($file_tmp, $file_direktori);
        }
        


    $icon_validasi = "<svg  xmlns='http://www.w3.org/2000/svg'  width='24'  height='24'  viewBox='0 0 24 24'  fill='none'  stroke='currentColor'  stroke-width='2'  stroke-linecap='round'  stroke-linejoin='round'  class='icon icon-tabler icons-tabler-outline icon-tabler-check'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><path d='M5 12l5 5l10 -10' /></svg>";
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($keterangan)) {
                $pesan_kesalahan[] = "$icon_validasi Keterangan wajib diisi!";
            }
            if (empty($tanggal)) {
                $pesan_kesalahan[] = "$icon_validasi Tanggal wajib diisi!";
            }
            if (empty($deskripsi)) {
                $pesan_kesalahan[] = "$icon_validasi Deskripsi wajib diisi!";
            }       
            if (!in_array(strtolower($ambil_ekstensi), $ekstensi_diizinkan)) {
                $pesan_kesalahan[] = "$icon_validasi Hanya file JPG, PNG, dan JPEG yang diperbolehkan!";
            }
            if ($ukuran_file > $max_ukuran_file) {
                $pesan_kesalahan[] = "$icon_validasi Ukuran file melebihi batas maksimal 10MB";
            }
    
            if (!empty($pesan_kesalahan)) {
                $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
            } else {
                $pegawai = mysqli_query($connection, "INSERT INTO ketidakhadiran(id_pegawai, keterangan, deskripsi, tanggal, status_pengajuan, file) VALUES ('$id','$keterangan','$deskripsi','$tanggal','$status_pengajuan','$nama_file')");
    
                $_SESSION['berhasil'] = 'Data berhasil disimpan';
                header('Location: ketidakhadiran.php');
                exit;
            }
        }
    

}
$id = $_SESSION['id'];
$result = mysqli_query($connection, "SELECT * FROM ketidakhadiran WHERE id_pegawai = '$id' ORDER BY id DESC");
?>

<div class="page-body">
    <div class="container-xl">
        <div class="card col-md-6">
            <div class="card-body">

                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" value="<?= $_SESSION['id'] ?>" name="id_pegawai">
                    <div class="mb-3">
                        <label for="">Keterangan</label>
                        <select name="keterangan" class="form-control">
                            <option value="">--Pilih Keterangan--</option>
                            <option <?php if (isset($_POST['keterangan']) && $_POST['keterangan'] == 'Cuti') {
                                        echo 'selected';
                                    } ?> value="Cuti">Cuti</option>
                            <option <?php if (isset($_POST['keterangan']) && $_POST['keterangan'] == 'Izin') {
                                        echo 'selected';
                                    } ?> value="Izin">Izin</option>
                            <option <?php if (isset($_POST['keterangan']) && $_POST['keterangan'] == 'Sakit') {
                                        echo 'selected';
                                    } ?> value="Sakit">Sakit</option>
                        </select>

                    </div>
                    <div class="mb-3">
                        <label for="">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" cols="30" rows="5"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="">Tanggal</label>
                        <input type="date" class="form-control" name="tanggal">
                    </div>
                    
                    <div class="mb-3">
                        <label for="">Surat Keterangan</label>
                        <input type="file" class="form-control" name="file">
                        
                    </div>

                                    <button type="submit" class="btn btn-primary" name="submit">Ajukan</button>

                </form>
            </div>


        </div>
    </div>
</div>

<?php include('../layouts/footer.php') ?>