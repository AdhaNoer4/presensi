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
$judul = 'Presensi Keluar Perjalanan Bisnis';
include('../layouts/header.php');
date_default_timezone_set('Asia/Jakarta');
// Cek apakah tombol absen diklik
if (isset($_POST['absen'])) {
    // Ambil data dari form dan session
    $id_pegawai = $_SESSION['id']; // Pastikan session menyimpan id_pegawai
    $tanggal_keluar = date('Y-m-d H:i:s'); // Ambil waktu saat ini
    $jam_keluar = date('H:i:s');


    // Query untuk memasukkan data ke tabel presensi
    $query = "UPDATE presensi 
              SET tanggal_keluar = '$tanggal_keluar',
              jam_keluar = '$jam_keluar' 
              WHERE id_pegawai = '$id_pegawai' 
              AND jenis_absensi = 'Perjalanan Bisnis' 
              ORDER BY tanggal_masuk DESC LIMIT 1";

    if ($connection->query($query) === TRUE) {
        $_SESSION['berhasil'] = "Absensi keluar perjalanan bisnis berhasil!";
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
                    <div class="card-header">Absensi Keluar Perjalanan Bisnis</div>
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <form method="POST" action="absen_perjalanan_keluar.php">
                            <!-- Tombol absen -->
                            <button type="submit" name="absen" class="btn btn-success">Absen Keluar</button>
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