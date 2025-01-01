<?php
ob_start();
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "pegawai") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}

$judul = 'Edit Pengajuan Ketidakhadiran';
include('../layouts/header.php');
include_once("../../config.php");

// Cek jika ada ID yang diterima dari URL
$id = $_GET['id'] ?? null;
if (!$id) {
    echo "ID tidak ditemukan.";
    exit;
}

// Query untuk mengambil data berdasarkan ID
$result = mysqli_query($connection, "SELECT * FROM ketidakhadiran WHERE id='$id'");

// Cek apakah query berhasil
if (!$result) {
    echo "Query gagal: " . mysqli_error($connection);
    exit;
}

// Ambil data dari hasil query
$data = mysqli_fetch_array($result);

// Cek apakah data ditemukan
if (!$data) {
    echo "Data tidak ditemukan.";
    exit;
}

// Ambil nilai-nilai dari data
$keterangan = $data['keterangan'];
$deskripsi = $data['deskripsi'];
$file = $data['file'];
$tanggal = $data['tanggal'];

if (isset($_POST['update'])) {

    $icon_validasi = "<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='icon icon-tabler icons-tabler-outline icon-tabler-check'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><path d='M5 12l5 5l10 -10' /></svg>";
    
    $id = $_POST['id'];
    $keterangan = $_POST['keterangan'];
    $tanggal = $_POST['tanggal'];
    $deskripsi = $_POST['deskripsi'];

    if($_FILES['file_baru']['error'] === 4) {
        $file_lama = $_POST['file_lama']; 
    } else {   
        $file = $_FILES['file_baru'];
        $nama_file = $file['name'];
        $file_tmp = $file['tmp_name'];
        $ukuran_file = $file['size'];
        $file_direktori = "../../assets/file_ketidakhadiran/" . $nama_file;

        $ambil_ekstensi = pathinfo($nama_file, PATHINFO_EXTENSION);
        $ekstensi_diizinkan = ["jpg", "png", "jpeg", "pdf"];
        $max_ukuran_file = 10 * 1024 * 1024;

        if (in_array(strtolower($ambil_ekstensi), $ekstensi_diizinkan) && $ukuran_file <= $max_ukuran_file) {
            move_uploaded_file($file_tmp, $file_direktori);
        } else {
            $pesan_kesalahan[] = "$icon_validasi Hanya file JPG, PNG, JPEG, atau PDF yang diperbolehkan dan ukuran file maksimal 10MB!";
        }
    }

    // Validasi input
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

        if (!empty($pesan_kesalahan)) {
            $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
        } else {
            // Update data ke database
            $file_update = ($_FILES['file_baru']['error'] === 4) ? $file_lama : $nama_file;
            $result = mysqli_query($connection, "UPDATE ketidakhadiran SET keterangan='$keterangan', tanggal='$tanggal', deskripsi='$deskripsi', file='$file_update' WHERE id='$id'");

            if ($result) {
                $_SESSION['berhasil'] = 'Data berhasil diupdate';
                header('Location: ketidakhadiran.php');
                exit;
            } else {
                $_SESSION['validasi'] = 'Update data gagal.';
            }
        }
    }
}
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
                            <option <?php if ($keterangan == 'Cuti') echo 'selected'; ?> value="Cuti">Cuti</option>
                            <option <?php if ($keterangan == 'Izin') echo 'selected'; ?> value="Izin">Izin</option>
                            <option <?php if ($keterangan == 'Sakit') echo 'selected'; ?> value="Sakit">Sakit</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" cols="30" rows="5"><?= $deskripsi ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="">Tanggal</label>
                        <input type="date" class="form-control" name="tanggal" value="<?= $tanggal ?>">
                    </div>

                    <div class="mb-3">
                        <label for="">Surat Keterangan</label>
                        <input type="file" name="file_baru" class="form-control">
                        <input type="hidden" value="<?= $file ?>" name="file_lama">
                        <?php if ($file): ?>
                            <small class="text-muted">File saat ini: <?= $file ?></small>
                        <?php endif; ?>
                    </div>

                    <input type="hidden" name="id" value="<?= $_GET['id']; ?>">

                    <button type="submit" class="btn btn-primary" name="update">Update</button>

                </form>
            </div>
        </div>
    </div>
</div>

<?php include('../layouts/footer.php') ?>
