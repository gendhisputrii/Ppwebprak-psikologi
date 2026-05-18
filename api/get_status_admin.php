<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../koneksi.php';

$total_psikolog  = $conn->query("SELECT COUNT(*) FROM psikolog")->fetch_row()[0];
$total_mahasiswa = $conn->query("SELECT COUNT(*) FROM mahasiswa")->fetch_row()[0];
$total_jadwal    = $conn->query("SELECT COUNT(*) FROM jadwal_praktik")->fetch_row()[0];
$total_reservasi = $conn->query("SELECT COUNT(*) FROM reservasi")->fetch_row()[0];

echo json_encode([
    'status' => 'success',
    'data'   => [
        'total_psikolog'  => $total_psikolog,
        'total_mahasiswa' => $total_mahasiswa,
        'total_jadwal'    => $total_jadwal,
        'total_reservasi' => $total_reservasi
    ]
]);
?>