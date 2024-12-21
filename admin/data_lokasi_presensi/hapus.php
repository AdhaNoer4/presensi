<?php

session_start();
require_once("../../config.php");

$id = $_GET['id'];

$result = mysqli_query($connection, "DELETE FROM lokasi_presensi WHERE id='$id'");

$_SESSION['berhasil'] = 'Data berhasil dihapus';
header('Location: lokasi_presensi.php');
exit;
?>

<?php include('../layouts/footer.php'); ?>


