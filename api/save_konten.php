<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Method tidak diizinkan. Gunakan POST.'
    ]);
    exit;
}

// Baca input
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

if (!$input) {
    echo json_encode([
        'status' => 'error',
        'message' => 'JSON tidak valid: ' . json_last_error_msg(),
        'raw' => $rawInput
    ]);
    exit;
}

if (!isset($input['tipe']) || !isset($input['data'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Data tidak lengkap. Butuh: tipe dan data',
        'received' => $input
    ]);
    exit;
}

$tipe = $input['tipe'];
$dataBaru = $input['data'];

if (!in_array($tipe, ['artikel', 'video'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Tipe harus "artikel" atau "video"'
    ]);
    exit;
}

$file = __DIR__ . '/konten.json';

// Cek apakah folder bisa ditulis
if (!is_writable(__DIR__)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Folder api/ tidak bisa ditulis. Cek permission.',
        'folder' => __DIR__,
        'writable' => is_writable(__DIR__)
    ]);
    exit;
}

// Baca data existing
if (file_exists($file)) {
    $json = file_get_contents($file);
    $data = json_decode($json, true);
    if ($data === null) {
        $data = ['artikel' => [], 'video' => []];
    }
} else {
    $data = ['artikel' => [], 'video' => []];
}

// Tambah ID dan timestamp
$dataBaru['id'] = time() . rand(100, 999);
$dataBaru['created_at'] = date('Y-m-d H:i:s');

// Tambahkan ke array
$data[$tipe][] = $dataBaru;

// Simpan ke file
$jsonOutput = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$result = file_put_contents($file, $jsonOutput, LOCK_EX);

if ($result === false) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal menyimpan ke file. Cek permission file.',
        'file' => $file,
        'exists' => file_exists($file),
        'writable' => is_writable($file) || !file_exists($file)
    ]);
    exit;
}

echo json_encode([
    'status' => 'success',
    'message' => 'Konten berhasil disimpan',
    'id' => $dataBaru['id'],
    'file_size' => $result
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);