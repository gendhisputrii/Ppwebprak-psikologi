<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../koneksi.php';

$result = $conn->query("
    SELECT r.id_reservasi, r.tanggal_reservasi, r.waktu_reservasi, r.status_reservasi, r.keluhan,
           um.nama_user as nama_mahasiswa,
           up.nama_user as nama_psikolog
    FROM reservasi r
    JOIN pengguna um ON r.id_mahasiswa = um.id_user
    JOIN jadwal_praktik j ON r.id_jadwal = j.id_jadwal
    JOIN psikolog p ON j.id_psikolog = p.id_psikolog
    JOIN pengguna up ON p.id_user = up.id_user
    ORDER BY r.tanggal_reservasi DESC, r.waktu_reservasi DESC
");

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode(['status' => 'success', 'data' => $data]);
?>