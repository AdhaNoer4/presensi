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
$id_pegawai = $_POST['id'];
$tanggal_masuk = $_POST['tanggal_masuk'];
$jam_masuk = $_POST['jam_masuk'];

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
$file = 'masuk_' . date('Y-m-d_His') . '.png';
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
$result = mysqli_query($connection, "INSERT INTO presensi(id_pegawai, tanggal_masuk, jam_masuk, foto_masuk) 
    VALUES ('$id_pegawai','$tanggal_masuk','$jam_masuk', '$file')");

if ($result) {
    $_SESSION["berhasil"] = "Presensi masuk berhasil";
} else {
    $_SESSION["gagal"] = "Presensi masuk gagal: " . mysqli_error($connection);
}

// Return response
header('Content-Type: application/json');
echo json_encode(['status' => $result ? 'success' : 'error', 
                 'message' => $result ? $_SESSION["berhasil"] : $_SESSION["gagal"]]);