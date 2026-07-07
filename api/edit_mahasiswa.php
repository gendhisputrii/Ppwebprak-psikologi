<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
require_once '../koneksi.php';

$data = json_decode(file_get_contents('php://input'), true);
$id_user = $data['id_user'] ?? '';
$nama = trim($data['nama'] ?? '');
$hp = trim($data['hp'] ?? '');

if (!$id_user || !$nama) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
    exit;
}

$stmt = $conn->prepare("UPDATE pengguna SET nama_user = ?, no_hp = ? WHERE id_user = ?");
$stmt->bind_param('sss', $nama, $hp, $id_user);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Mahasiswa berhasil diperbarui']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal update']);
}
?>