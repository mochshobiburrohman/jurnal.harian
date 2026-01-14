<?php
session_start();

// Cek sesi dan role admin
// Pastikan tidak ada spasi/enter sebelum <?php di file ini
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../index.php");
    exit;
}

include '../../../koneksi.php';

// Ambil daftar semua guru untuk dropdown
$sql_guru = "SELECT * FROM guru ORDER BY nama ASC";
$result_guru = $conn->query($sql_guru);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Jurnal - Admin</title>
    <link href="../../../src/output.css" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="antialiased">
        <?php include ("../../partials/navbar.php") ?>
        <?php include ("../../partials/sidebar_admin.php") ?>

        <main class="p-4 md:ml-64 h-auto pt-20">
            <div class="mb-4">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">
                    Cetak Laporan Jurnal (Admin)
                </h1>
            </div>
            <div class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow dark:bg-gray-800">
                <form action="cetak_jurnal.php" method="GET" target="_blank" class="space-y-5">
                    
                    <div>
                        <label for="id_guru" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Pilih Guru
                        </label>
                        <select name="id_guru" id="id_guru" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                            <option value="">-- Pilih Nama Guru --</option>
                            <?php if ($result_guru->num_rows > 0): ?>
                                <?php while($guru = $result_guru->fetch_assoc()): ?>
                                    <option value="<?= $guru['id'] ?>"><?= htmlspecialchars($guru['nama']) ?> (NIP: <?= htmlspecialchars($guru['nip']) ?>)</option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div>
                        <label for="bulan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Pilih Bulan
                        </label>
                        <input type="month" name="bulan" id="bulan" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    </div>

                    <div class="pt-4">
                        <button type="submit"
                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                            Cetak Laporan
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
</body>
</html>