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

// Cek jika error query
if (!$recent_journals) {
    die("Error Query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SI Jurnal</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.css" rel="stylesheet" />
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans antialiased">

    <?php include ("../partials/navbar.php") ?>

    <?php include ("../partials/sidebar_admin.php") ?>

    <main class="p-4 md:ml-64 h-auto pt-20">
        
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard Admin</h1>
            <p class="text-gray-600 dark:text-gray-400">Selamat datang kembali, <span class="font-semibold text-blue-600"><?= htmlspecialchars($_SESSION['nama']); ?></span>.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            
            <div class="relative overflow-hidden bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl shadow-lg p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl group">
                <div class="absolute right-0 top-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-white opacity-10 blur-xl group-hover:opacity-20 transition duration-500"></div>
                <div class="flex items-center relative z-10">
                    <div class="p-3 rounded-lg bg-white/20 backdrop-blur-sm text-white">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div class="ml-4 text-white">
                        <p class="text-sm font-medium opacity-80">Total Guru</p>
                        <p class="text-3xl font-bold"><?= $total_guru ?></p>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl shadow-lg p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl group">
                <div class="absolute right-0 top-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-white opacity-10 blur-xl group-hover:opacity-20 transition duration-500"></div>
                <div class="flex items-center relative z-10">
                    <div class="p-3 rounded-lg bg-white/20 backdrop-blur-sm text-white">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div class="ml-4 text-white">
                        <p class="text-sm font-medium opacity-80">Jurnal Hari Ini</p>
                        <p class="text-3xl font-bold"><?= $total_today ?></p>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden bg-gradient-to-br from-orange-400 to-red-500 rounded-xl shadow-lg p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl group">
                <div class="absolute right-0 top-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-white opacity-10 blur-xl group-hover:opacity-20 transition duration-500"></div>
                <div class="flex items-center relative z-10">
                    <div class="p-3 rounded-lg bg-white/20 backdrop-blur-sm text-white">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="ml-4 text-white">
                        <p class="text-sm font-medium opacity-80">Pending</p>
                        <p class="text-3xl font-bold"><?= $total_pending ?></p>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-lg p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl group">
                <div class="absolute right-0 top-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-white opacity-10 blur-xl group-hover:opacity-20 transition duration-500"></div>
                <div class="flex items-center relative z-10">
                    <div class="p-3 rounded-lg bg-white/20 backdrop-blur-sm text-white">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="ml-4 text-white">
                        <p class="text-sm font-medium opacity-80">Terverifikasi</p>
                        <p class="text-3xl font-bold"><?= $total_verified ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            
            <div class="bg-white shadow-md rounded-lg p-4 dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Statistik Status Jurnal</h3>
                <div class="h-64 relative flex justify-center">
                    <canvas id="jurnalChart"></canvas>
                </div>
            </div>

            <div class="lg:col-span-2 bg-white shadow-md rounded-lg p-4 dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Jurnal Terbaru Masuk</h3>
                    <a href="kelola_jurnal/index.php" class="text-sm text-blue-600 hover:underline dark:text-blue-400">Lihat Semua</a>
                </div>
                
                <div class="overflow-x-auto rounded-lg">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">Guru</th>
                                <th class="px-6 py-3">Tanggal</th>
                                <th class="px-6 py-3">Mapel</th>
                                <th class="px-6 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recent_journals->num_rows > 0): ?>
                                <?php while($row = $recent_journals->fetch_assoc()): ?>
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold uppercase">
                                            <?= substr($row['nama_guru'], 0, 1) ?>
                                        </div>
                                        <?= htmlspecialchars($row['nama_guru']) ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?= date('d M Y', strtotime($row['tanggal'])) ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?= htmlspecialchars($row['mata_pelajaran'] ?? '-') ?>
                                    </td>
                                    <td class="px-6 py-4">
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
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-400">
                                        Belum ada data jurnal.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
    
    <script>
        const ctx = document.getElementById('jurnalChart').getContext('2d');
        const jurnalChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Verified', 'Rejected'],
                datasets: [{
                    label: 'Jumlah Jurnal',
                    data: [
                        <?= $total_pending ?>, 
                        <?= $total_verified ?>, 
                        0 // Ganti dengan variabel rejected jika nanti sudah ada
                    ],
                    backgroundColor: [
                        '#FCD34D', // Kuning
                        '#34D399', // Hijau
                        '#F87171'  // Merah
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    }
                },
                cutout: '70%' // Membuat lubang tengah donat lebih besar
            }
        });
    </script>
</body>
</html>