<?php
session_start();
// Cek sesi & role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    header("Location: ../../index.php");
    exit;
}

include '../../koneksi.php';

$id_guru = $_SESSION['user_id']; // Pastikan saat login user_id disimpan di session

// --- QUERY DATA STATISTIK (Khusus Guru Ini) ---

// 1. Total Jurnal Bulan Ini
$bulan_ini = date('Y-m');
$q_total = $conn->query("SELECT COUNT(*) as total FROM jurnal_harian WHERE id_guru = '$id_guru' AND tanggal LIKE '$bulan_ini%'");
$total_bulan_ini = $q_total->fetch_assoc()['total'];

// 2. Jurnal Ditolak (Perlu Perhatian)
$q_reject = $conn->query("SELECT COUNT(*) as total FROM jurnal_harian WHERE id_guru = '$id_guru' AND status = 'rejected'");
$total_rejected = $q_reject->fetch_assoc()['total'];

// 3. Jurnal Menunggu Verifikasi
$q_pending = $conn->query("SELECT COUNT(*) as total FROM jurnal_harian WHERE id_guru = '$id_guru' AND status = 'pending'");
$total_pending = $q_pending->fetch_assoc()['total'];

// 4. Jurnal Terverifikasi
$q_verified = $conn->query("SELECT COUNT(*) as total FROM jurnal_harian WHERE id_guru = '$id_guru' AND status = 'verified'");
$total_verified = $q_verified->fetch_assoc()['total'];

// --- QUERY RIWAYAT TERAKHIR ---
$q_recent = $conn->query("SELECT * FROM jurnal_harian WHERE id_guru = '$id_guru' ORDER BY tanggal DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru</title>
    <link href="../../src/output.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans antialiased">

    <?php include ("../partials/navbar.php") ?>
    <?php include ("../partials/sidebar_guru.php") ?>

    <main class="p-4 md:ml-64 h-auto pt-20">
        
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard Guru</h1>
                <p class="text-gray-600 dark:text-gray-400">Halo, <?= htmlspecialchars($_SESSION['nama']); ?>. Semangat mengajar!</p>
            </div>
            <a href="kelola_jurnal/add_journal.php" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                + Tambah Jurnal
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="p-4 bg-white rounded-lg shadow dark:bg-gray-800 border-l-4 border-blue-500">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Bulan Ini</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $total_bulan_ini ?></p>
            </div>
            <div class="p-4 bg-white rounded-lg shadow dark:bg-gray-800 border-l-4 border-yellow-400">
                <p class="text-sm text-gray-500 dark:text-gray-400">Menunggu Verifikasi</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $total_pending ?></p>
            </div>
            <div class="p-4 bg-white rounded-lg shadow dark:bg-gray-800 border-l-4 border-green-500">
                <p class="text-sm text-gray-500 dark:text-gray-400">Sudah Diverifikasi</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $total_verified ?></p>
            </div>
            <div class="p-4 bg-white rounded-lg shadow dark:bg-gray-800 border-l-4 border-red-500">
                <p class="text-sm text-gray-500 dark:text-gray-400">Perlu Revisi</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $total_rejected ?></p>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-4 dark:bg-gray-800">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Status Jurnal Anda</h3>
    
    <div style="height: 300px; position: relative; width: 100%;">
        <canvas id="guruChart"></canvas>
    </div>
</div>
</div>

            <div class="lg:col-span-2 bg-white shadow rounded-lg p-4 dark:bg-gray-800">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">5 Jurnal Terakhir</h3>
                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3">Mapel</th>
                                <th class="px-4 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $q_recent->fetch_assoc()): ?>
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-4 py-3"><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                                <td class="px-4 py-3"><?= htmlspecialchars($row['mata_pelajaran'] ?? '-') ?></td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded text-xs font-medium 
                                        <?= $row['status'] == 'verified' ? 'bg-green-100 text-green-800' : 
                                           ($row['status'] == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') ?>">
                                        <?= ucfirst($row['status']) ?>
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
                backgroundColor: ['#FCD34D', '#34D399', '#F87171']
            }]
        },
        options: {
            responsive: true,           // <--- PASTIKAN ADA KOMA DI SINI
            maintainAspectRatio: false, // Opsi tambahan
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
</script>
</script>
    </script>
</body>
</html>