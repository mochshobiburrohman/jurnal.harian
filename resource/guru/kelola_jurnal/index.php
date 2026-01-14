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

// Simpan data ke array agar bisa diloop 2 kali (untuk tampilan Mobile & Desktop)
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
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans antialiased">

    <?php include ("../../partials/navbar.php") ?>
    <?php include ("../../partials/sidebar_guru.php") ?>

    <main class="p-4 md:ml-64 h-auto pt-20">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kelola Jurnal Harian</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Pantau dan kelola aktivitas mengajar Anda.</p>
            </div>
            <a href="add_journal.php" class="w-full sm:w-auto text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transition-all">
                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16"/>
                </svg>
                Tambah Jurnal
            </a>
        </div>

        <div class="block md:hidden space-y-4">
            <?php if (count($jurnals) > 0): ?>
                <?php foreach($jurnals as $row): ?>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 overflow-hidden">
                    
                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 flex justify-between items-center border-b dark:border-gray-600">
                        <div class="flex items-center gap-2 text-gray-700 dark:text-gray-200 font-medium text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <?= date('d M Y', strtotime($row['tanggal'])) ?>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                            <?= $row['status'] == 'verified' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 
                               ($row['status'] == 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300') ?>">
                            <?= ucfirst($row['status']) ?>
                        </span>
                    </div>

                    <div class="p-4 space-y-3">
                        
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($row['mata_pelajaran']) ?></h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Kelas: <span class="font-medium text-gray-800 dark:text-gray-200"><?= htmlspecialchars($row['kelas']) ?></span></p>
                            </div>
                            <div class="text-right">
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300 border border-blue-400">
                                    Jam: <?= htmlspecialchars($row['jam_ke']) ?>
                                </span>
                            </div>
                        </div>

                        <div class="text-sm text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 p-2 rounded border border-dashed border-gray-300 dark:border-gray-600">
                            <span class="font-semibold text-xs text-gray-400 uppercase tracking-wide">Materi:</span><br>
                            <?= htmlspecialchars(substr($row['isi_jurnal'], 0, 50)) . (strlen($row['isi_jurnal']) > 50 ? '...' : '') ?>
                        </div>

                        <div>
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Absensi Siswa:</span>
                            <div class="grid grid-cols-4 gap-2 mt-1 text-center">
                                <div class="bg-green-50 dark:bg-green-900/30 rounded p-1 border border-green-200 dark:border-green-800">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Hadir</div>
                                    <div class="font-bold text-green-700 dark:text-green-400"><?= $row['hadir'] ?></div>
                                </div>
                                <div class="bg-yellow-50 dark:bg-yellow-900/30 rounded p-1 border border-yellow-200 dark:border-yellow-800">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Sakit</div>
                                    <div class="font-bold text-yellow-700 dark:text-yellow-400"><?= $row['sakit'] ?></div>
                                </div>
                                <div class="bg-blue-50 dark:bg-blue-900/30 rounded p-1 border border-blue-200 dark:border-blue-800">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Izin</div>
                                    <div class="font-bold text-blue-700 dark:text-blue-400"><?= $row['izin'] ?></div>
                                </div>
                                <div class="bg-red-50 dark:bg-red-900/30 rounded p-1 border border-red-200 dark:border-red-800">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Alpa</div>
                                    <div class="font-bold text-red-700 dark:text-red-400"><?= $row['alpa'] ?></div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="flex border-t border-gray-100 dark:border-gray-700 divide-x divide-gray-100 dark:divide-gray-700">
                        <a href="edit_journal.php?id=<?= $row['id'] ?>" class="flex-1 py-3 text-sm font-medium text-blue-600 hover:bg-blue-50 dark:hover:bg-gray-700 dark:text-blue-400 text-center transition">
                            Edit
                        </a>
                        <a href="index.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus?')" class="flex-1 py-3 text-sm font-medium text-red-600 hover:bg-red-50 dark:hover:bg-gray-700 dark:text-red-400 text-center transition">
                            Hapus
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow text-center">
                    <p class="text-gray-500">Belum ada jurnal.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="hidden md:block bg-white shadow-lg rounded-lg overflow-hidden dark:bg-gray-800">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400 border-b dark:border-gray-600">
                        <tr>
                            <th scope="col" class="px-6 py-4">No</th>
                            <th scope="col" class="px-6 py-4">Tanggal & Jam</th>
                            <th scope="col" class="px-6 py-4">Kelas & Mapel</th>
                            <th scope="col" class="px-6 py-4 w-1/4">Materi</th>
                            <th scope="col" class="px-6 py-4 text-center">Absensi (H/S/I/A)</th>
                            <th scope="col" class="px-6 py-4">Status</th>
                            <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <?php if (count($jurnals) > 0): ?>
                            <?php $no = 1; foreach($jurnals as $row): ?>
                            <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white text-center"><?= $no++ ?></td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                                        <?= date('d M Y', strtotime($row['tanggal'])) ?>
                                    </div>
                                    <span class="text-xs text-blue-600 bg-blue-100 px-2 py-0.5 rounded-full dark:bg-blue-900 dark:text-blue-300 mt-1 inline-block">
                                        Jam Ke-<?= htmlspecialchars($row['jam_ke']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-800 dark:text-white"><?= htmlspecialchars($row['mata_pelajaran']) ?></div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Kelas: <?= htmlspecialchars($row['kelas']) ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-gray-600 dark:text-gray-300 line-clamp-2" title="<?= htmlspecialchars($row['isi_jurnal']) ?>">
                                        <?= htmlspecialchars($row['isi_jurnal']) ?>
                                    </p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex shadow-sm rounded-md" role="group">
                                        <button type="button" class="px-2 py-1 text-xs font-medium text-green-700 bg-green-50 border border-green-200 rounded-l-lg dark:bg-green-900 dark:border-green-800 dark:text-green-400" title="Hadir">
                                            H: <?= $row['hadir'] ?>
                                        </button>
                                        <button type="button" class="px-2 py-1 text-xs font-medium text-yellow-700 bg-yellow-50 border-t border-b border-yellow-200 dark:bg-yellow-900 dark:border-yellow-800 dark:text-yellow-400" title="Sakit">
                                            S: <?= $row['sakit'] ?>
                                        </button>
                                        <button type="button" class="px-2 py-1 text-xs font-medium text-blue-700 bg-blue-50 border-t border-b border-blue-200 dark:bg-blue-900 dark:border-blue-800 dark:text-blue-400" title="Izin">
                                            I: <?= $row['izin'] ?>
                                        </button>
                                        <button type="button" class="px-2 py-1 text-xs font-medium text-red-700 bg-red-50 border border-red-200 rounded-r-lg dark:bg-red-900 dark:border-red-800 dark:text-red-400" title="Alpa">
                                            A: <?= $row['alpa'] ?>
                                        </button>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        <?= $row['status'] == 'verified' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 
                                           ($row['status'] == 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300') ?>">
                                        <span class="w-2 h-2 mr-1 rounded-full 
                                            <?= $row['status'] == 'verified' ? 'bg-green-500' : 
                                               ($row['status'] == 'rejected' ? 'bg-red-500' : 'bg-yellow-500') ?>"></span>
                                        <?= ucfirst($row['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-3">
                                        <a href="edit_journal.php?id=<?= $row['id'] ?>" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-white transition" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <a href="index.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus jurnal ini?')" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-white transition" title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <p class="text-lg font-medium">Belum ada jurnal.</p>
                                        <p class="text-sm">Mulai tambahkan jurnal harian Anda sekarang.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
</body>
</html>