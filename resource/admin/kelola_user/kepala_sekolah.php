<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../index.php");
    exit;
}

include '../../../koneksi.php';

// Ambil data kepala sekolah
$sql = "SELECT * FROM kepala_sekolah ORDER BY nama ASC";
$kepseks = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Data Kepala Sekolah</title>
    <link href="../../../src/output.css" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-gray-900">

<?php include ("../../partials/navbar.php")?>
<?php include ("../../partials/sidebar_admin.php")?>

<main class="p-4 md:ml-64 h-auto pt-20">
    <div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700 rounded-t-lg">
        <div class="w-full mb-1">
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Data Kepala Sekolah</h1>
        </div>
    </div>

    <div class="flex flex-col">
        <div class="overflow-x-auto">
            <div class="inline-block w-full align-middle">
                <div class="overflow-hidden shadow">
                    <table class="w-full divide-y divide-gray-200 dark:divide-gray-600">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Nama Lengkap</th>
                                <th class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">NIP</th>
                                <th class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Username</th>
                                <th class="p-4 text-xs font-medium text-center text-gray-500 uppercase dark:text-gray-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            <?php while ($row = $kepseks->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                <td class="p-4 text-sm font-normal text-gray-900 dark:text-white"><?= htmlspecialchars($row['nama']); ?></td>
                                <td class="p-4 text-sm font-normal text-gray-500 dark:text-gray-400"><?= htmlspecialchars($row['nip']); ?></td>
                                <td class="p-4 text-sm font-normal text-gray-500 dark:text-gray-400"><?= htmlspecialchars($row['username']); ?></td>
                                <td class="p-4 text-center space-x-2">
                                    <a href="edit_user.php?id=<?= $row['id']; ?>&role=kepala_sekolah" 
                                       class="text-white bg-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-sm px-3 py-2 dark:focus:ring-yellow-900">
                                        Edit
                                    </a>
                                    <a href="delete_user.php?id=<?= $row['id']; ?>&type=kepala_sekolah" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');"
                                       class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-3 py-2 dark:bg-red-500 dark:hover:bg-red-600">
                                        Hapus
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
</body>
</html>