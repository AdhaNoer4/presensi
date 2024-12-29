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
include_once("../../config.php");

// Correct POST variable
$file_foto = $_POST['photo'];
$id_presensi = $_POST['id'];
$tanggal_keluar = $_POST['tanggal_keluar'];
$jam_keluar = $_POST['jam_keluar'];

// Handle PNG data URI
$foto = $file_foto;
$foto = str_replace('data:image/png;base64,', '', $foto);
$foto = str_replace(' ', '+', $foto);

// Decode base64 data
$data = base64_decode($foto);

// Check if decode was successful
if ($data === false) {
    $_SESSION["gagal"] = "Failed to decode image data";
    exit;
}

// Create directory if it doesn't exist
$upload_dir = 'foto/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Generate unique filename
$file = 'keluar_' . date('Y-m-d_His') . '.png';
$nama_file = $upload_dir . $file;

// Write file and check if successful
if (file_put_contents($nama_file, $data) === false) {
    $_SESSION["gagal"] = "Failed to save image file";
    exit;
}

// Verify file was created and has content
if (!file_exists($nama_file) || filesize($nama_file) === 0) {
    $_SESSION["gagal"] = "File was not created or is empty";
    exit;
}

// Insert into database
$result = mysqli_query($connection, "UPDATE presensi SET tanggal_keluar='$tanggal_keluar', jam_keluar='$jam_keluar', foto_keluar='$file' WHERE id=$id_presensi");

if ($result) {
    $_SESSION["berhasil"] = "Presensi keluar berhasil";
} else {
    $_SESSION["gagal"] = "Presensi keluar gagal: " . mysqli_error($connection);
}

// Return response
header('Content-Type: application/json');
echo json_encode(['status' => $result ? 'success' : 'error', 
                 'message' => $result ? $_SESSION["berhasil"] : $_SESSION["gagal"]]);