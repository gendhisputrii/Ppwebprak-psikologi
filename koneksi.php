<?php
date_default_timezone_set('Asia/Jakarta');
$host    = 'sql308.infinityfree.com';
$db      = 'if0_41822826_ruangpulih';
$user    = 'if0_41822826';
$pass    = 'Kelompok7pwbprk';
$charset = 'utf8mb4';

$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset($charset);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Koneksi database gagal']);
    exit;
}
?>