<?php
// TAMPILKAN ERROR UNTUK DEBUG
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

try {
    require_once '../koneksi.php';
    
    if (!isset($conn) || $conn->connect_error) {
        throw new Exception('Koneksi database gagal: ' . ($conn->connect_error ?? 'unknown'));
    }

    $data = json_decode(file_get_contents('php://input'), true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('JSON invalid: ' . json_last_error_msg());
    }

$id_mahasiswa = $data['id_mahasiswa'] ?? '';
$id_jadwal    = $data['id_jadwal'] ?? '';
$tanggal      = $data['tanggal_reservasi'] ?? '';
$waktu        = $data['waktu_reservasi'] ?? '';
$keluhan      = trim($data['keluhan'] ?? '');
$jenis        = strtolower(trim($data['jenis_konsultasi'] ?? 'online'));

// Validasi supaya nilainya cuma bisa 'online' atau 'offline'
if (!in_array($jenis, ['online', 'offline'])) {
    $jenis = 'online';
}

    if (!$id_mahasiswa || !$id_jadwal || !$tanggal || !$waktu) {
        echo json_encode(['status' => 'error', 'message' => 'Data reservasi tidak lengkap']);
        exit;
    }

    // Cek double booking
    $cek_double = $conn->prepare("
        SELECT id_reservasi FROM reservasi 
        WHERE id_mahasiswa = ? AND id_jadwal = ? AND tanggal_reservasi = ? 
        AND status_reservasi != 'dibatalkan'
    ");
    
    if (!$cek_double) {
        throw new Exception('Prepare error: ' . $conn->error);
    }
    
    $cek_double->bind_param('sss', $id_mahasiswa, $id_jadwal, $tanggal);
    $cek_double->execute();
    
    if ($cek_double->get_result()->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Anda sudah memesan jadwal ini']);
        exit;
    }
    $cek_double->close();

    // Generate ID unik dengan loop sampai benar-benar tidak duplikat
    do {
        $id_reservasi = 'RES' . strtoupper(bin2hex(random_bytes(8)));
        
        $cek_id = $conn->prepare("SELECT id_reservasi FROM reservasi WHERE id_reservasi = ?");
        if (!$cek_id) {
            throw new Exception('Prepare error (cek id): ' . $conn->error);
        }
        $cek_id->bind_param('s', $id_reservasi);
        $cek_id->execute();
        $exists = $cek_id->get_result()->num_rows > 0;
        $cek_id->close();
    } while ($exists);

    // Insert reservasi
    $status = 'menunggu';
   $stmt = $conn->prepare("
    INSERT INTO reservasi (id_reservasi, id_mahasiswa, id_jadwal, tanggal_reservasi, waktu_reservasi, keluhan, status_reservasi, jenis_konsultasi) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

if (!$stmt) {
    throw new Exception('Prepare error (insert): ' . $conn->error);
}

$stmt->bind_param('ssssssss', $id_reservasi, $id_mahasiswa, $id_jadwal, $tanggal, $waktu, $keluhan, $status, $jenis);

    if ($stmt->execute()) {
        $stmt->close();
        echo json_encode([
            'status'  => 'success',
            'message' => 'Reservasi berhasil dibuat',
            'data'    => [
                'id_reservasi' => $id_reservasi,
                'status'       => $status
            ]
        ]);
    } else {
        throw new Exception('Execute error: ' . $stmt->error);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>