<?php
session_start();
// Cek sesi dan role, hanya admin yang boleh akses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../index.php");
    exit;
}

include '../../../koneksi.php';

// --- LOGIKA FILTER GURU ---
$sql_guru = "SELECT * FROM guru ORDER BY nama ASC";
$result_guru = $conn->query($sql_guru);

$filter_guru = isset($_GET['id_guru']) ? $_GET['id_guru'] : '';

// Query dasar: Join tabel jurnal dan guru
$sql = "SELECT j.*, g.nama AS nama_guru 
        FROM jurnal_harian j 
        JOIN guru g ON j.id_guru = g.id";

// Jika ada filter guru yang dipilih
if (!empty($filter_guru)) {
    $sql .= " WHERE j.id_guru = " . intval($filter_guru);
}

// Urutkan berdasarkan tanggal terbaru
$sql .= " ORDER BY j.tanggal DESC, j.jam_ke ASC";

$result = $conn->query($sql);

// Simpan data ke array agar bisa diloop 2 kali (Mobile & Desktop)
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
    <title>Lihat Jurnal Guru - Admin</title>
    <link href="../../../src/output.css" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans antialiased">
    
    <div class="antialiased">
        <?php include ("../../partials/navbar.php") ?>
        <?php include ("../../partials/sidebar_admin.php") ?>

        <main class="p-4 md:ml-64 h-auto pt-20">
            
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Lihat Jurnal Guru</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Pantau aktivitas mengajar guru-guru.</p>
            </div>

            <div class="p-4 mb-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <form action="" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="w-full md:w-1/3">
                        <label for="id_guru" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Filter Nama Guru</label>
                        <select id="id_guru" name="id_guru" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                            <option value="">-- Tampilkan Semua Guru --</option>
                            <?php 
                            if ($result_guru->num_rows > 0) {
                                mysqli_data_seek($result_guru, 0);
                                while ($guru = $result_guru->fetch_assoc()): 
                            ?>
                                <option value="<?= $guru['id']; ?>" <?= ($filter_guru == $guru['id']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($guru['nama']); ?>
                                </option>
                            <?php endwhile; } ?>
                        </select>
                    </div>
                    <div class="w-full md:w-auto">
                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 flex items-center gap-2">
                            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                            </svg>
                            Tampilkan
                        </button>
                    </div>
                </form>
            </div>

            <div class="block md:hidden space-y-4">
                <?php if (count($jurnals) > 0): ?>
                    <?php foreach($jurnals as $row): ?>
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-300 dark:border-gray-600 overflow-hidden">
                        
                        <div class="px-4 py-3 bg-blue-700 dark:bg-blue-900 flex justify-between items-center border-b border-blue-800 dark:border-blue-950">
                            <div class="flex items-center gap-2 text-white font-medium text-sm">
                                <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <?= date('d M Y', strtotime($row['tanggal'])) ?>
                            </div>
                            
                            <span class="px-2.5 py-1 rounded text-xs font-bold
                                <?= $row['status'] == 'verified' ? 'font-bold text-gray-900 dark:text-white text-base leading-tight' : 
                                   ($row['status'] == 'rejected' ? 'font-bold text-gray-900 dark:text-white text-base leading-tight' : '') ?>">
                                <?= ucfirst($row['status']) ?>
                            </span>
                        </div>

                        <div class="p-4 space-y-4">
                            
                            <div class="flex items-center gap-3 pb-3 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-900/50 flex items-center justify-center text-blue-700 dark:text-blue-300 border border-blue-100 dark:border-blue-800">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Guru Pengajar</p>
                                    <h4 class="font-bold text-gray-900 dark:text-white text-base leading-tight"><?= htmlspecialchars($row['nama_guru']) ?></h4>
                                </div>
                            </div>

                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($row['mata_pelajaran']) ?></h3>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600">
                                            Kelas <?= htmlspecialchars($row['kelas']) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="bg-blue-50 text-blue-700 text-xs font-bold px-2.5 py-1 rounded dark:bg-blue-900/50 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                        Jam Ke-<?= htmlspecialchars($row['jam_ke']) ?>
                                    </span>
                                </div>
                            </div>

                            <div class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                <span class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1">Materi / Kegiatan:</span>
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                    <?= nl2br(htmlspecialchars(substr($row['isi_jurnal'], 0, 150))) . (strlen($row['isi_jurnal']) > 150 ? '...' : '') ?>
                                </p>
                            </div>

                            <div>
                                <span class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-2">Rekap Absensi:</span>
                                <div class="grid grid-cols-4 gap-2">
                                    <div class="flex flex-col items-center p-2 rounded bg-green-50 border border-green-200 dark:bg-green-900/20 dark:border-green-800">
                                        <span class="text-xs text-green-600 dark:text-green-400 font-medium">Hadir</span>
                                        <span class="text-lg font-bold text-green-700 dark:text-green-300"><?= $row['hadir'] ?></span>
                                    </div>
                                    <div class="flex flex-col items-center p-2 rounded bg-yellow-50 border border-yellow-200 dark:bg-yellow-900/20 dark:border-yellow-800">
                                        <span class="text-xs text-yellow-600 dark:text-yellow-400 font-medium">Sakit</span>
                                        <span class="text-lg font-bold text-yellow-700 dark:text-yellow-300"><?= $row['sakit'] ?></span>
                                    </div>
                                    <div class="flex flex-col items-center p-2 rounded bg-blue-50 border border-blue-200 dark:bg-blue-900/20 dark:border-blue-800">
                                        <span class="text-xs text-blue-600 dark:text-blue-400 font-medium">Izin</span>
                                        <span class="text-lg font-bold text-blue-700 dark:text-blue-300"><?= $row['izin'] ?></span>
                                    </div>
                                    <div class="flex flex-col items-center p-2 rounded bg-red-50 border border-red-200 dark:bg-red-900/20 dark:border-red-800">
                                        <span class="text-xs text-red-600 dark:text-red-400 font-medium">Alpa</span>
                                        <span class="text-lg font-bold text-red-700 dark:text-red-300"><?= $row['alpa'] ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow text-center border border-gray-300 dark:border-gray-700">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-12 h-12 mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <p class="text-gray-500 dark:text-gray-400">Tidak ada data jurnal ditemukan.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="hidden md:block bg-white shadow-lg rounded-lg overflow-hidden dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400 border-b dark:border-gray-600">
                            <tr>
                                <th scope="col" class="px-6 py-4">No</th>
                                <th scope="col" class="px-6 py-4">Tanggal & Jam</th>
                                <th scope="col" class="px-6 py-4">Guru</th>
                                <th scope="col" class="px-6 py-4">Kelas & Mapel</th>
                                <th scope="col" class="px-6 py-4 w-1/4">Materi</th>
                                <th scope="col" class="px-6 py-4 text-center">Absensi (H/S/I/A)</th>
                                <th scope="col" class="px-6 py-4">Status</th>
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
                                        <span class="text-xs text-blue-600 bg-blue-100 px-2 py-0.5 rounded-full dark:bg-blue-900 dark:text-blue-300 mt-1 inline-block border border-blue-200 dark:border-blue-800">
                                            Jam Ke-<?= htmlspecialchars($row['jam_ke']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-800 dark:text-white"><?= htmlspecialchars($row['nama_guru']) ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-800 dark:text-white"><?= htmlspecialchars($row['mata_pelajaran']) ?></div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kelas: <span class="font-medium text-gray-900 dark:text-gray-200"><?= htmlspecialchars($row['kelas']) ?></span></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-gray-600 dark:text-gray-300 line-clamp-2 leading-relaxed" title="<?= htmlspecialchars($row['isi_jurnal']) ?>">
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
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border
                                            <?= $row['status'] == 'verified' ? 'bg-green-100 text-green-800 border-green-200 dark:bg-green-900 dark:text-green-300 dark:border-green-800' : 
                                               ($row['status'] == 'rejected' ? 'bg-red-100 text-red-800 border-red-200 dark:bg-red-900 dark:text-red-300 dark:border-red-800' : 'bg-yellow-100 text-yellow-800 border-yellow-200 dark:bg-yellow-900 dark:text-yellow-300 dark:border-yellow-800') ?>">
                                            <span class="w-2 h-2 mr-1 rounded-full 
                                                <?= $row['status'] == 'verified' ? 'bg-green-500' : 
                                                   ($row['status'] == 'rejected' ? 'bg-red-500' : 'bg-yellow-500') ?>"></span>
                                            <?= ucfirst($row['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            <p class="text-lg font-medium">Data tidak ditemukan.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
</body>
</html>