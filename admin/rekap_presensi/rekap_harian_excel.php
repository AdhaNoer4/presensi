<?php
ob_start();
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
    exit;
} else if ($_SESSION["role"] !== "admin") {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}

$judul = "Rekap Presensi Harian";
include_once("../../config.php");
require('../../vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$tanggal_dari = $_POST['tanggal_dari'];
$tanggal_sampai = $_POST['tanggal_sampai'];
$search_name = isset($_POST['search_name']) ? mysqli_real_escape_string($connection, $_POST['search_name']) : '';

$query = "SELECT presensi.*, pegawai.nama, pegawai.lokasi_presensi, pegawai.nip 
          FROM presensi 
          JOIN pegawai ON presensi.id_pegawai = pegawai.id 
          WHERE tanggal_masuk BETWEEN '$tanggal_dari' AND '$tanggal_sampai'";

if (!empty($search_name)) {
    $query .= " AND pegawai.nama LIKE '%$search_name%'";
}

$query .= " ORDER BY tanggal_masuk DESC";
$result = mysqli_query($connection, $query);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
// Set up headers
$sheet->setCellValue('A1', 'REKAP PRESENSI HARIAN');
$sheet->setCellValue('A2', 'Tanggal Awal');
$sheet->setCellValue('A3', 'Tanggal Akhir');
if (!empty($search_name)) {
    $sheet->setCellValue('A4', 'Filter Nama');
    $sheet->setCellValue('C4', $search_name);
    $sheet->mergeCells('A4:B4');
}
$sheet->setCellValue('C2', $tanggal_dari);
$sheet->setCellValue('C3', $tanggal_sampai);

// Set table headers
$headerRow = empty($search_name) ? 5 : 6;
$sheet->setCellValue('A' . $headerRow, 'NO');
$sheet->setCellValue('B' . $headerRow, 'NAMA');
$sheet->setCellValue('C' . $headerRow, 'NIP');
$sheet->setCellValue('D' . $headerRow, 'TANGGAL MASUK');
$sheet->setCellValue('E' . $headerRow, 'JAM MASUK');
$sheet->setCellValue('F' . $headerRow, 'TANGGAL KELUAR');
$sheet->setCellValue('G' . $headerRow, 'JAM KELUAR');
$sheet->setCellValue('H' . $headerRow, 'TOTAL JAM KERJA');
$sheet->setCellValue('I' . $headerRow, 'TOTAL JAM TERLAMBAT');

$sheet->mergeCells('A1:F1');
$sheet->mergeCells('A2:B2');
$sheet->mergeCells('A3:B3');

$no = 1;
$row = $headerRow + 1;

while ($data = mysqli_fetch_array($result)) {

    //menghitung total jam kerja
    $jam_tanggal_masuk = date('Y-m-d H:i:s', strtotime($data['tanggal_masuk'] . ' ' . $data['jam_masuk']));
    $jam_tanggal_keluar = date('Y-m-d H:i:s', strtotime($data['tanggal_keluar'] . ' ' . $data['jam_keluar']));
    $timestamp_masuk = strtotime($jam_tanggal_masuk);
    $timestamp_keluar = strtotime($jam_tanggal_keluar);
    $selisih = $timestamp_keluar - $timestamp_masuk;
    $total_jam_kerja = floor($selisih / 3600);
    $selisih -= $total_jam_kerja * 3600;
    $selisih_menit_kerja = floor($selisih / 60);

    //menghitung total jam terlambat
    $lokasi_presensi = $data['lokasi_presensi'];
    $lokasi = mysqli_query($connection, "SELECT * FROM lokasi_presensi WHERE nama_lokasi = '$lokasi_presensi'");

    while ($lokasi_result = mysqli_fetch_array($lokasi)) :
        $jam_masuk_kantor = date('H:i:s', strtotime($lokasi_result['jam_masuk']));
    endwhile;
    $jam_masuk = date('H:i:s', strtotime($data['jam_masuk']));
    $timestamp_jam_masuk_real = strtotime($jam_masuk);
    $timestamp_jam_masuk_kantor = strtotime($jam_masuk_kantor);
    $terlambat = $timestamp_jam_masuk_real - $timestamp_jam_masuk_kantor;

    // Set flag untuk status keterlambatan
    $is_ontime = $terlambat <= 0;

    if ($terlambat > 0) {
        $total_jam_terlambat = floor($terlambat / 3600);
        $terlambat -= $total_jam_terlambat * 3600;
        $selisih_menit_terlambat = floor($terlambat / 60);
    } else {
        $total_jam_terlambat = 0;
        $selisih_menit_terlambat = 0;
    }

    $sheet->setCellValue('A' . $row, $no);
    $sheet->setCellValue('B' . $row, $data['nama']);
    $sheet->setCellValue('C' . $row, $data['nip']);
    $sheet->setCellValue('D' . $row, $data['tanggal_masuk']);
    $sheet->setCellValue('E' . $row, $data['jam_masuk']);
    $sheet->setCellValue('F' . $row, $data['tanggal_keluar']);
    $sheet->setCellValue('G' . $row, $data['jam_keluar']);
    $sheet->setCellValue('H' . $row, $total_jam_kerja . ' Jam ' . $selisih_menit_kerja . ' Menit');
    $sheet->setCellValue('I' . $row, $total_jam_terlambat . ' Jam ' . $selisih_menit_terlambat . ' Menit');

    $no++;
    $row++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Laporan Presensi Harian.xlsx"');
header('Cache-Control: max-age=0');

$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
