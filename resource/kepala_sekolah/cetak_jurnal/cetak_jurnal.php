<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'kepala_sekolah') {
    exit('Akses ditolak');
}

include '../../../koneksi.php';

// Ambil parameter filter
$id_guru = isset($_GET['id_guru']) ? $conn->real_escape_string($_GET['id_guru']) : '';
$bulan   = isset($_GET['bulan']) ? $conn->real_escape_string($_GET['bulan']) : ''; // Format YYYY-MM

// Validasi input
if (empty($id_guru) || empty($bulan)) {
    exit('Harap pilih guru dan bulan terlebih dahulu.');
}

// Ambil Nama Guru untuk Header Laporan
$q_guru = $conn->query("SELECT nama, nip FROM guru WHERE id = '$id_guru'");
$guru_info = $q_guru->fetch_assoc();
$nama_guru = $guru_info ? $guru_info['nama'] : 'Tidak Diketahui';
$nip_guru = $guru_info ? $guru_info['nip'] : '-';

// Format tanggal untuk query (YYYY-MM)
$filter_tanggal = $bulan; 

// Query Data Jurnal berdasarkan Guru dan Bulan
$sql = "SELECT j.tanggal, j.isi_jurnal, j.status, j.mata_pelajaran, j.kelas, j.hadir
        FROM jurnal_harian j
        WHERE j.id_guru = '$id_guru' 
        AND j.tanggal LIKE '$filter_tanggal%'
        ORDER BY j.tanggal ASC";

$data = $conn->query($sql);

// Format Bulan untuk Judul (Contoh: Januari 2026)
$nama_bulan = date("F Y", strtotime($bulan));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Jurnal - <?= htmlspecialchars($nama_guru) ?></title>
    <style>
        body { font-family: Arial, sans-serif; color: #000; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2, .header h3 { margin: 5px 0; }
        .info-table { width: 100%; margin-bottom: 20px; font-weight: bold; }
        .info-table td { padding: 5px; }
        table.data-table { width: 100%; border-collapse: collapse; font-size: 12px; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 8px; }
        table.data-table th { background: #f0f0f0; text-align: center; }
        .status-verified { color: green; font-weight: bold; }
        .status-pending { color: orange; font-weight: bold; }
        .status-rejected { color: red; font-weight: bold; }
        @media print {
            @page { margin: 2cm; }
            button { display: none; }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="header">
        <h2>LAPORAN JURNAL HARIAN GURU</h2>
        <h3>PERIODE: <?= strtoupper($nama_bulan) ?></h3>
    </div>

    <table class="info-table">
        <tr>
            <td width="150">Nama Guru</td>
            <td>: <?= htmlspecialchars($nama_guru); ?></td>
        </tr>
        <tr>
            <td>NIP</td>
            <td>: <?= htmlspecialchars($nip_guru); ?></td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal</th>
                <th width="15%">Mata Pelajaran</th>
                <th width="10%">Kelas</th>
                <th>Isi Jurnal / Materi</th>
                <th class="border border-gray-400 px-2 py-1">Hadir</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($data->num_rows > 0): ?>
                <?php $no = 1; while ($row = $data->fetch_assoc()): ?>
                <tr>
                    <td style="text-align: center;"><?= $no++; ?></td>
                    <td style="text-align: center;"><?= date('d-m-Y', strtotime($row['tanggal'])); ?></td>
                    <td style="text-align: center;"><?= htmlspecialchars($row['mata_pelajaran'] ?? '-'); ?></td>
                    <td style="text-align: center;"><?= htmlspecialchars($row['kelas'] ?? '-'); ?></td>
                    <td><?= nl2br(htmlspecialchars($row['isi_jurnal'])); ?></td>
                    <td class="border border-gray-400 px-2 py-1 text-center">
                        <?= $row['hadir'] ?> Siswa
                        </td>
                            <?= ucfirst($row['status']); ?>
                        </span>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">
                        Tidak ada data jurnal pada bulan ini.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top: 50px; float: right; text-align: center; width: 200px;">
        <p>Mengetahui,</p>
        <p>Kepala Sekolah</p>
        <br><br><br>
        <p>___________________</p>
    </div>

</body>
</html>