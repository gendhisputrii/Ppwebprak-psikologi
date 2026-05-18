<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../koneksi.php';

$result = $conn->query("
    SELECT r.id_reservasi, r.tanggal_reservasi, r.waktu_reservasi, r.status_reservasi,
           u_mhs.nama_user AS nama_mahasiswa,
           p.nama_psikolog,
           j.hari, j.jam_mulai, j.jam_selesai
    FROM reservasi r
    JOIN pengguna u_mhs ON r.id_mahasiswa = u_mhs.id_user
    JOIN jadwal_praktik j ON r.id_jadwal = j.id_jadwal
    JOIN psikolog p ON j.id_psikolog = p.id_psikolog
    ORDER BY r.tanggal_reservasi DESC
    LIMIT 50
");

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode(['status' => 'success', 'data' => $data]);
?>