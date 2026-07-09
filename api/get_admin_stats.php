<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../koneksi.php';

$mahasiswa = $conn->query("SELECT COUNT(*) as total FROM pengguna WHERE role_user = 'mahasiswa'")->fetch_assoc()['total'];
$psikolog = $conn->query("SELECT COUNT(*) as total FROM pengguna WHERE role_user = 'psikolog'")->fetch_assoc()['total'];
$reservasi = $conn->query("SELECT COUNT(*) as total FROM reservasi")->fetch_assoc()['total'];
$pendapatan = $conn->query("SELECT SUM(p.tarif) as total FROM reservasi r JOIN jadwal_praktik j ON r.id_jadwal = j.id_jadwal JOIN psikolog p ON j.id_psikolog = p.id_psikolog WHERE r.status_reservasi = 'selesai'")->fetch_assoc()['total'] ?? 0;

// Mahasiswa baru bulan ini
$mahasiswa_baru = $conn->query("SELECT COUNT(*) as total FROM pengguna WHERE role_user = 'mahasiswa' AND MONTH(created_at) = MONTH(CURRENT_DATE())")->fetch_assoc()['total'] ?? 0;

// Psikolog aktif
$psikolog_aktif = $conn->query("SELECT COUNT(*) as total FROM psikolog WHERE status = 'aktif'")->fetch_assoc()['total'] ?? 0;

// Reservasi hari ini
$reservasi_hari_ini = $conn->query("SELECT COUNT(*) as total FROM reservasi WHERE tanggal_reservasi = CURRENT_DATE()")->fetch_assoc()['total'] ?? 0;

echo json_encode([
    'status' => 'success',
    'data' => [
        'total_mahasiswa' => $mahasiswa,
        'total_psikolog' => $psikolog,
        'total_reservasi' => $reservasi,
        'total_pendapatan' => $pendapatan,
        'mahasiswa_baru' => $mahasiswa_baru,
        'psikolog_aktif' => $psikolog_aktif,
        'reservasi_hari_ini' => $reservasi_hari_ini,
        'pendapatan_growth' => 12
    ]
]);
?>