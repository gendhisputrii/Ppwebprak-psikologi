<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once '../koneksi.php';

$data           = json_decode(file_get_contents('php://input'), true);
$id_mahasiswa   = $data['id_mahasiswa'] ?? '';
$id_jadwal      = $data['id_jadwal'] ?? '';
$tanggal        = $data['tanggal_reservasi'] ?? '';
$waktu          = $data['waktu_reservasi'] ?? '';

if (!$id_mahasiswa || !$id_jadwal || !$tanggal || !$waktu) {
    echo json_encode(['status' => 'error', 'message' => 'Semua field wajib diisi']);
    exit;
}

// Cek apakah jadwal di tanggal & waktu yang sama sudah ada
$cek = $conn->prepare("
    SELECT id_reservasi FROM reservasi
    WHERE id_jadwal = ? AND tanggal_reservasi = ? AND waktu_reservasi = ?
    AND status_reservasi != 'dibatalkan'
");
$cek->bind_param('sss', $id_jadwal, $tanggal, $waktu);
$cek->execute();
$cek->store_result();

if ($cek->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Jadwal ini sudah dipesan, pilih waktu lain']);
    exit;
}

// Generate ID reservasi
$id_reservasi = 'RES' . rand(1000000, 9999999);

$stmt = $conn->prepare("
    INSERT INTO reservasi (id_reservasi, tanggal_reservasi, waktu_reservasi, status_reservasi, id_mahasiswa, id_jadwal)
    VALUES (?, ?, ?, 'menunggu', ?, ?)
");
$stmt->bind_param('sssss', $id_reservasi, $tanggal, $waktu, $id_mahasiswa, $id_jadwal);

if (!$stmt->execute()) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal membuat reservasi, coba lagi']);
    exit;
}

echo json_encode([
    'status'       => 'success',
    'message'      => 'Reservasi berhasil dibuat',
    'id_reservasi' => $id_reservasi
]);
?>