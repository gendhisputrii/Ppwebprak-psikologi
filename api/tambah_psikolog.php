<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../koneksi.php';

$input = json_decode(file_get_contents('php://input'), true);
$nama_psikolog = $input['nama_psikolog'] ?? '';
$email = $input['email'] ?? '';
$spesialisasi = $input['spesialisasi'] ?? '';
$tarif = $input['tarif'] ?? 0;
$no_str = $input['no_str'] ?? '';

if (!$nama_psikolog || !$email || !$spesialisasi) {
    echo json_encode(['status' => 'error', 'message' => 'Nama, email, dan spesialisasi wajib diisi']);
    exit;
}

$id_psikolog = substr(uniqid(), -10);

$stmt = $conn->prepare("INSERT INTO psikolog (id_psikolog, nama_psikolog, email, spesialisasi, tarif, no_str) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param('ssssds', $id_psikolog, $nama_psikolog, $email, $spesialisasi, $tarif, $no_str);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'id' => $id_psikolog]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan: ' . $stmt->error]);
}
?>