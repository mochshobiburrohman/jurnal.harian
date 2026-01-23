<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'kepala_sekolah') {
    header("Location: ../../index.php");
    exit;
}

include '../../koneksi.php';

// --- QUERY STATISTIK UTAMA ---

// 1. Total Guru Aktif
$q_guru = $conn->query("SELECT COUNT(*) as total FROM guru");
$total_guru = $q_guru->fetch_assoc()['total'];

// 2. Jurnal Belum Diverifikasi (PRIORITAS UTAMA KEPSEK)
$q_pending = $conn->query("SELECT COUNT(*) as total FROM jurnal_harian WHERE status = 'pending'");
$total_pending = $q_pending->fetch_assoc()['total'];

// 3. Jurnal Masuk Hari Ini
$today = date('Y-m-d');
$q_today = $conn->query("SELECT COUNT(*) as total FROM jurnal_harian WHERE tanggal = '$today'");
$total_today = $q_today->fetch_assoc()['total'];

// --- QUERY DAFTAR MENUNGGU VERIFIKASI (Limit 5) ---
$sql_verif = "SELECT j.*, g.nama as nama_guru 
              FROM jurnal_harian j 
              JOIN guru g ON j.id_guru = g.id 
              WHERE j.status = 'pending' 
              ORDER BY j.tanggal ASC LIMIT 5";
$q_verif_list = $conn->query($sql_verif);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kepala Sekolah</title>
    <link href="../../src/output.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans antialiased">

    <?php include ("../partials/navbar.php") ?>
    <?php include ("../partials/sidebar_kepala_sekolah.php") ?>

    <main class="p-4 md:ml-64 h-auto pt-20">
        
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard Kepala Sekolah</h1>
            <p class="text-gray-600 dark:text-gray-400">Selamat datang. Berikut ringkasan aktivitas jurnal guru.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="flex items-center p-4 bg-white rounded-lg shadow dark:bg-gray-800 border-l-4 border-yellow-400">
                <div class="p-3 mr-4 text-white bg-yellow-500 rounded-full dark:text-white dark:bg-yellow-600">
                   <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path></svg>
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">Perlu Verifikasi</p>
                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-200"><?= $total_pending ?> Jurnal</p>
                </div>
            </div>

            <div class="flex items-center p-4 bg-white rounded-lg shadow dark:bg-gray-800 border-l-4 border-blue-500">
                <div class="p-3 mr-4 text-white bg-blue-500 rounded-full dark:text-white dark:bg-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">Jurnal Masuk Hari Ini</p>
                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-200"><?= $total_today ?> Jurnal</p>
                </div>
            </div>

            <div class="flex items-center p-4 bg-white rounded-lg shadow dark:bg-gray-800 border-l-4 border-green-500">
                <div class="p-3 mr-4 text-white bg-green-500 rounded-full dark:text-white dark:bg-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">Total Guru</p>
                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-200"><?= $total_guru ?> Guru</p>
                </div>
            </div>
        </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white shadow rounded-lg p-6 dark:bg-gray-800">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Menunggu Verifikasi</h3>
                    <a href="kelola_jurnal/index.php" class="text-sm text-white hover:underline">Lihat Semua</a>
                </div>
                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3">Guru</th>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($q_verif_list->num_rows > 0): ?>
                                <?php while($row = $q_verif_list->fetch_assoc()): ?>
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                        <?= htmlspecialchars($row['nama_guru']) ?>
                                    </td>
                                    <td class="px-4 py-3"><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                                    <td class="px-4 py-3">
                                        <a href="kelola_jurnal/verify_journal.php?id=<?= $row['id'] ?>" class="text-blue-600 hover:underline">Periksa</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="px-4 py-3 text-center">Tidak ada antrian verifikasi.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6 dark:bg-gray-800">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Statistik Harian</h3>
                <canvas id="kepsekChart"></canvas>
            </div>
        </div>

    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
    <script>
        // Chart Statistik Sederhana
        const ctx = document.getElementById('kepsekChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Menunggu Verifikasi', 'Sudah Masuk Hari Ini'],
                datasets: [{
                    label: 'Jumlah Jurnal',
                    data: [<?= $total_pending ?>, <?= $total_today ?>],
                    backgroundColor: ['#FBBF24', '#3B82F6'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</body>
</html>