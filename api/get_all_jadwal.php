<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../koneksi.php';

$result = $conn->query("
    SELECT j.id_jadwal, j.id_psikolog, j.hari, j.jam_mulai, j.jam_selesai, 
           COALESCE(j.status, 'aktif') as status,
           u.nama_user as nama_psikolog,
           (SELECT COUNT(*) FROM reservasi r WHERE r.id_jadwal = j.id_jadwal AND r.status_reservasi != 'dibatalkan') as terisi
    FROM jadwal_praktik j
    JOIN psikolog p ON j.id_psikolog = p.id_psikolog
    JOIN pengguna u ON p.id_user = u.id_user
    ORDER BY FIELD(j.hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'), j.jam_mulai
");

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode(['status' => 'success', 'data' => $data]);
?>