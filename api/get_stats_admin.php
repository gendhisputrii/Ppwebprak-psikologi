<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../koneksi.php';

// Total psikolog terdaftar
$total_psikolog = $conn->query("SELECT COUNT(*) FROM psikolog")->fetch_row()[0];

// Total mahasiswa (mahasiswa disimpan di tabel pengguna dengan role_user = 'mahasiswa')
$total_mahasiswa = $conn->query("SELECT COUNT(*) FROM pengguna WHERE role_user = 'mahasiswa'")->fetch_row()[0];

// Jadwal (reservasi) bulan ini
$jadwal_bulan_ini = $conn->query(
    "SELECT COUNT(*) FROM reservasi 
     WHERE MONTH(tanggal_reservasi) = MONTH(CURDATE()) 
       AND YEAR(tanggal_reservasi) = YEAR(CURDATE())"
)->fetch_row()[0];

// Total konten (artikel + video) diambil dari konten.json, bukan dari database
$total_konten = 0;
$kontenFile = __DIR__ . '/konten.json';
if (file_exists($kontenFile)) {
    $kontenData = json_decode(file_get_contents($kontenFile), true);
    if ($kontenData) {
        $total_konten = count($kontenData['artikel'] ?? []) + count($kontenData['video'] ?? []);
    }
}

echo json_encode([
    'status' => 'success',
    'data'   => [
        'total_psikolog'   => (int)$total_psikolog,
        'total_mahasiswa'  => (int)$total_mahasiswa,
        'jadwal_bulan_ini' => (int)$jadwal_bulan_ini,
        'total_konten'     => (int)$total_konten
    ]
]);