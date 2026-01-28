<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'kepala_sekolah') {
    header("Location: ../../index.php");
    exit;
}

include '../../koneksi.php';

// --- QUERY STATISTIK ---
$q_guru = $conn->query("SELECT COUNT(*) as total FROM guru");
$total_guru = $q_guru->fetch_assoc()['total'];

$q_pending = $conn->query("SELECT COUNT(*) as total FROM jurnal_harian WHERE status = 'pending'");
$total_pending = $q_pending->fetch_assoc()['total'];

$today = date('Y-m-d');
$q_today = $conn->query("SELECT COUNT(*) as total FROM jurnal_harian WHERE tanggal = '$today'");
$total_today = $q_today->fetch_assoc()['total'];

// --- QUERY DAFTAR MENUNGGU VERIFIKASI ---
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
    <title>Dashboard Kepala Sekolah - Modern</title>
    <link href="../../src/output.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans antialiased text-gray-800">

    <?php include ("../partials/navbar.php") ?>
    <?php include ("../partials/sidebar_kepala_sekolah.php") ?>

    <main class="p-4 md:ml-64 h-auto pt-24">
        
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Pantau kinerja guru dan verifikasi jurnal harian.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="relative overflow-hidden bg-gradient-to-br from-yellow-400 to-orange-500 rounded-2xl p-6 shadow-lg shadow-orange-500/20 text-white transform hover:-translate-y-1 transition-transform duration-300">
                <div class="relative z-10">
                    <p class="text-white/90 text-sm font-semibold uppercase tracking-wider mb-1">Perlu Verifikasi</p>
                    <div class="flex items-end gap-2">
                        <span class="text-4xl font-bold"><?= $total_pending ?></span>
                        <span class="text-lg mb-1 opacity-80">Jurnal</span>
                    </div>
                    <?php if($total_pending > 0): ?>
                        <a href="kelola_jurnal/index.php" class="inline-block mt-4 bg-white/20 hover:bg-white/30 backdrop-blur-sm py-2 px-4 rounded-lg text-sm font-medium transition-colors">
                            Proses Sekarang &rarr;
                        </a>
                    <?php else: ?>
                        <div class="mt-4 text-sm opacity-80">Semua aman terkendali! 👌</div>
                    <?php endif; ?>
                </div>
                <div class="absolute right-0 bottom-0 opacity-20 transform translate-x-4 translate-y-4">
                     <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path></svg>
                </div>
            </div>

            <div class="relative overflow-hidden bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-md border border-gray-100 dark:border-gray-700 group hover:border-blue-500 transition-colors">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase">Masuk Hari Ini</p>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-2"><?= $total_today ?></h3>
                    </div>
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-xl text-blue-600 dark:text-blue-400 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm text-gray-500">
                    <span class="text-green-500 font-medium flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        Aktif
                    </span>
                    <span class="ml-2">Memperbarui secara real-time</span>
                </div>
            </div>

            <div class="relative overflow-hidden bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-md border border-gray-100 dark:border-gray-700 group hover:border-emerald-500 transition-colors">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase">Total Guru</p>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-2"><?= $total_guru ?></h3>
                    </div>
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl text-emerald-600 dark:text-emerald-400 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-4 text-sm text-gray-500">
                    Terdaftar dalam sistem
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 rounded-2xl p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Antrian Verifikasi</h3>
                        <p class="text-sm text-gray-500">Daftar jurnal yang menunggu persetujuan Anda.</p>
                    </div>
                    <a href="kelola_jurnal/index.php" class="text-sm font-medium text-blue-600 hover:text-blue-700 hover:underline">Lihat Semua</a>
                </div>

                <div class="relative overflow-x-auto rounded-xl">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 rounded-tl-lg">Guru</th>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3 rounded-tr-lg text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if($q_verif_list->num_rows > 0): ?>
                                <?php while($row = $q_verif_list->fetch_assoc()): ?>
                                <tr class="bg-white hover:bg-blue-50/50 dark:bg-gray-800 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-gray-900 dark:text-white"><?= htmlspecialchars($row['nama_guru']) ?></div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500"><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="kelola_jurnal/verify_journal.php?id=<?= $row['id'] ?>" class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 hover:bg-blue-600 hover:text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-all shadow-sm">
                                            Periksa
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-400">
                                        <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Tidak ada antrian saat ini.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 rounded-2xl p-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Statistik Masuk vs Pending</h3>
                <div class="relative w-full h-64">
                    <canvas id="kepsekChart"></canvas>
                </div>
            </div>
        </div>
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
    <script>
        const ctx = document.getElementById('kepsekChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Menunggu Verifikasi', 'Masuk Hari Ini'],
                datasets: [{
                    label: 'Jumlah Jurnal',
                    data: [<?= $total_pending ?>, <?= $total_today ?>],
                    backgroundColor: [
                        'rgba(251, 191, 36, 0.8)', // Yellow/Orange
                        'rgba(59, 130, 246, 0.8)'  // Blue
                    ],
                    borderColor: [
                        'rgba(251, 191, 36, 1)',
                        'rgba(59, 130, 246, 1)'
                    ],
                    borderWidth: 1,
                    borderRadius: 8,
                    barThickness: 50
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { 
                        beginAtZero: true,
                        grid: { borderDash: [2, 4], color: '#f3f4f6' }
                    },
                    x: {
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    </script>
</body>
</html>