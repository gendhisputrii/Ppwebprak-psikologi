<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
require_once '../koneksi.php';

$data = json_decode(file_get_contents('php://input'), true);
$id_psikolog = $data['id_psikolog'] ?? '';

if (!$id_psikolog) {
    echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
    exit;
}

$stmt = $conn->prepare("SELECT id_user FROM psikolog WHERE id_psikolog = ?");
$stmt->bind_param('s', $id_psikolog);
$stmt->execute();
$id_user = $stmt->get_result()->fetch_assoc()['id_user'];

$conn->begin_transaction();
try {
    $conn->query("DELETE FROM reservasi WHERE id_jadwal IN (SELECT id_jadwal FROM jadwal_praktik WHERE id_psikolog = '$id_psikolog')");
    $conn->query("DELETE FROM jadwal_praktik WHERE id_psikolog = '$id_psikolog'");
    $conn->query("DELETE FROM psikolog WHERE id_psikolog = '$id_psikolog'");
    $conn->query("DELETE FROM pengguna WHERE id_user = '$id_user'");
    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Psikolog berhasil dihapus']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Gagal: ' . $e->getMessage()]);
}
?>