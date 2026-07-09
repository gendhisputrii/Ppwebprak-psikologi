<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../koneksi.php';

$id_mahasiswa = $_GET['id_mahasiswa'] ?? '';
if (!$id_mahasiswa) {
    echo json_encode(['status' => 'error', 'message' => 'ID mahasiswa tidak valid']);
    exit;
}

$stmt = $conn->prepare("
    SELECT r.id_reservasi, r.tanggal_reservasi, r.waktu_reservasi, r.status_reservasi,
           p.nama_psikolog
    FROM reservasi r
    JOIN jadwal_praktik j ON r.id_jadwal = j.id_jadwal
    JOIN psikolog p ON j.id_psikolog = p.id_psikolog
    WHERE r.id_mahasiswa = ?
    ORDER BY r.tanggal_reservasi DESC, r.waktu_reservasi DESC
");
$stmt->bind_param('s', $id_mahasiswa);
$stmt->execute();
$result = $stmt->get_result();
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
echo json_encode(['status' => 'success', 'data' => $data]);
?>