<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['tipe']) || !isset($input['id']) || !isset($input['data'])) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap. Butuh: tipe, id, data']);
    exit;
}

$tipe = $input['tipe'];
$id = $input['id'];
$dataBaru = $input['data'];

if (!in_array($tipe, ['artikel', 'video'])) {
    echo json_encode(['status' => 'error', 'message' => 'Tipe harus "artikel" atau "video"']);
    exit;
}

$file = __DIR__ . '/konten.json';
if (!file_exists($file)) {
    echo json_encode(['status' => 'error', 'message' => 'File tidak ditemukan']);
    exit;
}

$json = file_get_contents($file);
$data = json_decode($json, true);
if ($data === null) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal membaca data']);
    exit;
}

$found = false;
if (isset($data[$tipe])) {
    foreach ($data[$tipe] as &$item) {
        if ($item['id'] == $id) {
            foreach ($dataBaru as $key => $val) {
                $item[$key] = $val;
            }
            $found = true;
            break;
        }
    }
    unset($item);
}

if (!$found) {
    echo json_encode(['status' => 'error', 'message' => 'Konten tidak ditemukan']);
    exit;
}

$result = file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX);
if ($result === false) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan perubahan']);
    exit;
}

echo json_encode(['status' => 'success', 'message' => 'Konten berhasil diperbarui']);
?>