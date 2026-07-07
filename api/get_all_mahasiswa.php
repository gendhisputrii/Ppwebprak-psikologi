<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../koneksi.php';

$limit = intval($_GET['limit'] ?? 999);

$stmt = $conn->prepare("
    SELECT u.id_user, u.nama_user, u.email_user, u.no_hp,
           (SELECT COUNT(*) FROM reservasi WHERE id_mahasiswa = u.id_user AND status_reservasi = 'selesai') as total_sesi
    FROM pengguna u
    WHERE u.role_user = 'mahasiswa'
    LIMIT ?
");
$stmt->bind_param('i', $limit);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $row['status'] = $row['total_sesi'] > 0 ? 'aktif' : 'baru';
    $data[] = $row;
}

echo json_encode(['status' => 'success', 'data' => $data]);
?>