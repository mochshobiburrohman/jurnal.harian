<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../index.php");
    exit;
}

include '../../../koneksi.php';

$id = $_GET['id'] ?? null;
$role = $_GET['role'] ?? null;

if (!$id || !$role) {
    echo "ID atau Role tidak ditemukan.";
    exit;
}

// Tentukan tabel berdasarkan role
$table = ($role === 'kepala_sekolah') ? 'kepala_sekolah' : 'guru';

$stmt = $conn->prepare("SELECT * FROM $table WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User tidak ditemukan.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="../../../src/output.css" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-gray-900">

<?php include ("../../partials/navbar.php")?>
<?php include ("../../partials/sidebar_admin.php")?>

<main class="p-4 md:ml-64 h-auto pt-20">
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow dark:bg-gray-800">
        <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Edit Data User</h2>
        
        <form action="proses_user.php" method="POST">
            <input type="hidden" name="action" value="edit_user">
            <input type="hidden" name="id" value="<?= $user['id']; ?>">
            <input type="hidden" name="old_role" value="<?= $role; ?>">

            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Lengkap</label>
                <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']); ?>" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
            </div>

            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">NIP</label>
                <input type="text" name="nip" value="<?= htmlspecialchars($user['nip']); ?>" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
            </div>

            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($user['username']); ?>" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
            </div>

            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password (Kosongkan jika tidak diubah)</label>
                <input type="password" name="password" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="********">
            </div>

            <div class="mb-6">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Role (Jabatan)</label>
                <select name="new_role" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="guru" <?= ($role == 'guru') ? 'selected' : ''; ?>>Guru</option>
                    <option value="kepala_sekolah" <?= ($role == 'kepala_sekolah') ? 'selected' : ''; ?>>Kepala Sekolah</option>
                </select>
                <p class="mt-1 text-sm text-gray-500">Mengubah role akan memindahkan data user ke tabel yang sesuai.</p>
            </div>

            <div class="flex items-center space-x-4">
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">Simpan Perubahan</button>
                <a href="<?= ($role == 'guru') ? 'guru.php' : 'kepala_sekolah.php'; ?>" class="text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5">Batal</a>
            </div>
        </form>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
</body>
</html>