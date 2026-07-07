<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
require_once '../koneksi.php';

$data = json_decode(file_get_contents('php://input'), true);
$id_psikolog = $data['id_psikolog'] ?? '';
$hari = $data['hari'] ?? '';
$jam_mulai = $data['jam_mulai'] ?? '';
$jam_selesai = $data['jam_selesai'] ?? '';

if (!$id_psikolog || !$hari || !$jam_mulai || !$jam_selesai) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
    exit;
}

$id_jadwal = 'JAD' . time();
$stmt = $conn->prepare("INSERT INTO jadwal_praktik (id_jadwal, id_psikolog, hari, jam_mulai, jam_selesai, status) VALUES (?, ?, ?, ?, ?, 'aktif')");
$stmt->bind_param('sssss', $id_jadwal, $id_psikolog, $hari, $jam_mulai, $jam_selesai);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Jadwal berhasil ditambahkan']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal: ' . $stmt->error]);
}
?>