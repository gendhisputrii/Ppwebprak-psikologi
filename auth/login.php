<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once '../koneksi.php';

$data     = json_decode(file_get_contents('php://input'), true);
$email    = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(['status' => 'error', 'message' => 'Email dan password wajib diisi']);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM pengguna WHERE email_user = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$user   = $result->fetch_assoc();

if (!$user || !password_verify($password, $user['password_user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Email atau password salah']);
    exit;
}

// Hapus password sebelum dikirim ke frontend
unset($user['password_user']);

echo json_encode(['status' => 'success', 'user' => $user]);
?>