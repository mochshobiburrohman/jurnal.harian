<?php
session_start();
// Cek sesi dan role, hanya kepala sekolah yang boleh akses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'kepala_sekolah') {
    header("Location: ../../../index.php");
    exit;
}

include '../../../koneksi.php';

// Ambil data semua guru yang terdaftar
$sql = "SELECT * FROM guru ORDER BY nama ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Guru - Kepala Sekolah</title>
    <link href="../../../src/output.css" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    
    <div class="antialiased">
        <?php include ("../../partials/navbar.php") ?>
        
        <?php include ("../../partials/sidebar_kepala_sekolah.php") ?>

        <main class="p-4 md:ml-64 h-auto pt-20">
        <div class="mb-4">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">
                    Daftar Guru
                </h1>
            </div>

            <div class="flex flex-col">
                <div class="overflow-x-auto">
                    <div class="inline-block w-full align-middle">
                        <div class="overflow-hidden shadow rounded-lg">
                            <table class="w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                                <thead class="bg-gray-100 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                            No
                                        </th>
                                        <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                            Nama Lengkap
                                        </th>
                                        <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                            NIP
                                        </th>
                                        </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                <?= $no++; ?>
                                            </td>
                                            <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                                                <div class="text-base font-semibold text-gray-900 dark:text-white">
                                                    <?= htmlspecialchars($row['nama']); ?>
                                                </div>
                                            </td>
                                            <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                <?= htmlspecialchars($row['nip']); ?>
                                            </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="p-4 text-center text-gray-500 dark:text-gray-400">
                                                Belum ada guru yang mendaftar.
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