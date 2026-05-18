<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once '../koneksi.php';

$data         = json_decode(file_get_contents('php://input'), true);
$id_reservasi = $data['id_reservasi'] ?? '';
$status       = $data['status'] ?? '';

$allowed = ['menunggu', 'terkonfirmasi', 'selesai', 'dibatalkan'];
if (!$id_reservasi || !in_array(strtolower($status), $allowed)) {
    echo json_encode(['status' => 'error', 'message' => 'Parameter tidak valid']);
    exit;
}

$stmt = $conn->prepare("UPDATE reservasi SET status_reservasi = ? WHERE id_reservasi = ?");
$stmt->bind_param('ss', $status, $id_reservasi);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Status berhasil diupdate']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal update status']);
}
?>