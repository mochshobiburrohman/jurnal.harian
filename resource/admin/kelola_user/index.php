<?php
session_start();
// if (!isset($_SESSION['role'])) header("Location: index.php");

include '../../../koneksi.php';
$role = $_SESSION['role'];
$nama = $_SESSION['nama'];

if ($role == 'guru') {
    // Dashboard Guru: Lihat dan tambah jurnal
    $id_guru = $_SESSION['user_id'];
    $sql = "SELECT * FROM jurnal_harian WHERE id_guru=$id_guru ORDER BY tanggal DESC";
    $journals = $conn->query($sql);
} elseif ($role == 'kepala_sekolah') {
    // Dashboard Kepsek: Lihat jurnal untuk verifikasi
    $sql = "SELECT j.*, g.nama AS nama_guru FROM jurnal_harian j JOIN guru g ON j.id_guru=g.id ORDER BY j.tanggal DESC";
    $journals = $conn->query($sql);
} elseif ($role == 'admin') {
    // Dashboard Admin: Kelola semua
    $sql = "SELECT * FROM guru";
    $gurus = $conn->query($sql);
    $sql = "SELECT * FROM kepala_sekolah";
    $kepseks = $conn->query($sql);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="../../../src/output.css" rel="stylesheet">
</head>
<body>
<div class="antialiased bg-gray-50 dark:bg-gray-900">
<?php include ("../../partials/navbar.php")?>
<?php include ("../../partials/sidebar_admin.php")?>
    <main class="p-4 md:ml-64 h-auto pt-20">
        <div class="text-gray-900 dark:text-white">
    <h3 class="text-lg font-semibold mb-4">Guru</h3>

    <div class="overflow-x-auto rounded-lg shadow">
        <table class="min-w-full border border-gray-200 dark:border-gray-700">
            <thead class="bg-gray-100 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium border-b dark:border-gray-700">
                        Nama
                    </th>
                    <th class="px-4 py-3 text-left text-sm font-medium border-b dark:border-gray-700">
                        NIP
                    </th>
                    <th class="px-4 py-3 text-center text-sm font-medium border-b dark:border-gray-700">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php while ($row = $gurus->fetch_assoc()): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <td class="px-4 py-2 text-sm">
                            <?php echo htmlspecialchars($row['nama']); ?>
                        </td>
                        <td class="px-4 py-2 text-sm">
                            <?php echo htmlspecialchars($row['nip']); ?>
                        </td>
                        <td class="px-4 py-2 text-center">
                            <a href="#"
                               class="inline-block px-3 py-1 text-sm font-medium text-white bg-red-600 rounded hover:bg-red-700 transition">
                                Hapus
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

    </main>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
  
</body>
</html>