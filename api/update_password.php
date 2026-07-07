<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once '../koneksi.php';

$data        = json_decode(file_get_contents('php://input'), true);
$id_user     = $data['id_user'] ?? '';
$pass_lama   = $data['password_lama'] ?? '';
$pass_baru   = $data['password_baru'] ?? '';

if (!$id_user || !$pass_lama || !$pass_baru) {
    echo json_encode(['status' => 'error', 'message' => 'Semua field wajib diisi']);
    exit;
}

// Ambil password lama dari database
$stmt = $conn->prepare("SELECT password_user FROM pengguna WHERE id_user = ?");
$stmt->bind_param('s', $id_user);
$stmt->execute();
$result = $stmt->get_result();
$user   = $result->fetch_assoc();

if (!$user || !password_verify($pass_lama, $user['password_user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Password lama salah']);
    exit;
}

if (strlen($pass_baru) < 8) {
    echo json_encode(['status' => 'error', 'message' => 'Password baru minimal 8 karakter']);
    exit;
}

$hashed = password_hash($pass_baru, PASSWORD_BCRYPT);
$stmt2  = $conn->prepare("UPDATE pengguna SET password_user = ? WHERE id_user = ?");
$stmt2->bind_param('ss', $hashed, $id_user);

if ($stmt2->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Password berhasil diperbarui']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal update password']);
}
?>