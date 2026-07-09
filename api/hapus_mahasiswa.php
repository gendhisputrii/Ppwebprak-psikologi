<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../koneksi.php';

$input = json_decode(file_get_contents('php://input'), true);
$id_user = $input['id_user'] ?? '';

if (!$id_user) {
    echo json_encode(['status' => 'error', 'message' => 'ID mahasiswa tidak valid']);
    exit;
}

try {
    // Hapus reservasi milik mahasiswa ini
    $stmtRes = $conn->prepare("DELETE FROM reservasi WHERE id_mahasiswa = ?");
    $stmtRes->bind_param('s', $id_user);
    $stmtRes->execute();

    // Hapus dari tabel mahasiswa
    $stmtMhs = $conn->prepare("DELETE FROM mahasiswa WHERE id_mahasiswa = ?");
    $stmtMhs->bind_param('s', $id_user);
    $stmtMhs->execute();

    // Baru hapus akun dari pengguna
    $stmt = $conn->prepare("DELETE FROM pengguna WHERE id_user = ? AND role_user = 'mahasiswa'");
    $stmt->bind_param('s', $id_user);
    $stmt->execute();

    echo json_encode(['status' => 'success']);
} catch (mysqli_sql_exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus: ' . $e->getMessage()]);
}
?>