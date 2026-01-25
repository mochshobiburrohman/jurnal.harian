<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../index.php");
    exit;
}

include '../../../koneksi.php';

// Ambil data guru untuk dropdown
$guru_sql = "SELECT id, nama FROM guru ORDER BY nama ASC";
$guru_result = $conn->query($guru_sql);
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

<?php include ("../../partials/navbar.php")?>
<?php include ("../../partials/sidebar_admin.php")?>

<main class="p-4 md:ml-64 h-auto pt-20">
    <div class="mb-4">
        <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">
            Cetak Jurnal Guru
        </h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        
        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Cetak Jurnal Bulanan</h5>
            <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Pilih bulan dan guru (opsional) untuk mencetak rekap jurnal.</p>
            
            <form action="cetak_jurnal.php" method="GET" target="_blank">
                <div class="mb-4">
                    <label for="bulan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Bulan</label>
                    <input type="month" id="bulan" name="bulan" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                </div>
                
                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Guru (Opsional)</label>
                    <select name="id_guru" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        <option value="">-- Semua Guru --</option>
                        <?php 
                        // Reset pointer data guru
                        $guru_result->data_seek(0);
                        while($g = $guru_result->fetch_assoc()): 
                        ?>
                            <option value="<?= $g['id']; ?>"><?= $g['nama']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <button type="submit" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    Cetak Bulanan
                </button>
            </form>
        </div>

        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Cetak Jurnal Harian</h5>
            <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Pilih tanggal dan guru (opsional) untuk mencetak jurnal harian.</p>
            
            <form action="cetak_jurnal.php" method="GET" target="_blank">
                <div class="mb-4">
                    <label for="tanggal" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Tanggal</label>
                    <input type="date" id="tanggal" name="tanggal" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                </div>
                
                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Guru (Opsional)</label>
                    <select name="id_guru" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        <option value="">-- Semua Guru --</option>
                        <?php 
                        // Reset pointer data guru
                        $guru_result->data_seek(0);
                        while($g = $guru_result->fetch_assoc()): 
                        ?>
                            <option value="<?= $g['id']; ?>"><?= $g['nama']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <button type="submit" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-green-700 rounded-lg hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                    Cetak Harian
                </button>
            </form>
        </div>

    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
</body>
</html>