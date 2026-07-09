<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
require_once '../koneksi.php';

$data = json_decode(file_get_contents('php://input'), true);
$id_reservasi = $data['id_reservasi'] ?? '';
$status = $data['status_reservasi'] ?? '';
$link_meeting = $data['link_meeting'] ?? null;
$lokasi = $data['lokasi'] ?? null;

if (!$id_reservasi || !$status) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
    exit;
}

$stmt = $conn->prepare("UPDATE reservasi SET status_reservasi = ? WHERE id_reservasi = ?");
$stmt->bind_param('ss', $status, $id_reservasi);
if (!$stmt->execute()) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal update status']);
    exit;
}

if ($status === 'dikonfirmasi') {
    if ($lokasi) {
        $stmt2 = $conn->prepare("UPDATE reservasi SET lokasi = ? WHERE id_reservasi = ?");
        $stmt2->bind_param('ss', $lokasi, $id_reservasi);
        $stmt2->execute();
    }

    if ($link_meeting) {
        $cek = $conn->prepare("SELECT id_konsultasi FROM konsultasi WHERE id_reservasi = ?");
        $cek->bind_param('s', $id_reservasi);
        $cek->execute();
        $res = $cek->get_result();

        if ($res->num_rows > 0) {
            $upd = $conn->prepare("UPDATE konsultasi SET link_meeting = ? WHERE id_reservasi = ?");
            $upd->bind_param('ss', $link_meeting, $id_reservasi);
            $upd->execute();
        } else {
            $get = $conn->prepare("
                SELECT r.id_mahasiswa, p.id_psikolog
                FROM reservasi r
                JOIN jadwal_praktik j ON r.id_jadwal = j.id_jadwal
                JOIN psikolog p ON j.id_psikolog = p.id_psikolog
                WHERE r.id_reservasi = ?
            ");
            $get->bind_param('s', $id_reservasi);
            $get->execute();
            $row = $get->get_result()->fetch_assoc();

            if ($row) {
                $id_konsultasi = substr(md5(uniqid()), 0, 10);
                $ins = $conn->prepare("INSERT INTO konsultasi (id_konsultasi, id_reservasi, id_mahasiswa, id_psikolog, link_meeting) VALUES (?, ?, ?, ?, ?)");
                $ins->bind_param('sssss', $id_konsultasi, $id_reservasi, $row['id_mahasiswa'], $row['id_psikolog'], $link_meeting);
                $ins->execute();
            }
        }
    }
}

echo json_encode(['status' => 'success', 'message' => 'Status berhasil diperbarui']);
?>