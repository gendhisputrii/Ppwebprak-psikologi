<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once '../koneksi.php';

$data     = json_decode(file_get_contents('php://input'), true);
$nama     = trim($data['nama'] ?? '');
$email    = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$no_hp    = $data['no_hp'] ?? '';
$role     = $data['role'] ?? 'mahasiswa';

if (!$nama || !$email || !$password) {
    echo json_encode(['status' => 'error', 'message' => 'Semua field wajib diisi']);
    exit;
}

$cek = $conn->prepare("SELECT id_user FROM pengguna WHERE email_user = ?");
$cek->bind_param('s', $email);
$cek->execute();
$cek->store_result();

if ($cek->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Email sudah terdaftar']);
    exit;
}

$prefix  = ($role === 'mahasiswa') ? 'USR' : 'PSI';
$id_user = $prefix . rand(1000000, 9999999);
$hashed  = password_hash($password, PASSWORD_BCRYPT);

$stmt = $conn->prepare("INSERT INTO pengguna (id_user, nama_user, email_user, password_user, no_hp, role_user, status_verifikasi) VALUES (?, ?, ?, ?, ?, ?, 0)");
$stmt->bind_param('ssssss', $id_user, $nama, $email, $hashed, $no_hp, $role);

if (!$stmt->execute()) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal daftar, coba lagi']);
    exit;
}

if ($role === 'mahasiswa') {
    $conn->query("INSERT INTO mahasiswa (id_mahasiswa) VALUES ('$id_user')");
} else {
    $spesialis = $data['spesialisasi'] ?? '';
    $ins = $conn->prepare("INSERT INTO psikolog (id_psikolog, nama_psikolog, spesialisasi) VALUES (?, ?, ?)");
    $ins->bind_param('sss', $id_user, $nama, $spesialis);
    $ins->execute();
}

echo json_encode(['status' => 'success', 'message' => 'Registrasi berhasil']);
?>