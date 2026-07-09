<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../koneksi.php';

$aktivitas = [];

function waktuLalu($timestamp) {
    if (!$timestamp) return '-';
    $diff = time() - strtotime($timestamp);
    if ($diff < 60) return 'Baru saja';
    if ($diff < 3600) return floor($diff / 60) . ' menit lalu';
    if ($diff < 86400) return floor($diff / 3600) . ' jam lalu';
    if ($diff < 2592000) return floor($diff / 86400) . ' hari lalu';
    return date('d M Y', strtotime($timestamp));
}

// 1. Mahasiswa baru mendaftar
$q = $conn->query(
    "SELECT nama_user, created_at FROM pengguna 
     WHERE role_user = 'mahasiswa' AND created_at IS NOT NULL
     ORDER BY created_at DESC LIMIT 5"
);
while ($row = $q->fetch_assoc()) {
    $aktivitas[] = [
        'text'  => "Mahasiswa baru <b>{$row['nama_user']}</b> mendaftar",
        'time'  => $row['created_at'],
        'color' => '#6352c7'
    ];
}

// 2. Psikolog baru bergabung
$q = $conn->query(
    "SELECT nama_psikolog, created_at FROM psikolog 
     WHERE created_at IS NOT NULL
     ORDER BY created_at DESC LIMIT 5"
);
while ($row = $q->fetch_assoc()) {
    $aktivitas[] = [
        'text'  => "Psikolog <b>{$row['nama_psikolog']}</b> bergabung",
        'time'  => $row['created_at'],
        'color' => '#4a90d9'
    ];
}

// 3. Aktivitas reservasi (dibuat / status diubah)
$q = $conn->query(
    "SELECT r.status_reservasi, r.updated_at, p.nama_user AS nama_mahasiswa
     FROM reservasi r
     LEFT JOIN pengguna p ON r.id_mahasiswa = p.id_user
     WHERE r.updated_at IS NOT NULL
     ORDER BY r.updated_at DESC LIMIT 8"
);
while ($row = $q->fetch_assoc()) {
    $nama = $row['nama_mahasiswa'] ?? 'Mahasiswa';
    $status = $row['status_reservasi'];

    if ($status === 'dikonfirmasi') {
        $text = "Jadwal <b>{$nama}</b> dikonfirmasi";
        $color = '#16a34a';
    } elseif ($status === 'dibatalkan') {
        $text = "Reservasi <b>{$nama}</b> dibatalkan";
        $color = '#dc2626';
    } elseif ($status === 'selesai') {
        $text = "Sesi konsultasi <b>{$nama}</b> selesai";
        $color = '#8b88a8';
    } else {
        $text = "<b>{$nama}</b> mengajukan reservasi baru";
        $color = '#d97706';
    }

    $aktivitas[] = ['text' => $text, 'time' => $row['updated_at'], 'color' => $color];
}

// 4. Konten (artikel & video) terbaru — pakai urutan array di konten.json sebagai proxy "terbaru"
$kontenFile = __DIR__ . '/konten.json';
if (file_exists($kontenFile)) {
    $mtime = date('Y-m-d H:i:s', filemtime($kontenFile));
    $kontenData = json_decode(file_get_contents($kontenFile), true);

    if (!empty($kontenData['artikel'])) {
        $last = end($kontenData['artikel']);
        $aktivitas[] = [
            'text'  => "Artikel <b>\"{$last['judul']}\"</b> dipublikasi",
            'time'  => $mtime,
            'color' => '#d97706'
        ];
    }
    if (!empty($kontenData['video'])) {
        $last = end($kontenData['video']);
        $aktivitas[] = [
            'text'  => "Video <b>\"{$last['judul']}\"</b> ditambahkan",
            'time'  => $mtime,
            'color' => '#6352c7'
        ];
    }
}

// Urutkan semua aktivitas berdasarkan waktu terbaru, ambil 8 teratas
usort($aktivitas, fn($a, $b) => strtotime($b['time']) <=> strtotime($a['time']));
$aktivitas = array_slice($aktivitas, 0, 8);

// Format waktu jadi "X menit/jam lalu"
foreach ($aktivitas as &$a) {
    $a['time_label'] = waktuLalu($a['time']);
    unset($a['time']);
}

echo json_encode(['status' => 'success', 'data' => $aktivitas]);