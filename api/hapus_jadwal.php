<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../koneksi.php';

$input = json_decode(file_get_contents('php://input'), true);
$id_jadwal = $input['id_jadwal'] ?? '';

if (!$id_jadwal) {
    echo json_encode(['status' => 'error', 'message' => 'ID jadwal tidak valid']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM jadwal_praktik WHERE id_jadwal = ?");
$stmt->bind_param('s', $id_jadwal);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus jadwal: ' . $stmt->error]);
}
?>