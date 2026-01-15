<?php
session_start();
// Cek sesi login guru
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    header("Location: ../../../index.php");
    exit;
}

include '../../../koneksi.php';

$id_guru = $_SESSION['user_id'];

// Logika Hapus Jurnal
if (isset($_GET['hapus'])) {
    $id_jurnal = $_GET['hapus'];
    // Pastikan hanya menghapus jurnal milik sendiri
    $del = $conn->query("DELETE FROM jurnal_harian WHERE id='$id_jurnal' AND id_guru='$id_guru'");
    if ($del) {
        echo "<script>alert('Jurnal berhasil dihapus'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus jurnal'); window.location='index.php';</script>";
    }
}

// Query Ambil Data Jurnal
$sql = "SELECT * FROM jurnal_harian WHERE id_guru = '$id_guru' ORDER BY tanggal DESC, jam_ke ASC";
$result = $conn->query($sql);

// Simpan data ke array
$jurnals = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $jurnals[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Jurnal Saya</title>
    <link href="../../../src/output.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans antialiased">

    <?php include ("../../partials/navbar.php") ?>
    <?php include ("../../partials/sidebar_guru.php") ?>

    <main class="p-4 md:ml-64 h-auto pt-24">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 border-b border-gray-200 pb-4 dark:border-gray-700">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Kelola Jurnal</h1>
                <p class="text-base text-gray-600 dark:text-gray-400 mt-1">Pantau riwayat aktivitas mengajar Anda.</p>
            </div>
            
            <a href="add_journal.php" class="w-full sm:w-auto text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-bold rounded-lg text-sm px-5 py-3 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 flex items-center justify-center gap-2 shadow-md transition-transform transform hover:scale-105">
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 1v16M1 9h16"/>
                </svg>
                Tambah Jurnal
            </a>
        </div>

        <div class="block md:hidden space-y-6">
            <?php if (count($jurnals) > 0): ?>
                <?php foreach($jurnals as $row): ?>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden relative">
                    
                    <div class="px-5 py-3 bg-gray-100 dark:bg-gray-700 flex justify-between items-center border-b border-gray-200 dark:border-gray-600">
                        <div class="flex items-center gap-2 text-gray-900 dark:text-gray-100 font-bold text-sm">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <?= date('d M Y', strtotime($row['tanggal'])) ?>
                        </div>
                        <span class="px-3 py-1 rounded-md text-xs font-bold border 
                            <?= $row['status'] == 'verified' ? 'bg-green-100 text-green-700 border-green-300' : 
                               ($row['status'] == 'rejected' ? 'bg-red-100 text-red-700 border-red-300' : 'bg-yellow-100 text-yellow-700 border-yellow-300') ?>">
                            <?= $row['status'] == 'verified' ? 'DISETUJUI' : ($row['status'] == 'rejected' ? 'DITOLAK' : 'PENDING') ?>
                        </span>
                    </div>

                    <div class="p-5 space-y-4">
                        
                        <div class="flex justify-between items-start">
                            <div class="w-3/4">
                                <h3 class="text-xl font-black text-gray-900 dark:text-white leading-tight mb-1">
                                    <?= htmlspecialchars($row['mata_pelajaran']) ?>
                                </h3>
                                <div class="inline-block bg-gray-200 dark:bg-gray-600 px-2 py-0.5 rounded text-xs font-bold text-gray-700 dark:text-gray-200">
                                    Kelas <?= htmlspecialchars($row['kelas']) ?>
                                </div>
                            </div>
                            <div class="w-1/4 text-right">
                                <div class="flex flex-col items-end">
                                    <span class="text-[10px] uppercase font-bold text-gray-500 tracking-wider">Jam Ke</span>
                                    <span class="text-2xl font-bold text-blue-700 dark:text-blue-400 border-b-2 border-blue-500 leading-none pb-1">
                                        <?= htmlspecialchars($row['jam_ke']) ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 bg-blue-50 dark:bg-gray-700/40 rounded-lg border-l-4 border-blue-500">
                            <span class="block text-xs font-bold text-blue-800 dark:text-blue-300 uppercase mb-1">Materi:</span>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 leading-snug">
                                <?= htmlspecialchars(substr($row['isi_jurnal'], 0, 100)) . (strlen($row['isi_jurnal']) > 100 ? '...' : '') ?>
                            </p>
                        </div>

                        <div>
                            <span class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2 text-center tracking-widest">Kehadiran Siswa</span>
                            <div class="grid grid-cols-4 gap-2">
                                <div class="flex flex-col items-center bg-white border border-green-300 rounded-lg p-2 shadow-sm">
                                    <span class="text-xs font-bold text-gray-500">Hadir</span>
                                    <span class="text-lg font-black text-green-600"><?= $row['hadir'] ?></span>
                                </div>
                                <div class="flex flex-col items-center bg-white border border-yellow-300 rounded-lg p-2 shadow-sm">
                                    <span class="text-xs font-bold text-gray-500">Sakit</span>
                                    <span class="text-lg font-black text-yellow-600"><?= $row['sakit'] ?></span>
                                </div>
                                <div class="flex flex-col items-center bg-white border border-blue-300 rounded-lg p-2 shadow-sm">
                                    <span class="text-xs font-bold text-gray-500">Izin</span>
                                    <span class="text-lg font-black text-blue-600"><?= $row['izin'] ?></span>
                                </div>
                                <div class="flex flex-col items-center bg-white border border-red-300 rounded-lg p-2 shadow-sm">
                                    <span class="text-xs font-bold text-gray-500">Alpa</span>
                                    <span class="text-lg font-black text-red-600"><?= $row['alpa'] ?></span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="grid grid-cols-2 divide-x divide-gray-200 border-t border-gray-200 dark:border-gray-700 dark:divide-gray-700">
                        <a href="edit_journal.php?id=<?= $row['id'] ?>" class="py-3 text-sm font-bold text-blue-700 bg-gray-50 hover:bg-blue-100 dark:bg-gray-800 dark:text-blue-400 dark:hover:bg-gray-700 text-center transition">
                            EDIT DATA
                        </a>
                        <a href="index.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus data ini?')" class="py-3 text-sm font-bold text-red-700 bg-gray-50 hover:bg-red-100 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-gray-700 text-center transition">
                            HAPUS
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="bg-white p-6 rounded-xl shadow-md text-center">
                    <p class="text-gray-500 font-medium">Belum ada jurnal.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="hidden md:block bg-white shadow-lg rounded-xl overflow-hidden dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-200 border-b-2 border-gray-300 dark:border-gray-600">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-extrabold text-center border-r border-gray-200 w-16">No</th>
                            <th scope="col" class="px-6 py-4 font-extrabold border-r border-gray-200 w-48">Waktu</th>
                            <th scope="col" class="px-6 py-4 font-extrabold border-r border-gray-200 w-64">Kelas & Mapel</th>
                            <th scope="col" class="px-6 py-4 font-extrabold border-r border-gray-200">Materi</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-center border-r border-gray-200 w-40">Absensi</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-center border-r border-gray-200 w-32">Status</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-center w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <?php if (count($jurnals) > 0): ?>
                            <?php $no = 1; foreach($jurnals as $row): ?>
                            <tr class="hover:bg-blue-50 dark:hover:bg-gray-700 transition duration-150 even:bg-gray-50 dark:even:bg-gray-800/50">
                                
                                <td class="px-6 py-4 font-bold text-gray-900 dark:text-white text-center border-r border-gray-100">
                                    <?= $no++ ?>
                                </td>
                                
                                <td class="px-6 py-4 border-r border-gray-100">
                                    <div class="font-bold text-gray-900 dark:text-white text-base">
                                        <?= date('d/m/Y', strtotime($row['tanggal'])) ?>
                                    </div>
                                    <span class="inline-block mt-1 px-2 py-0.5 bg-blue-100 text-blue-800 text-xs font-bold rounded">
                                        Jam ke-<?= htmlspecialchars($row['jam_ke']) ?>
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 border-r border-gray-100">
                                    <div class="font-black text-gray-800 dark:text-white text-base">
                                        <?= htmlspecialchars($row['mata_pelajaran']) ?>
                                    </div>
                                    <div class="text-xs font-semibold text-gray-500 mt-1 uppercase tracking-wide">
                                        Kelas <?= htmlspecialchars($row['kelas']) ?>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 border-r border-gray-100">
                                    <p class="text-gray-700 dark:text-gray-300 font-medium line-clamp-2" title="<?= htmlspecialchars($row['isi_jurnal']) ?>">
                                        <?= htmlspecialchars($row['isi_jurnal']) ?>
                                    </p>
                                </td>
                                
                                <td class="px-6 py-4 text-center border-r border-gray-100">
                                    <div class="grid grid-cols-2 gap-1 justify-items-center">
                                        <span class="text-xs font-bold text-green-600 bg-green-50 px-1.5 rounded border border-green-100" title="Hadir">H: <?= $row['hadir'] ?></span>
                                        <span class="text-xs font-bold text-yellow-600 bg-yellow-50 px-1.5 rounded border border-yellow-100" title="Sakit">S: <?= $row['sakit'] ?></span>
                                        <span class="text-xs font-bold text-blue-600 bg-blue-50 px-1.5 rounded border border-blue-100" title="Izin">I: <?= $row['izin'] ?></span>
                                        <span class="text-xs font-bold text-red-600 bg-red-50 px-1.5 rounded border border-red-100" title="Alpa">A: <?= $row['alpa'] ?></span>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 text-center border-r border-gray-100">
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-bold border shadow-sm w-full
                                        <?= $row['status'] == 'verified' ? 'bg-green-100 text-green-700 border-green-200' : 
                                           ($row['status'] == 'rejected' ? 'bg-red-100 text-red-700 border-red-200' : 'bg-yellow-100 text-yellow-700 border-yellow-200') ?>">
                                        <?= $row['status'] == 'verified' ? 'Disetujui' : ($row['status'] == 'rejected' ? 'Ditolak' : 'Pending') ?>
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="edit_journal.php?id=<?= $row['id'] ?>" class="p-2 bg-blue-50 text-blue-600 rounded hover:bg-blue-600 hover:text-white transition shadow-sm" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </a>
                                        <a href="index.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus data jurnal ini?')" class="p-2 bg-red-50 text-red-600 rounded hover:bg-red-600 hover:text-white transition shadow-sm" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <span class="font-medium">Belum ada data jurnal</span>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-3 border-t border-gray-200 dark:border-gray-600 flex justify-between items-center">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Total: <?= count($jurnals) ?> Jurnal</span>
            </div>
        </div>

    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
</body>
</html>