<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../koneksi.php';

$total_pendapatan = $conn->query("
    SELECT SUM(p.tarif) as total FROM reservasi r
    JOIN jadwal_praktik j ON r.id_jadwal = j.id_jadwal
    JOIN psikolog p ON j.id_psikolog = p.id_psikolog
    WHERE r.status_reservasi = 'selesai'
")->fetch_assoc()['total'] ?? 0;

$total_lunas = $conn->query("SELECT COUNT(*) as total FROM reservasi WHERE status_reservasi = 'selesai'")->fetch_assoc()['total'];
$total_belum = $conn->query("SELECT COUNT(*) as total FROM reservasi WHERE status_reservasi = 'menunggu'")->fetch_assoc()['total'];

$result = $conn->query("
    SELECT r.id_reservasi, r.tanggal_reservasi, r.status_reservasi,
           um.nama_user as nama_mahasiswa,
           up.nama_user as nama_psikolog,
           p.tarif
    FROM reservasi r
    JOIN pengguna um ON r.id_mahasiswa = um.id_user
    JOIN jadwal_praktik j ON r.id_jadwal = j.id_jadwal
    JOIN psikolog p ON j.id_psikolog = p.id_psikolog
    JOIN pengguna up ON p.id_user = up.id_user
    ORDER BY r.tanggal_reservasi DESC
");

$transaksi = [];
while ($row = $result->fetch_assoc()) {
    $transaksi[] = $row;
}

echo json_encode([
    'status' => 'success',
    'data' => [
        'total_pendapatan' => $total_pendapatan,
        'total_lunas' => $total_lunas,
        'total_belum' => $total_belum,
        'transaksi' => $transaksi
    ]
]);
?>