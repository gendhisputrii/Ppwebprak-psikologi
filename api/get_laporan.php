<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../koneksi.php';

$id_psikolog = $_GET['id_psikolog'] ?? '';

if (!$id_psikolog) {
    echo json_encode(['status' => 'error', 'message' => 'ID psikolog tidak valid']);
    exit;
}

// Total pendapatan & transaksi selesai
$stmt1 = $conn->prepare("
    SELECT SUM(p.tarif) as total_pendapatan, COUNT(*) as total_transaksi
    FROM reservasi r
    JOIN jadwal_praktik j ON r.id_jadwal = j.id_jadwal
    JOIN psikolog p ON j.id_psikolog = p.id_psikolog
    WHERE j.id_psikolog = ? AND r.status_reservasi = 'selesai'
");
$stmt1->bind_param('s', $id_psikolog);
$stmt1->execute();
$summary = $stmt1->get_result()->fetch_assoc();

// Transaksi lunas
$stmt2 = $conn->prepare("
    SELECT COUNT(*) as lunas FROM reservasi r
    JOIN jadwal_praktik j ON r.id_jadwal = j.id_jadwal
    WHERE j.id_psikolog = ? AND r.status_reservasi = 'selesai'
");
$stmt2->bind_param('s', $id_psikolog);
$stmt2->execute();
$lunas = $stmt2->get_result()->fetch_assoc()['lunas'] ?? 0;

// Transaksi belum dibayar
$stmt3 = $conn->prepare("
    SELECT COUNT(*) as belum FROM reservasi r
    JOIN jadwal_praktik j ON r.id_jadwal = j.id_jadwal
    WHERE j.id_psikolog = ? AND r.status_reservasi = 'menunggu'
");
$stmt3->bind_param('s', $id_psikolog);
$stmt3->execute();
$belum = $stmt3->get_result()->fetch_assoc()['belum'] ?? 0;

// Detail transaksi
$stmt4 = $conn->prepare("
    SELECT r.id_reservasi, r.tanggal_reservasi, r.waktu_reservasi, r.status_reservasi,
           u.nama_user as nama_mahasiswa, p.tarif
    FROM reservasi r
    JOIN pengguna u ON r.id_mahasiswa = u.id_user
    JOIN jadwal_praktik j ON r.id_jadwal = j.id_jadwal
    JOIN psikolog p ON j.id_psikolog = p.id_psikolog
    WHERE j.id_psikolog = ?
    ORDER BY r.tanggal_reservasi DESC
");
$stmt4->bind_param('s', $id_psikolog);
$stmt4->execute();
$result = $stmt4->get_result();

$transaksi = [];
while ($row = $result->fetch_assoc()) {
    $transaksi[] = $row;
}

echo json_encode([
    'status' => 'success',
    'data' => [
        'total_pendapatan' => $summary['total_pendapatan'] ?? 0,
        'total_transaksi' => $summary['total_transaksi'] ?? 0,
        'lunas' => $lunas,
        'belum' => $belum,
        'transaksi' => $transaksi
    ]
]);
?>