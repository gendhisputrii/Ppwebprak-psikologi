<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../koneksi.php';

$input = json_decode(file_get_contents('php://input'), true);
$id_psikolog = $input['id_psikolog'] ?? '';
$hari = $input['hari'] ?? '';
$jam_mulai = $input['jam_mulai'] ?? '';
$jam_selesai = $input['jam_selesai'] ?? '';

if (!$id_psikolog || !$hari || !$jam_mulai || !$jam_selesai) {
    echo json_encode(['status' => 'error', 'message' => 'Semua field wajib diisi']);
    exit;
}

// Generate ID unik manual, karena kolom id_jadwal bertipe CHAR (bukan AUTO_INCREMENT)
$id_jadwal = uniqid();

$stmt = $conn->prepare("INSERT INTO jadwal_praktik (id_jadwal, id_psikolog, hari, jam_mulai, jam_selesai) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param('sssss', $id_jadwal, $id_psikolog, $hari, $jam_mulai, $jam_selesai);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'id_jadwal' => $id_jadwal]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan jadwal: ' . $stmt->error]);
}
?>