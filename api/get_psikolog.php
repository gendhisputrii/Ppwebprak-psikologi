<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../koneksi.php';

$result = $conn->query("
    SELECT 
        id_psikolog, 
        nama_psikolog, 
        spesialisasi, 
        tarif, 
        deskripsi_psikolog
    FROM psikolog
    ORDER BY nama_psikolog ASC
");

if (!$result) {
    echo json_encode(['status' => 'error', 'message' => $conn->error]);
    exit;
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode(['status' => 'success', 'data' => $data]);
?>