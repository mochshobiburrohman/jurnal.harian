<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../index.php");
    exit;
}

include '../../../koneksi.php';

// Inisialisasi variabel
$whereClauses = [];
$titleInfo = "";

// Variabel untuk Info Guru Spesifik (Untuk Header & Tanda Tangan)
$is_specific_guru = false;
$nama_guru_header = "";
$nip_guru_header = "";

// 1. CEK INPUT FILTER TANGGAL/BULAN
if (isset($_GET['tanggal']) && !empty($_GET['tanggal'])) {
    $tanggal = $conn->real_escape_string($_GET['tanggal']);
    $whereClauses[] = "j.tanggal = '$tanggal'";
    $titleInfo = "Harian: " . date('d F Y', strtotime($tanggal));

} elseif (isset($_GET['bulan']) && !empty($_GET['bulan'])) {
    $bulan = $conn->real_escape_string($_GET['bulan']); 
    $whereClauses[] = "DATE_FORMAT(j.tanggal, '%Y-%m') = '$bulan'";
    $titleInfo = "Bulanan: " . date('F Y', strtotime($bulan));

} else {
    echo "Harap pilih tanggal atau bulan terlebih dahulu.";
    exit;
}

// 2. CEK FILTER GURU
if (isset($_GET['id_guru']) && !empty($_GET['id_guru'])) {
    $id_guru = $conn->real_escape_string($_GET['id_guru']);
    $whereClauses[] = "j.id_guru = '$id_guru'";
    
    // Ambil data detail guru untuk ditampilkan di Header
    $qGuru = $conn->query("SELECT nama, nip FROM guru WHERE id = '$id_guru'");
    if ($qGuru->num_rows > 0) {
        $dGuru = $qGuru->fetch_assoc();
        $is_specific_guru = true;
        $nama_guru_header = $dGuru['nama'];
        $nip_guru_header = $dGuru['nip'];
    }
}

// Gabungkan Filter SQL
$whereSql = "";
if (count($whereClauses) > 0) {
    $whereSql = "WHERE " . implode(' AND ', $whereClauses);
}

// 3. QUERY DATA JURNAL
$sql = "SELECT j.*, g.nama as nama_guru, g.nip 
        FROM jurnal_harian j 
        JOIN guru g ON j.id_guru = g.id 
        $whereSql 
        ORDER BY j.tanggal ASC, j.jam_ke ASC";

$result = $conn->query($sql);
if (!$result) {
    die("Error Query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Jurnal - <?= $titleInfo ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid black; padding-bottom: 10px; }
        .header h2 { margin: 5px 0; font-size: 18px; }
        .header p { margin: 2px 0; font-size: 14px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 6px; vertical-align: top; }
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        
        .text-center { text-align: center; }
        .no-print { margin-bottom: 20px; padding: 10px; background: #eee; border: 1px solid #ccc; }
        
        /* Layout Tanda Tangan Flexbox */
        .signature-container {
            width: 100%;
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        .signature-box {
            width: 250px;
            text-align: center;
        }
        
        @media print {
            .no-print { display: none; }
            @page { margin: 1cm; size: landscape; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print">
        <a href="index.php" style="text-decoration: none; font-weight: bold; color: blue;">&larr; Kembali</a>
    </div>

    <div class="header">
        <h2>LAPORAN JURNAL KEGIATAN GURU</h2>
        <p>PERIODE: <?= strtoupper($titleInfo) ?></p>
        
        <?php if ($is_specific_guru): ?>
            <p style="margin-top: 5px;"><strong>NAMA GURU: <?= htmlspecialchars($nama_guru_header) ?></strong></p>
            <p>NIP: <?= htmlspecialchars($nip_guru_header) ?></p>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 3%">No</th>
                <th style="width: 12%">Hari/Tanggal</th>
                <?php if (!$is_specific_guru): ?>
                <th style="width: 20%">Nama Guru</th>
                <?php endif; ?>
                
                <th style="width: 15%">Kelas & Mapel</th>
                <th style="width: 8%">Jam Ke</th>
                <th>Isi Jurnal / Materi</th>
                <th style="width: 12%">Absensi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td class="text-center"><?= $no++; ?></td>
                    <td>
                        <?php 
                        $days = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
                        $dayEng = date('l', strtotime($row['tanggal']));
                        echo $days[$dayEng] . ",<br>" . date('d-m-Y', strtotime($row['tanggal']));
                        ?>
                    </td>
                    
                    <?php if (!$is_specific_guru): ?>
                    <td>
                        <strong><?= htmlspecialchars($row['nama_guru']); ?></strong><br>
                        <small>NIP: <?= htmlspecialchars($row['nip']); ?></small>
                    </td>
                    <?php endif; ?>

                    <td>
                        <strong><?= htmlspecialchars($row['mata_pelajaran']); ?></strong><br>
                        Kelas: <?= htmlspecialchars($row['kelas']); ?>
                    </td>
                    <td class="text-center">
                        <?= htmlspecialchars($row['jam_ke']); ?>
                    </td>
                    <td>
                        <?= nl2br(htmlspecialchars($row['isi_jurnal'])); ?>
                    </td>
                    <td style="font-size: 11px;">
                        Hadir: <b><?= $row['hadir'] ?></b><br>
                        Sakit: <?= $row['sakit'] ?><br>
                        Izin: <?= $row['izin'] ?><br>
                        Alpa: <?= $row['alpa'] ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?= $is_specific_guru ? '6' : '7' ?>" class="text-center" style="padding: 20px;">
                        Tidak ada data jurnal yang ditemukan.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="signature-container">
        
       

        <div class="signature-box">
            <p>Mengetahui,<br>Kepala Sekolah</p>
            <br><br><br><br>
            <p><strong>Rohmat Sampurno,S.T.</strong></p>
            <p>NIP </p>
        </div>

        <div class="signature-box">
            <?php if ($is_specific_guru): ?>
                <p>Tuban, <?= date('d F Y'); ?></p>
                <p>Guru Mata Pelajaran</p>
                <br><br><br><br><br>
                <p><strong><?= htmlspecialchars($nama_guru_header) ?></strong></p>
                <p>NIP <?= htmlspecialchars($nip_guru_header) ?></p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>