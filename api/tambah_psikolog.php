<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
require_once '../koneksi.php';

$data = json_decode(file_get_contents('php://input'), true);
$nama = trim($data['nama'] ?? '');
$email = trim($data['email'] ?? '');
$password = password_hash($data['password'] ?? '', PASSWORD_BCRYPT);
$hp = trim($data['hp'] ?? '');
$spesialisasi = trim($data['spesialisasi'] ?? '');
$tarif = intval($data['tarif'] ?? 0);

if (!$nama || !$email || !$data['password']) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
    exit;
}

$cek = $conn->prepare("SELECT id_user FROM pengguna WHERE email_user = ?");
$cek->bind_param('s', $email);
$cek->execute();
if ($cek->get_result()->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Email sudah terdaftar']);
    exit;
}

$conn->begin_transaction();

try {
    $id_user = 'USR' . time();
    $stmt1 = $conn->prepare("INSERT INTO pengguna (id_user, nama_user, email_user, password_user, no_hp, role_user) VALUES (?, ?, ?, ?, ?, 'psikolog')");
    $stmt1->bind_param('sssss', $id_user, $nama, $email, $password, $hp);
    $stmt1->execute();

    $id_psikolog = 'PSI' . time();
    $stmt2 = $conn->prepare("INSERT INTO psikolog (id_psikolog, id_user, spesialisasi, tarif, status) VALUES (?, ?, ?, ?, 'aktif')");
    $stmt2->bind_param('sssi', $id_psikolog, $id_user, $spesialisasi, $tarif);
    $stmt2->execute();

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Psikolog berhasil ditambahkan']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Gagal: ' . $e->getMessage()]);
}
?>