<?php
ob_start();
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] !== "pegawai") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}
// Koneksi ke database
include '../../config.php'; // Ubah sesuai lokasi file koneksi Anda
$judul = 'Presensi Perjalanan Bisnis';
include('../layouts/header.php');
date_default_timezone_set('Asia/Jakarta');
// Cek apakah tombol absen diklik
if (isset($_POST['absen'])) {
    // Ambil data dari form dan session
    $id_pegawai = $_SESSION['id']; // Pastikan session menyimpan id_pegawai
    $lokasi_absensi = isset($_POST['lokasi_absensi']) ? $_POST['lokasi_absensi'] : '';
    $tanggal_masuk = date('Y-m-d H:i:s'); // Ambil waktu saat ini
    $jam_masuk = date('H:i:s');
    $jenis_absensi = 'Perjalanan Bisnis'; // Jenis absensi

    // Query untuk memasukkan data ke tabel presensi
    $query = "INSERT INTO presensi (id_pegawai, tanggal_masuk, jam_masuk, lokasi_absensi, jenis_absensi) 
              VALUES ('$id_pegawai', '$tanggal_masuk','$jam_masuk', '$lokasi_absensi', '$jenis_absensi')";

    if ($connection->query($query) === TRUE) {
        $_SESSION['berhasil'] = "Absensi perjalanan bisnis berhasil!";
        header("Location: ../home/home.php");
        exit;
    } else {
        echo "Error: " . $query . "<br>" . $connection->error;
    }
}
?>

<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Absensi Perjalanan Bisnis</div>
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <form method="POST" action="absen_perjalanan.php">
                            <!-- Input manual lokasi -->
                            <div class="mb-3">
                                <label for="lokasi_absensi">Lokasi Absensi:</label>
                                <input type="text" class="form-control" name="lokasi_absensi" id="lokasi_absensi" placeholder="Masukkan lokasi perjalanan bisnis" required>
                            </div>

                            <!-- Tombol absen -->
                            <button type="submit" name="absen" class="btn btn-success">Absen</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
    document.querySelector('form').addEventListener('submit', function(event) {
        const lokasi = document.getElementById('lokasi_absensi').value.trim();
        if (!lokasi) {
            alert('Harap isi lokasi perjalanan bisnis!');
            event.preventDefault(); // Hentikan pengiriman form jika kosong
        }
    });
</script>

<?php include('../layouts/footer.php') ?>