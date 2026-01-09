<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'kepala_sekolah') {
    header("Location: index.php");
    exit;
}

include '../../../koneksi.php';

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $komentar = $_POST['komentar'];
    $id_verifikator = $_SESSION['user_id'];

    $conn->query("UPDATE jurnal_harian SET status='$status' WHERE id=$id");
    $conn->query("INSERT INTO verifikasi (id_jurnal, id_verifikator, status, komentar)
                  VALUES ($id, $id_verifikator, '$status', '$komentar')");

    header("Location: index.php");
    exit;
}

$sql = "SELECT * FROM jurnal_harian WHERE id=$id";
$journal = $conn->query($sql)->fetch_assoc();
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
<?php include ("../../partials/sidebar_kepala_sekolah.php")?>
    <main class="p-4 md:ml-64 h-auto pt-20">
    <div class="p-6">
    <h2 class="text-xl font-semibold text-white mb-4">
        Verifikasi Jurnal
    </h2>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 dark:bg-gray-900">
    <div class="max-w-xl mx-auto mt-10 bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
            Verifikasi Jurnal
        </h2>

        <div class="mb-4 text-sm text-gray-700 dark:text-gray-200 space-y-2">
            <p>
                <span class="font-semibold">Tanggal:</span>
                <?php echo htmlspecialchars($journal['tanggal']); ?>
            </p>
            <p>
                <span class="font-semibold">Isi:</span>
                <?php echo nl2br(htmlspecialchars($journal['isi_jurnal'])); ?>
            </p>
        </div>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                    Status
                </label>
                <select name="status" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600
                               rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500
                               dark:bg-gray-700 dark:text-white">
                    <option value="verified">Verified</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                    Komentar
                </label>
                <textarea name="komentar" rows="4"
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
</body>
</html>
