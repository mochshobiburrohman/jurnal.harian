<?php
session_start();
if ($_SESSION['role'] !== 'kepala_sekolah') {
    exit('Akses ditolak');
}

include '../../../koneksi.php';

$sql = "SELECT j.tanggal, j.isi_jurnal, j.status, g.nama
        FROM jurnal_harian j
        JOIN guru g ON j.id_guru = g.id
        ORDER BY j.tanggal DESC";

$data = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Jurnal Harian Guru</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            color: #000;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px 8px;
        }
        th {
            background: #f0f0f0;
        }
        .status-verified {
            color: green;
            font-weight: bold;
        }
        .status-pending {
            color: orange;
            font-weight: bold;
        }
    </style>
</head>

<body onload="window.print()">

<h2>Laporan Jurnal Harian Guru</h2>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Guru</th>
            <th>Tanggal</th>
            <th>Isi Jurnal</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; while ($row = $data->fetch_assoc()): ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= htmlspecialchars($row['nama']); ?></td>
            <td><?= $row['tanggal']; ?></td>
            <td><?= htmlspecialchars($row['isi_jurnal']); ?></td>
            <td class="<?= $row['status'] === 'verified'
                    ? 'status-verified'
                    : 'status-pending'; ?>">
                <?= ucfirst($row['status']); ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
