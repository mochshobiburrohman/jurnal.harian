<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    header("Location: index.php");
    exit;
}

include '../../../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_guru = $_SESSION['user_id'];
    $tanggal = $_POST['tanggal'];
    $isi = $_POST['isi'];

    $sql = "INSERT INTO jurnal_harian (id_guru, tanggal, isi_jurnal)
            VALUES ('$id_guru', '$tanggal', '$isi')";
 
    if ($conn->query($sql)) {
        echo "<script>alert('Berhasil Menambahkan Jurnal');window.location='index.php';</script>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="../../../src/output.css" rel="stylesheet">
</head>
<body>
<div class="antialiased bg-gray-50 dark:bg-gray-900">
<?php include ("../../partials/navbar.php")?>
<?php include ("../../partials/sidebar_guru.php")?>
<main class="h-screen mx-auto p-4 md:ml-64 h-auto pt-20">
    <div class="max-w-xl mx-auto mt-10 bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">
            Tambah Jurnal Harian
        </h2>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                    Tanggal
                </label>
                <input type="date" name="tanggal" required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600
                              rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500
                              dark:bg-gray-700 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                    Isi Jurnal
                </label>
                <textarea name="isi" rows="5" required
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600
                                 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500
                                 dark:bg-gray-700 dark:text-white"></textarea>
            </div>

            <div class="pt-2">
                <button type="submit"
                        class="px-5 py-2 bg-blue-600 text-white text-sm font-medium
                               rounded-md hover:bg-blue-700 transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</main>
</body>
</html>
