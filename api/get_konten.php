<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$file = __DIR__ . '/konten.json';

// Kalau file belum ada, buat file kosong
if (!file_exists($file)) {
    $default = ['artikel' => [], 'video' => []];
    file_put_contents($file, json_encode($default, JSON_PRETTY_PRINT));

    echo json_encode([
        'status' => 'success',
        'data' => $default,
        'note' => 'File baru dibuat'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$json = file_get_contents($file);
$data = json_decode($json, true);

if ($data === null) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal parse JSON: ' . json_last_error_msg(),
        'file_exists' => true,
        'file_size' => filesize($file)
    ]);
    exit;
}

// Pastikan struktur benar
if (!isset($data['artikel'])) $data['artikel'] = [];
if (!isset($data['video'])) $data['video'] = [];

echo json_encode([
    'status' => 'success',
    'data' => $data,
    'count' => [
        'artikel' => count($data['artikel']),
        'video' => count($data['video'])
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);