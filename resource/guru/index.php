<?php
session_start();
// Cek sesi & role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    header("Location: ../../index.php");
    exit;
}

include '../../koneksi.php';

$id_guru = $_SESSION['user_id']; 

// --- QUERY DATA STATISTIK ---
$bulan_ini = date('Y-m');
$q_total = $conn->query("SELECT COUNT(*) as total FROM jurnal_harian WHERE id_guru = '$id_guru' AND tanggal LIKE '$bulan_ini%'");
$total_bulan_ini = $q_total->fetch_assoc()['total'];

$q_reject = $conn->query("SELECT COUNT(*) as total FROM jurnal_harian WHERE id_guru = '$id_guru' AND status = 'rejected'");
$total_rejected = $q_reject->fetch_assoc()['total'];

$q_pending = $conn->query("SELECT COUNT(*) as total FROM jurnal_harian WHERE id_guru = '$id_guru' AND status = 'pending'");
$total_pending = $q_pending->fetch_assoc()['total'];

$q_verified = $conn->query("SELECT COUNT(*) as total FROM jurnal_harian WHERE id_guru = '$id_guru' AND status = 'verified'");
$total_verified = $q_verified->fetch_assoc()['total'];

$q_recent = $conn->query("SELECT * FROM jurnal_harian WHERE id_guru = '$id_guru' ORDER BY tanggal DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard SI Jurnal</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans antialiased text-gray-800">

    <?php include ("../partials/navbar.php") ?>
    <?php include ("../partials/sidebar_guru.php") ?>

    <main class="p-4 md:ml-64 h-auto pt-24">
        
        <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-8 mb-8 shadow-xl text-white">
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Halo, <?= htmlspecialchars($_SESSION['nama']); ?>! 👋</h1>
                    <p class="text-blue-100 text-lg">Siap mencatat kemajuan siswa hari ini?</p>
                </div>
                <a href="kelola_jurnal/add_journal.php" class="group flex items-center gap-2 bg-white text-blue-700 hover:bg-blue-50 font-bold py-3 px-6 rounded-xl shadow-lg transition-all transform hover:scale-105">
                    <span>+ Buat Jurnal Baru</span>
                    <svg class="w-5 h-5 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </a>
            </div>
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 rounded-full bg-white opacity-10 blur-2xl"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-40 h-40 rounded-full bg-blue-400 opacity-20 blur-xl"></div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm hover:shadow-lg transition-shadow border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-20 h-20 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path></svg>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Bulan Ini</p>
                <p class="text-4xl font-bold text-gray-800 dark:text-white mt-2"><?= $total_bulan_ini ?></p>
                <div class="mt-4 h-1 w-full bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-500 w-full"></div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm hover:shadow-lg transition-shadow border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                     <svg class="w-20 h-20 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path></svg>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Menunggu Verifikasi</p>
                <p class="text-4xl font-bold text-yellow-500 mt-2"><?= $total_pending ?></p>
                <p class="text-xs text-gray-400 mt-1">Menanti persetujuan KS</p>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm hover:shadow-lg transition-shadow border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-20 h-20 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sudah Diverifikasi</p>
                <p class="text-4xl font-bold text-green-500 mt-2"><?= $total_verified ?></p>
                <p class="text-xs text-gray-400 mt-1">Jurnal diterima</p>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm hover:shadow-lg transition-shadow border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-20 h-20 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Perlu Revisi</p>
                <p class="text-4xl font-bold text-red-500 mt-2"><?= $total_rejected ?></p>
                <p class="text-xs text-gray-400 mt-1">Harap periksa kembali</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6 flex items-center gap-2">
                    <span class="w-2 h-8 bg-blue-500 rounded-full"></span>
                    Statistik Jurnal
                </h3>
                <div class="relative w-full h-64">
                    <canvas id="guruChart"></canvas>
                </div>
            </div>

            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <span class="w-2 h-8 bg-purple-500 rounded-full"></span>
                        Aktivitas Terakhir
                    </h3>
                    <a href="kelola_jurnal/index.php" class="text-sm font-medium text-blue-600 hover:text-blue-800 hover:underline">Lihat Semua &rarr;</a>
                </div>
                
                <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-700">
                    <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-4 font-semibold">Tanggal</th>
                                <th class="px-6 py-4 font-semibold">Mata Pelajaran</th>
                                <th class="px-6 py-4 font-semibold text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <?php while($row = $q_recent->fetch_assoc()): ?>
                            <tr class="bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    <?= date('d M Y', strtotime($row['tanggal'])) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= htmlspecialchars($row['mata_pelajaran'] ?? '-') ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php
                                        $statusClass = '';
                                        $statusText = '';
                                        if($row['status'] == 'verified') {
                                            $statusClass = 'bg-green-100 text-green-700 border border-green-200';
                                            $statusText = 'Diverifikasi';
                                        } elseif($row['status'] == 'rejected') {
                                            $statusClass = 'bg-red-100 text-red-700 border border-red-200';
                                            $statusText = 'Revisi';
                                        } else {
                                            $statusClass = 'bg-yellow-100 text-yellow-700 border border-yellow-200';
                                            $statusText = 'Pending';
                                        }
                                    ?>
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $statusClass ?>">
                                        <?= $statusText ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
    <script>
    const ctx = document.getElementById('guruChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Verified', 'Revisi'],
            datasets: [{
                data: [<?= $total_pending ?>, <?= $total_verified ?>, <?= $total_rejected ?>],
                backgroundColor: ['#FBBF24', '#34D399', '#F87171'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                }
            }
        }
    });
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
</body>
</html>