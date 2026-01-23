<?php
session_start();
// Cek sesi dan role, hanya kepala sekolah yang boleh akses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'kepala_sekolah') {
    header("Location: ../../../index.php");
    exit;
}

include '../../../koneksi.php';

// Query tetap sama (SELECT * akan otomatis mengambil kolom kelas & mata_pelajaran yang baru ditambahkan)
$sql = "SELECT j.*, g.nama AS nama_guru 
        FROM jurnal_harian j 
        JOIN guru g ON j.id_guru = g.id 
        ORDER BY j.tanggal DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Jurnal - Kepala Sekolah</title>
    <link href="../../../src/output.css" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    
    <div class="antialiased">
        <?php include ("../../partials/navbar.php") ?>
        <?php include ("../../partials/sidebar_kepala_sekolah.php") ?>

        <main class="p-4 md:ml-64 h-auto pt-20">
            <div class="mb-4">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">
                    Verifikasi Jurnal Guru
                </h1>
            </div>

            <div class="flex flex-col">
                <div class="overflow-x-auto">
                    <div class="inline-block w-full align-middle">
                        <div class="overflow-hidden shadow rounded-lg">
                            <table class="w-full divide-y divide-gray-200 dark:divide-gray-600">
                                <thead class="bg-gray-100 dark:bg-gray-700">
                                    <tr>
                                        <th class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Tanggal</th>
                                        <th class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Guru</th>
                                        
                                        <th class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Kelas</th>
                                        <th class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Mata Pelajaran</th>
                                        <th class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Isi Jurnal</th>
                                        <th class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Jumlah Hadir</th>
                                        <th class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Status</th>
                                        <th class="p-4 text-xs font-medium text-center text-gray-500 uppercase dark:text-gray-400">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <td class="p-4 text-sm font-medium text-gray-900 dark:text-white">
                                                <?= htmlspecialchars($row['tanggal']); ?>
                                            </td>
                                            <td class="p-4 text-sm text-gray-900 dark:text-white">
                                                <?= htmlspecialchars($row['nama_guru']); ?>
                                            </td>

                                            <td class="p-4 text-sm text-gray-900 dark:text-white">
                                                <?= htmlspecialchars($row['kelas'] ?? '-'); ?>
                                            </td>
                                            <td class="p-4 text-sm text-gray-900 dark:text-white">
                                                <?= htmlspecialchars($row['mata_pelajaran'] ?? '-'); ?>
                                            </td>
                                            <td class="p-4 text-sm text-gray-500 dark:text-gray-400">
                                                <?= htmlspecialchars(substr($row['isi_jurnal'], 0, 50)) . '...'; ?>
                                            </td>
                                            <td class="p-4 text-sm text-gray-500 dark:text-gray-400">
                                                 <?= $row['hadir'] ?>
                                            </td>
                                            <td class="p-4 text-sm text-gray-900 dark:text-white">
                                                <span class="px-2 py-1 rounded-full text-xs font-bold
                                                    <?php
                                                        if ($row['status'] == 'verified') echo 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
                                                        elseif ($row['status'] == 'rejected') echo 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
                                                        else echo 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
                                                    ?>">
                                                    <?= ucfirst($row['status']); ?>
                                                </span>
                                            </td>
                                            <td class="p-4 text-center">
                                                <a href="verify_journal.php?id=<?= $row['id']; ?>" 
                                                   class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-3 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                                    Verifikasi
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="p-4 text-center text-gray-500 dark:text-gray-400">
                                                Tidak ada data jurnal untuk diverifikasi.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
</body>
</html>