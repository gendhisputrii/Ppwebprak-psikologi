<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../koneksi.php';

$id_user = $_GET['id_user'] ?? '';

if (!$id_user) {
    echo json_encode(['status' => 'error', 'message' => 'ID user tidak valid']);
    exit;
}

$stmt = $conn->prepare("
    SELECT p.*, u.nama_user, u.email_user, u.no_hp
    FROM psikolog p
    JOIN pengguna u ON p.id_psikolog = u.id_user
    WHERE p.id_psikolog = ?
");
$stmt->bind_param('s', $id_user);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if ($data) {
    echo json_encode(['status' => 'success', 'data' => $data]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Profil tidak ditemukan']);
}
?>