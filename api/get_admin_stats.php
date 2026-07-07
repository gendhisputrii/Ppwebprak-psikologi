<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../koneksi.php';

$mahasiswa = $conn->query("SELECT COUNT(*) as total FROM pengguna WHERE role_user = 'mahasiswa'")->fetch_assoc()['total'];
$psikolog = $conn->query("SELECT COUNT(*) as total FROM pengguna WHERE role_user = 'psikolog'")->fetch_assoc()['total'];
$reservasi = $conn->query("SELECT COUNT(*) as total FROM reservasi")->fetch_assoc()['total'];
$pendapatan = $conn->query("
    SELECT SUM(p.tarif) as total FROM reservasi r
    JOIN jadwal_praktik j ON r.id_jadwal = j.id_jadwal
    JOIN psikolog p ON j.id_psikolog = p.id_psikolog
    WHERE r.status_reservasi = 'selesai'
")->fetch_assoc()['total'] ?? 0;

$mahasiswa_baru = $conn->query("
    SELECT COUNT(*) as total FROM pengguna 
    WHERE role_user = 'mahasiswa' 
    AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
")->fetch_assoc()['total'] ?? 0;

echo json_encode([
    'status' => 'success',
    'data' => [
        'total_mahasiswa' => $mahasiswa,
        'total_psikolog' => $psikolog,
        'total_reservasi' => $reservasi,
        'total_pendapatan' => $pendapatan,
        'mahasiswa_baru' => $mahasiswa_baru,
        'psikolog_aktif' => $psikolog,
        'reservasi_hari_ini' => $reservasi_hari_ini,
        'pendapatan_growth' => 12
    ]
]);
?>