<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../koneksi.php';

$limit = intval($_GET['limit'] ?? 999);

$stmt = $conn->prepare("
    SELECT p.id_psikolog, u.id_user, u.nama_user, u.email_user, u.no_hp, p.spesialisasi, p.tarif,
           COALESCE(p.status, 'aktif') as status,
           (SELECT COUNT(DISTINCT r.id_mahasiswa) FROM reservasi r 
            WHERE r.id_jadwal IN (SELECT j.id_jadwal FROM jadwal_praktik j WHERE j.id_psikolog = p.id_psikolog)
            AND r.status_reservasi != 'dibatalkan') as total_pasien,
           (SELECT COUNT(*) FROM reservasi r 
            WHERE r.id_jadwal IN (SELECT j.id_jadwal FROM jadwal_praktik j WHERE j.id_psikolog = p.id_psikolog)
            AND r.status_reservasi = 'selesai') as total_sesi
    FROM psikolog p
    JOIN pengguna u ON p.id_user = u.id_user
    LIMIT ?
");
$stmt->bind_param('i', $limit);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode(['status' => 'success', 'data' => $data]);
?>