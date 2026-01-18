<?php
session_start();
// Cek sesi dan role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'kepala_sekolah') {
    header("Location: ../../../index.php");
    exit;
}

include '../../../koneksi.php';

// Ambil data guru untuk dropdown filter
$sql_guru = "SELECT * FROM guru ORDER BY nama ASC";
$result_guru = $conn->query($sql_guru);

// Inisialisasi variabel filter
$filter_guru = isset($_GET['id_guru']) ? $_GET['id_guru'] : '';

// Query dasar: Hanya ambil yang statusnya 'verified'
$sql = "SELECT j.*, g.nama AS nama_guru 
        FROM jurnal_harian j 
        JOIN guru g ON j.id_guru = g.id 
        WHERE j.status = 'verified'";

// Jika ada filter guru yang dipilih, tambahkan kondisi WHERE
if (!empty($filter_guru)) {
    $sql .= " AND j.id_guru = " . intval($filter_guru);
}

// Urutkan berdasarkan tanggal terbaru
$sql .= " ORDER BY j.tanggal DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lihat Jurnal Guru - Kepala Sekolah</title>
    <link href="../../../src/output.css" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    
    <div class="antialiased">
        <?php include ("../../partials/navbar.php") ?>
        <?php include ("../../partials/sidebar_kepala_sekolah.php") ?>

        <main class="p-4 md:ml-64 h-auto pt-20">
            <div class="mb-4">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">
                    Riwayat Jurnal Terverifikasi
                </h1>
            </div>

            <div class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <form action="" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="w-full md:w-1/3">
                        <label for="id_guru" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Guru</label>
                        <select id="id_guru" name="id_guru" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                            <option value="">-- Semua Guru --</option>
                            <?php 
                            // Reset pointer data guru jika perlu atau iterasi ulang
                            mysqli_data_seek($result_guru, 0);
                            while ($guru = $result_guru->fetch_assoc()): 
                            ?>
                                <option value="<?= $guru['id']; ?>" <?= ($filter_guru == $guru['id']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($guru['nama']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="w-full md:w-auto">
                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                            Tampilkan
                        </button>
                    </div>
                </form>
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
                                        <th class="p-4 text-xs font-medium text-center text-gray-500 uppercase dark:text-gray-400">Jml Hadir</th>
                                        <th class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Status</th>
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
                                                <?= nl2br(htmlspecialchars($row['isi_jurnal'])); ?>
                                            </td>
                                            <td class="p-4 text-sm text-center text-gray-900 dark:text-white">
                                                 <?= $row['hadir'] ?>
                                            </td>
                                            <td class="p-4 text-sm text-gray-900 dark:text-white">
                                                <span class="px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                    <?= ucfirst($row['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="p-4 text-center text-gray-500 dark:text-gray-400">
                                                Tidak ada data jurnal yang ditemukan.
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