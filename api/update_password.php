<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../koneksi.php';

$data         = json_decode(file_get_contents('php://input'), true);
$id_user      = trim($data['id_user'] ?? '');
$pass_lama    = $data['password_lama'] ?? '';
$pass_baru    = $data['password_baru'] ?? '';

if (!$id_user || !$pass_lama || !$pass_baru) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
    exit;
}

$stmt = $conn->prepare("SELECT password_user FROM pengguna WHERE id_user = ?");
$stmt->bind_param('s', $id_user);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row || !password_verify($pass_lama, $row['password_user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Password lama salah']);
    exit;
}

$hashBaru = password_hash($pass_baru, PASSWORD_DEFAULT);
$update = $conn->prepare("UPDATE pengguna SET password_user = ? WHERE id_user = ?");
$update->bind_param('ss', $hashBaru, $id_user);

if ($update->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal update password']);
}