<?php

session_start();
require_once("../../config.php");

$id = $_GET['id'];

$result = mysqli_query($connection, "DELETE FROM pegawai WHERE id='$id'");

$_SESSION['berhasil'] = 'Data berhasil dihapus';
header('Location: pegawai.php');
exit;
?>

<?php include('../layouts/footer.php'); ?>


