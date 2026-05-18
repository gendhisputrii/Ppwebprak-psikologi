<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../koneksi.php';

$result = $conn->query("
    SELECT p.id_user, p.nama_user, p.email_user, p.no_hp, p.status_verifikasi,
           m.nim_nip
    FROM pengguna p
    LEFT JOIN mahasiswa m ON p.id_user = m.id_mahasiswa
    WHERE p.role_user = 'mahasiswa'
    ORDER BY p.nama_user ASC
");

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode(['status' => 'success', 'data' => $data]);
?>