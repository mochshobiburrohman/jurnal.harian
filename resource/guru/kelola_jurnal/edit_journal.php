<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    header("Location: index.php");
    exit;
}

include '../../../koneksi.php';

$id_guru = $_SESSION['user_id'];
$id_jurnal = isset($_GET['id']) ? intval($_GET['id']) : null;
$isEdit = false;
$data = null;

/* AMBIL DATA JIKA MODE EDIT */
if ($id_jurnal) {
    $query = $conn->query(
        "SELECT * FROM jurnal_harian 
         WHERE id = $id_jurnal AND id_guru = $id_guru"
    );

    if ($query->num_rows === 0) {
        echo "<script type='text/javascript'>alert('Berhasil mengedit jurnal!');</script>";
        header("Location: index.php");
        exit;
    }

    $data = $query->fetch_assoc();
    $isEdit = true;
}
/* PROSES SIMPAN / UPDATE */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['tanggal'];
    $isi = $_POST['isi'];

    if ($isEdit) {
        $sql = "UPDATE jurnal_harian 
                SET tanggal='$tanggal', isi_jurnal='$isi'
                WHERE id=$id_jurnal AND id_guru=$id_guru";
        $pesan = "Jurnal berhasil diperbarui!";
    } else {
        $sql = "INSERT INTO jurnal_harian (id_guru, tanggal, isi_jurnal)
                VALUES ('$id_guru', '$tanggal', '$isi')";
        $pesan = "Jurnal berhasil ditambahkan!";
    }

    if ($conn->query($sql)) {
        echo "<script>alert('$pesan');window.location='index.php';</script>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isEdit ? 'Edit Jurnal' : 'Tambah Jurnal'; ?></title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 dark:bg-gray-900 antialiased">
<?php include "../../partials/navbar.php"; ?>
<?php include "../../partials/sidebar_guru.php"; ?>
<main class="h-screen mx-auto p-4 md:ml-64 h-auto pt-20">
<div class="max-w-xl mx-auto mt-12 bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">
        <?= $isEdit ? 'Edit Jurnal Harian' : 'Tambah Jurnal Harian'; ?>
    </h2>

    <form method="POST" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                Tanggal
            </label>
            <input type="date" name="tanggal" required
                   value="<?= $data['tanggal'] ?? ''; ?>"
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
                             dark:bg-gray-700 dark:text-white"><?= $data['isi_jurnal'] ?? ''; ?></textarea>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="px-5 py-2 bg-blue-600 text-white text-sm font-medium
                           rounded-md hover:bg-blue-700 transition">
                <?= $isEdit ? 'Update' : 'Simpan'; ?>
            </button>

            <a href="index.php"
               class="px-5 py-2 bg-gray-500 text-white text-sm font-medium
                      rounded-md hover:bg-gray-600 transition">
                Batal
            </a>
        </div>
    </form>
</div>
</main>
</body>
</html>