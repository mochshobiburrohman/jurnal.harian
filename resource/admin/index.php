<?php
session_start();
// Cek sesi dan role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../index.php");
    exit;
}

include '../../koneksi.php';

// --- BAGIAN 1: QUERY STATISTIK ---

// 1. Total Guru
$q_guru = $conn->query("SELECT COUNT(*) as total FROM guru");
$total_guru = $q_guru->fetch_assoc()['total'];

// 2. Jurnal Hari Ini
$today = date('Y-m-d');
$q_today = $conn->query("SELECT COUNT(*) as total FROM jurnal_harian WHERE tanggal = '$today'");
$total_today = $q_today->fetch_assoc()['total'];

// 3. Jurnal Belum Diverifikasi (Pending)
$q_pending = $conn->query("SELECT COUNT(*) as total FROM jurnal_harian WHERE status = 'pending'");
$total_pending = $q_pending->fetch_assoc()['total'];

// 4. Jurnal Terverifikasi
$q_verified = $conn->query("SELECT COUNT(*) as total FROM jurnal_harian WHERE status = 'verified'");
$total_verified = $q_verified->fetch_assoc()['total'];

// --- BAGIAN 2: QUERY AKTIVITAS TERBARU (5 Terakhir) ---
$sql_recent = "SELECT j.*, g.nama AS nama_guru 
               FROM jurnal_harian j 
               JOIN guru g ON j.id_guru = g.id 
               ORDER BY j.tanggal DESC 
               LIMIT 5";

$recent_journals = $conn->query($sql_recent);

// Cek jika error
if (!$recent_journals) {
    // Tampilkan error ke layar untuk diperbaiki
    echo "<h3>Terjadi Error pada Query Dashboard:</h3>";
    echo "<p>Pesan Error: " . $conn->error . "</p>";
    echo "<p>Cek apakah tabel 'jurnal_harian' dan 'guru' sudah benar.</p>";
    exit; // Hentikan script
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="../../src/output.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans antialiased">

    <?php include ("../partials/navbar.php") ?>

    <?php include ("../partials/sidebar_admin.php") ?>

    <main class="p-4 md:ml-64 h-auto pt-20">
        
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard Admin</h1>
            <p class="text-gray-600 dark:text-gray-400">Selamat datang kembali, <?= htmlspecialchars($_SESSION['nama']); ?>.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            
            <div class="bg-white rounded-lg shadow p-5 dark:bg-gray-800 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 p-3 rounded-full dark:bg-blue-900">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Guru</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $total_guru ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-5 dark:bg-gray-800 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 p-3 rounded-full dark:bg-green-900">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Jurnal Hari Ini</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $total_today ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-5 dark:bg-gray-800 border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 p-3 rounded-full dark:bg-yellow-900">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Perlu Verifikasi</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $total_pending ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-5 dark:bg-gray-800 border-l-4 border-indigo-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-100 p-3 rounded-full dark:bg-indigo-900">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sudah Diverifikasi</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $total_verified ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            
        <div class="bg-white shadow rounded-lg p-4 dark:bg-gray-800">
    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Statistik Status Jurnal</h3>
    <div class="h-64 relative flex justify-center">
        <canvas id="jurnalChart"></canvas>
    </div>
</div>

            <div class="lg:col-span-2 bg-white shadow rounded-lg p-4 dark:bg-gray-800">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Jurnal Terbaru Masuk</h3>
                    <a href="kelola_jurnal/index.php" class="text-sm text-blue-600 hover:underline dark:text-blue-400">Lihat Semua</a>
                </div>
                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3">Guru</th>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3">Mapel</th>
                                <th class="px-4 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recent_journals->num_rows > 0): ?>
                                <?php while($row = $recent_journals->fetch_assoc()): ?>
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                        <?= htmlspecialchars($row['nama_guru']) ?>
                                    </td>
                                    <td class="px-4 py-3">
                                        <?= date('d/m/Y', strtotime($row['tanggal'])) ?>
                                    </td>
                                    <td class="px-4 py-3">
                                        <?= htmlspecialchars($row['mata_pelajaran'] ?? '-') ?>
                                    </td>
                                    <td class="px-4 py-3">
                                        <?php if($row['status'] == 'verified'): ?>
                                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Verified</span>
                                        <?php elseif($row['status'] == 'rejected'): ?>
                                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Rejected</span>
                                        <?php else: ?>
                                            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-center">Belum ada data jurnal.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
    
    <script>
        const ctx = document.getElementById('jurnalChart').getContext('2d');
        const jurnalChart = new Chart(ctx, {
            type: 'doughnut', // Tipe chart: doughnut, pie, bar, dll
            data: {
                labels: ['Pending', 'Verified', 'Rejected'],
                datasets: [{
                    label: 'Jumlah Jurnal',
                    data: [
                        <?= $total_pending ?>, 
                        <?= $total_verified ?>, 
                        0 // Masukkan variabel rejected jika ada di database
                    ],
                    backgroundColor: [
                        '#FCD34D', // Kuning (Pending)
                        '#34D399', // Hijau (Verified)
                        '#F87171'  // Merah (Rejected)
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    </script>
</body>
</html>