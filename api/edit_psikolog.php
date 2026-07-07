<?php
ob_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
require_once '../koneksi.php';
ob_clean();

$data         = json_decode(file_get_contents('php://input'), true);
$id_psikolog  = $data['id_psikolog'] ?? '';
$nama         = trim($data['nama'] ?? '');
$hp           = trim($data['hp'] ?? '');
$spesialisasi = trim($data['spesialisasi'] ?? '');
$tarif        = intval($data['tarif'] ?? 0);
$status       = $data['status'] ?? 'aktif';

if (!$id_psikolog || !$nama) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
    exit;
}

$stmt = $conn->prepare("SELECT id_user FROM psikolog WHERE id_psikolog = ?");
$stmt->bind_param('s', $id_psikolog);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
    echo json_encode(['status' => 'error', 'message' => 'Psikolog tidak ditemukan']);
    exit;
}

$id_user = $row['id_user'];

$stmt1 = $conn->prepare("UPDATE pengguna SET nama_user = ?, no_hp = ? WHERE id_user = ?");
$stmt1->bind_param('sss', $nama, $hp, $id_user);

$stmt2 = $conn->prepare("UPDATE psikolog SET spesialisasi = ?, tarif = ?, status = ? WHERE id_psikolog = ?");
$stmt2->bind_param('siss', $spesialisasi, $tarif, $status, $id_psikolog);

if ($stmt1->execute() && $stmt2->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Psikolog berhasil diperbarui']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal update: ' . $conn->error]);
}