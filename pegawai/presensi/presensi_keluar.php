<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js" integrity="sha512-dQIiHSl2hr3NWKKLycPndtpbh5iaHLo6MwrXm7F0FM5e+kL2U16oE9uIwPHUl6fQBeCthiEuV/rzP3MiAB8Vfw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
     <!-- Make sure you put this AFTER Leaflet's CSS -->
 <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>
     <style>
        #map{
            height: 300px;
        }
     </style>
<?php
ob_start();
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
    exit;
} else if ($_SESSION["role"] !== "pegawai") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}

include('../layouts/header.php');
include_once("../../config.php");

$lokasi_presensi = $_SESSION['lokasi_presensi'];
$result = mysqli_query($connection, "SELECT * FROM lokasi_presensi WHERE nama_lokasi = '$lokasi_presensi'");

if (isset($_POST['tombol-keluar'])) {
    // Ambil data dari form dan pastikan valid
    $id = $_POST['id'];
    $latitude_pegawai = (float) $_POST['latitude_pegawai'];
    $longitude_pegawai = (float) $_POST['longitude_pegawai'];
    $latitude_kantor = (float) $_POST['latitude_kantor'];
    $longitude_kantor = (float) $_POST['longitude_kantor'];
    $radius = (float) $_POST['radius'];
    $tanggal_keluar= $_POST['tanggal_keluar'];
    $jam_keluar = $_POST['jam_keluar'];

    // Debugging untuk memastikan nilai-nilai tersebut benar
    // echo "<pre>";
    // echo "Latitude Pegawai: $latitude_pegawai<br>";
    // echo "Longitude Pegawai: $longitude_pegawai<br>";
    // echo "Latitude Kantor: $latitude_kantor<br>";
    // echo "Longitude Kantor: $longitude_kantor<br>";
    // echo "</pre>";

    // Hitung perbedaan koordinat
    $perbedaan_koordinat = $longitude_pegawai - $longitude_kantor;

    // Hitung jarak menggunakan trigonometri
    $jarak = sin(deg2rad($latitude_pegawai)) * sin(deg2rad($latitude_kantor)) +
             cos(deg2rad($latitude_pegawai)) * cos(deg2rad($latitude_kantor)) * cos(deg2rad($perbedaan_koordinat));
    $jarak = acos($jarak); // Mengambil nilai arc cosine
    $jarak = rad2deg($jarak); // Mengubah dari radian ke derajat

    // Konversi jarak ke mil, km, dan meter
    $mil = $jarak * 60 * 1.1515;
    $jarak_km = $mil * 1.609344;
    $jarak_meter = $jarak_km * 1000;

    // Debugging hasil jarak
    // echo "Jarak dalam meter: $jarak_meter<br>";
}

// Validasi apakah pegawai berada di dalam radius kantor
if (isset($jarak_meter) && $jarak_meter > $radius) {
    $_SESSION['gagal'] = "Anda berada di luar area kantor.";
    header("Location: ../home/home.php");
    exit;
}
?>

<!-- HTML untuk kamera dan tombol -->
<div class="page-body">
    <div class="container-xl">
        <div class="row">

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
               <div id="map"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-body" style="margin: auto;">
                    <input type="hidden" id="id" value="<?= $id ?>">
                    <input type="hidden" id="tanggal_keluar" value="<?= $tanggal_keluar?>">
                    <input type="hidden" id="jam_keluar" value="<?= $jam_keluar?>">
                    <!-- Tampilan kamera -->
                    <div id="my_camera" ></div>
                    <div id="my_result"></div>
                    <div><span><?= date('d F Y',strtotime($tanggal_keluar)) . ' - ' . $jam_keluar ?></span></div>
                    <button class="btn btn-danger mt-3" id="ambil-foto">Keluar</button>
                </div>
            </div>
        </div>
      
        </div>
    </div>
</div>
      <!-- Script untuk Webcam.js -->
      <script language="JavaScript">
                Webcam.set({
    width: 320,
    height: 240,
    dest_width: 320,
    dest_height: 240,
    image_format: 'png',
    jpeg_quality: 90,
    force_flash: false
});
                Webcam.attach('#my_camera'); // Menghubungkan kamera ke elemen HTML

                
                document.getElementById('ambil-foto').addEventListener('click', function() {
    let id = document.getElementById('id').value;
    let tanggal_keluar = document.getElementById('tanggal_keluar').value;
    let jam_keluar = document.getElementById('jam_keluar').value; 
    
    Webcam.snap(function(data_uri) {
        document.getElementById('my_result').innerHTML = '<img src="' + data_uri + '"/>';
        
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (xhttp.readyState == 4) {
                if (xhttp.status == 200) {
                    try {
                        var response = JSON.parse(xhttp.responseText);
                        if (response.status === 'success') {
                            window.location.href = '../home/home.php';
                        } else {
                            alert('Error: ' + response.message);
                        }
                    } catch (e) {
                        alert('Error processing response');
                    }
                } else {
                    alert('Error sending photo');
                }
            }
        };
        
        xhttp.open("POST", "presensi_keluar_aksi.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send(
            'photo=' + encodeURIComponent(data_uri) +
            '&id=' + encodeURIComponent(id) +
            '&tanggal_keluar=' + encodeURIComponent(tanggal_keluar) +
            '&jam_keluar=' + encodeURIComponent(jam_keluar)
        );
    });
});

//map leaflet js

let latitude_ktr = <?= $latitude_kantor ?>;
let longitude_ktr = <?= $longitude_kantor ?>;

let latitude_peg = <?= $latitude_pegawai ?>;
let longitude_peg = <?= $longitude_pegawai ?>;

let map = L.map('map').setView([latitude_ktr, longitude_ktr], 13);
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);

var marker = L.marker([latitude_ktr, longitude_ktr]).addTo(map);

var circle = L.circle([latitude_peg, longitude_peg], {
    color: 'red',
    fillColor: '#f03',
    fillOpacity: 0.5,
    radius: 500
}).addTo(map).bindPopup('Lokasi anda saat ini').openPopup();
            </script>
            
    
<?php include('../layouts/footer.php'); ?>
