<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../index.php");
    exit;
}

include '../../../koneksi.php';

// Ambil data guru
$sql = "SELECT * FROM guru ORDER BY nama ASC";
$gurus = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Data Guru</title>
    <link href="../../../src/output.css" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-gray-900">

<?php include ("../../partials/navbar.php")?>
<?php include ("../../partials/sidebar_admin.php")?>

<main class="p-4 md:ml-64 h-auto pt-20">
    <div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700 rounded-t-lg">
        <div class="w-full mb-1">
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Data Guru</h1>
        </div>
        <button data-modal-target="add-user-modal" data-modal-toggle="add-user-modal" class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
            Tambah Guru
        </button>
    </div>

    <div class="flex flex-col mt-4">
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
                            <?php while ($row = $gurus->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                <td class="p-4 text-sm font-normal text-gray-900 dark:text-white"><?= htmlspecialchars($row['nama']); ?></td>
                                <td class="p-4 text-sm font-normal text-gray-500 dark:text-gray-400"><?= htmlspecialchars($row['nip']); ?></td>
                                <td class="p-4 text-sm font-normal text-gray-500 dark:text-gray-400"><?= htmlspecialchars($row['username']); ?></td>
                                <td class="p-4 text-center space-x-2">
                                    <a href="edit_user.php?id=<?= $row['id']; ?>&role=guru" 
                                       class="text-white bg-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-sm px-3 py-2 dark:focus:ring-yellow-900">
                                        Edit
                                    </a>
                                    <a href="delete_user.php?id=<?= $row['id']; ?>&type=guru" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus data guru ini?');"
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

<div id="add-user-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Tambah Guru Baru
                </h3>
                <button type="button" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="add-user-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <div class="p-4 md:p-5">
                <form class="space-y-4" action="proses_user.php" method="POST">
                    <input type="hidden" name="action" value="add_guru">
                    <div>
                        <label for="nama" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Lengkap</label>
                        <input type="text" name="nama" id="nama" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required>
                    </div>
                    <div>
                        <label for="nip" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">NIP</label>
                        <input type="text" name="nip" id="nip" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required>
                    </div>
                    <div>
                        <label for="username" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Username</label>
                        <input type="text" name="username" id="username" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required>
                    </div>
                    <div>
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                        <input type="password" name="password" id="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required>
                    </div>
                    <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Simpan Data</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
</body>
</html>