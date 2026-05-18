<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../koneksi.php';

$id_psikolog = $_GET['id_psikolog'] ?? '';

if (!$id_psikolog) {
    // Kalau tidak ada filter, ambil semua jadwal
    $result = $conn->query("
        SELECT j.id_jadwal, j.hari, j.jam_mulai, j.jam_selesai,
               p.nama_psikolog, p.spesialisasi, p.tarif
        FROM jadwal_praktik j
        JOIN psikolog p ON j.id_psikolog = p.id_psikolog
        ORDER BY FIELD(j.hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')
    ");
} else {
    $stmt = $conn->prepare("
        SELECT j.id_jadwal, j.hari, j.jam_mulai, j.jam_selesai,
               p.nama_psikolog, p.spesialisasi, p.tarif
        FROM jadwal_praktik j
        JOIN psikolog p ON j.id_psikolog = p.id_psikolog
        WHERE j.id_psikolog = ?
        ORDER BY FIELD(j.hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')
    ");
    $stmt->bind_param('s', $id_psikolog);
    $stmt->execute();
    $result = $stmt->get_result();
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode(['status' => 'success', 'data' => $data]);
?>