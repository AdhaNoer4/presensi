<?php
session_start();
ob_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "admin") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}
$judul = "Tambah Pegawai";
include('../layouts/header.php');
require_once('../../config.php');

if (isset($_POST['submit'])) {
    $ambil_nip = mysqli_query($connection, "SELECT nip FROM pegawai ORDER BY nip DESC LIMIT 1");

    if (mysqli_num_rows($ambil_nip) > 0) {
        $row = mysqli_fetch_assoc($ambil_nip);
        $nip_db = $row["nip"];
        $nip_db = explode("-", $nip_db);
        $no_baru = (int)$nip_db[1] + 1;
        $nip_baru = "PEG-" . str_pad($no_baru, 3, 0, STR_PAD_LEFT);
    } else {
        $nip_baru = "PEG-001";
    }

    $nip = $nip_baru;
    $nama = htmlspecialchars($_POST['nama']);
    $jenis_kelamin = htmlspecialchars($_POST['jenis_kelamin']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $no_handphone = htmlspecialchars($_POST['no_handphone']);
    $jabatan = htmlspecialchars($_POST['jabatan']);
    $username = htmlspecialchars($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = htmlspecialchars($_POST['role']);
    $status = htmlspecialchars($_POST['status']);
    $lokasi_presensi = htmlspecialchars($_POST['lokasi_presensi']);
    $email = htmlspecialchars($_POST['email']);

    if (isset($_FILES['foto'])) {
        $file = $_FILES['foto'];
        $nama_file = $file['name'];
        $file_tmp = $file['tmp_name'];
        $ukuran_file = $file['size'];
        $file_direktori = "../../assets/img/foto_pegawai/" . $nama_file;

        $ambil_ekstensi = pathinfo($nama_file, PATHINFO_EXTENSION);
        $ekstensi_diizinkan = ["jpg", "png", "jpeg"];
        $max_ukuran_file = 10 * 1024 * 1024;

        move_uploaded_file($file_tmp, $file_direktori);
    }


    $icon_validasi = "<svg  xmlns='http://www.w3.org/2000/svg'  width='24'  height='24'  viewBox='0 0 24 24'  fill='none'  stroke='currentColor'  stroke-width='2'  stroke-linecap='round'  stroke-linejoin='round'  class='icon icon-tabler icons-tabler-outline icon-tabler-check'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><path d='M5 12l5 5l10 -10' /></svg>";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (empty($nama)) {
            $pesan_kesalahan[] = "$icon_validasi Nama  wajib diisi!";
        }
        if (empty($jenis_kelamin)) {
            $pesan_kesalahan[] = "$icon_validasi Jenis kelamin wajib diisi!";
        }
        if (empty($alamat)) {
            $pesan_kesalahan[] = "$icon_validasi Alamat wajib diisi!";
        }
        if (empty($no_handphone)) {
            $pesan_kesalahan[] = "$icon_validasi No. Handphone wajib diisi!";
        }
        if (empty($jabatan)) {
            $pesan_kesalahan[] = "$icon_validasi Jabatan wajib diisi!";
        }
        if (empty($username)) {
            $pesan_kesalahan[] = "$icon_validasi Username wajib diisi!";
        }
        if (empty($password)) {
            $pesan_kesalahan[] = "$icon_validasi Password wajib diisi!";
        }
        if (empty($role)) {
            $pesan_kesalahan[] = "$icon_validasi Role wajib diisi!";
        }
        if (empty($status)) {
            $pesan_kesalahan[] = "$icon_validasi Status wajib diisi!";
        }
        if (empty($lokasi_presensi)) {
            $pesan_kesalahan[] = "$icon_validasi Lokasi Presensi wajib diisi!";
        }
        if (empty($email)) {
            $pesan_kesalahan[] = "$icon_validasi E-mail wajib diisi!";
        }
        if ($_POST['password'] !== $_POST['ulangi_password']) {
            $pesan_kesalahan[] = "$icon_validasi Password tidak cocok!";
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
            $pegawai = mysqli_query($connection, "INSERT INTO pegawai(nip, nama, jenis_kelamin, alamat, no_handphone, jabatan, lokasi_presensi, foto) VALUES ('$nip','$nama','$jenis_kelamin','$alamat','$no_handphone','$jabatan','$lokasi_presensi','$nama_file')");


            $id_pegawai = mysqli_insert_id($connection);
            $users = mysqli_query($connection, "INSERT INTO users(id_pegawai, username, password, status, role, email) VALUES ('$id_pegawai','$username','$password','$status','$role','$email')");

            $_SESSION['berhasil'] = 'Data berhasil disimpan';
            header('Location: pegawai.php');
            exit;
        }
    }
}
?>

<div class="page-body">
    <div class="container-xl">
        <form action="<?= base_url('admin/data_pegawai/tambah.php') ?>" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="nama">Nama</label>
                                <input type="text" name="nama" class="form-control" value="<?php if (isset($_POST['nama'])) echo $_POST['nama'] ?>">
                            </div>
                            <div class="mb-3">
                                <label for="jenis_kelamin">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-control">
                                    <option value="">--Pilih Jenis Kelamin--</option>
                                    <option <?php if (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] == 'Laki-laki') {
                                                echo 'selected';
                                            } ?> value="Laki-laki">Laki-laki</option>
                                    <option <?php if (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] == 'Perempuan') {
                                                echo 'selected';
                                            } ?> value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="alamat">Alamat</label>
                                <input type="text" name="alamat" class="form-control" value="<?php if (isset($_POST['alamat'])) echo $_POST['alamat'] ?>">
                            </div>
                            <div class="mb-3">
                                <label for="no_handphone">No. Handphone</label>
                                <input type="text" name="no_handphone" class="form-control" value="<?php if (isset($_POST['no_handphone'])) echo $_POST['no_handphone'] ?>">
                            </div>
                            <div class="mb-3">
                                <label for="jabatan">Jabatan</label>
                                <select name="jabatan" class="form-control">
                                    <option value="">--Pilih Jabatan--</option>
                                    <?php
                                    $ambil_jabatan = mysqli_query($connection, "SELECT * FROM jabatan ORDER BY jabatan ASC");

                                    while ($jabatan = mysqli_fetch_assoc($ambil_jabatan)) {
                                        $nama_jabatan = $jabatan["jabatan"];

                                        if (isset($_POST['jabatan']) && $_POST['jabatan'] == $nama_jabatan) {
                                            echo "<option value='" . $nama_jabatan . "' selected='selected'>" . $nama_jabatan . "</option>";
                                        } else {
                                            echo "<option value='" . $nama_jabatan . "'>" . $nama_jabatan . "</option>";
                                        }
                                    }

                                    ?>

                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="status">Status</label>
                                <select name="status" class="form-control">
                                    <option value="">--Pilih Status--</option>
                                    <option <?php if (isset($_POST['status']) && $_POST['status'] == 'Aktif') {
                                                echo 'selected';
                                            } ?> value="Aktif">Aktif</option>
                                    <option <?php if (isset($_POST['status']) && $_POST['status'] == 'Tidak Aktif') {
                                                echo 'selected';
                                            } ?> value="Tidak Aktif">Tidak Aktif</option>
                                    <option <?php if (isset($_POST['status']) && $_POST['status'] == 'Dinas') {
                                                echo 'selected';
                                            } ?> value="Dinas">Dinas</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="email">E-mail</label>
                                <input type="email" name="email" class="form-control" value="<?php if (isset($_POST['email'])) echo $_POST['email'] ?>">
                            </div>
                            <div class="mb-3">
                                <label for="username">Username</label>
                                <input type="text" name="username" class="form-control" value="<?php if (isset($_POST['username'])) echo $_POST['username'] ?>">
                            </div>
                            <div class="mb-3">
                                <label for="password">Password</label>
                                <input type="password" name="password" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="ulangi_password">Ulangi Password</label>
                                <input type="password" name="ulangi_password" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="role">Role</label>
                                <select name="role" class="form-control">
                                    <option value="">--Pilih role--</option>
                                    <option <?php if (isset($_POST['role']) && $_POST['role'] == 'admin') {
                                                echo 'selected';
                                            } ?> value="admin">Admin</option>
                                    <option <?php if (isset($_POST['role']) && $_POST['role'] == 'pegawai') {
                                                echo 'selected';
                                            } ?> value="pegawai">Pegawai</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="lokasi_presensi">Lokasi Presensi</label>
                                <select name="lokasi_presensi" class="form-control">
                                    <option value="">--Pilih Lokasi Presensi--</option>
                                    <?php
                                    $ambil_lok_presensi = mysqli_query($connection, "SELECT * FROM lokasi_presensi ORDER BY nama_lokasi ASC");

                                    while ($lokasi = mysqli_fetch_assoc($ambil_lok_presensi)) {
                                        $nama_lokasi = $lokasi["nama_lokasi"];

                                        if (isset($_POST['lokasi_presensi']) && $_POST['lokasi_presensi'] == $nama_lokasi) {
                                            echo "<option value='" . $nama_lokasi . "' selected='selected'>" . $nama_lokasi . "</option>";
                                        } else {
                                            echo "<option value='" . $nama_lokasi . "'>" . $nama_lokasi . "</option>";
                                        }
                                    }

                                    ?>

                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="foto">Foto</label>
                                <input type="file" name="foto" class="form-control">
                                <label for="foto" class="text-danger">* jpg, jpeg, png. Max 10MB</label>
                            </div>
                            <div class="mb-3 text-end">
                                <button type="submit" class="btn btn-primary" name="submit">Simpan</button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>


<?php include('../layouts/footer.php') ?>