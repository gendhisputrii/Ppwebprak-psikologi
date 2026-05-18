<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../koneksi.php';

$result = $conn->query("
    SELECT p.id_psikolog, p.nama_psikolog, p.spesialisasi, p.tarif, p.deskripsi_psikolog,
           u.email_user, u.no_hp
    FROM psikolog p
    JOIN pengguna u ON p.id_psikolog = u.id_user
    ORDER BY p.nama_psikolog ASC
");

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode(['status' => 'success', 'data' => $data]);
?>