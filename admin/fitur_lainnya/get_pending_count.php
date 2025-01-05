<?php
require_once('../../config.php');

// Query untuk jumlah pending
$query_pending = "SELECT COUNT(*) AS jumlah_pending FROM ketidakhadiran WHERE status_pengajuan = 'pending'";
$result_pending = mysqli_query($connection, $query_pending);
$data_pending = $result_pending->fetch_assoc();

echo json_encode(['jumlah_pending' => $data_pending['jumlah_pending']]);
