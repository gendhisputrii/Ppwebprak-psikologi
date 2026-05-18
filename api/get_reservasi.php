<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../koneksi.php';

$id_mahasiswa = $_GET['id_mahasiswa'] ?? '';
$id_psikolog  = $_GET['id_psikolog'] ?? '';

if ($id_mahasiswa) {
    // Untuk dashboard mahasiswa — ambil reservasi milik mahasiswa ini
    $stmt = $conn->prepare("
        SELECT r.id_reservasi, r.tanggal_reservasi, r.waktu_reservasi, r.status_reservasi,
               j.hari, j.jam_mulai, j.jam_selesai,
               p.nama_psikolog, p.spesialisasi, p.tarif
        FROM reservasi r
        JOIN jadwal_praktik j ON r.id_jadwal = j.id_jadwal
        JOIN psikolog p ON j.id_psikolog = p.id_psikolog
        WHERE r.id_mahasiswa = ?
        ORDER BY r.tanggal_reservasi DESC
    ");
    $stmt->bind_param('s', $id_mahasiswa);

} elseif ($id_psikolog) {
    // Untuk dashboard psikolog — ambil reservasi untuk psikolog ini
    $stmt = $conn->prepare("
        SELECT r.id_reservasi, r.tanggal_reservasi, r.waktu_reservasi, r.status_reservasi,
               j.hari, j.jam_mulai, j.jam_selesai,
               u.nama_user AS nama_mahasiswa, u.email_user
        FROM reservasi r
        JOIN jadwal_praktik j ON r.id_jadwal = j.id_jadwal
        JOIN pengguna u ON r.id_mahasiswa = u.id_user
        WHERE j.id_psikolog = ?
        ORDER BY r.tanggal_reservasi DESC
    ");
    $stmt->bind_param('s', $id_psikolog);

} else {
    echo json_encode(['status' => 'error', 'message' => 'Parameter tidak valid']);
    exit;
}

$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode(['status' => 'success', 'data' => $data]);
?>