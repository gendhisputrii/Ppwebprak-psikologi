<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
require_once '../koneksi.php';

$data = json_decode(file_get_contents('php://input'), true);
$id_jadwal = $data['id_jadwal'] ?? '';

if (!$id_jadwal) {
    echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
    exit;
}

$conn->begin_transaction();
try {
    $conn->query("DELETE FROM reservasi WHERE id_jadwal = '$id_jadwal'");
    $conn->query("DELETE FROM jadwal_praktik WHERE id_jadwal = '$id_jadwal'");
    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Jadwal berhasil dihapus']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Gagal: ' . $e->getMessage()]);
}
?>