<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
require_once '../koneksi.php';

$data = json_decode(file_get_contents('php://input'), true);
$id_user = $data['id_user'] ?? '';

if (!$id_user) {
    echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
    exit;
}

$conn->begin_transaction();
try {
    $conn->query("DELETE FROM reservasi WHERE id_mahasiswa = '$id_user'");
    $conn->query("DELETE FROM mahasiswa WHERE id_mahasiswa = '$id_user'");
    $conn->query("DELETE FROM pengguna WHERE id_user = '$id_user'");
    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Mahasiswa berhasil dihapus']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Gagal: ' . $e->getMessage()]);
}
?>