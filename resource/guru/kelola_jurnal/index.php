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
<?php include ("../../partials/sidebar_guru.php")?>
    <main class="h-screen mx-auto p-4 md:ml-64 h-auto pt-20">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
    Jurnal Harian Saya
</h2>

<a href="add_journal.php"
class="inline-block border-white border px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
    Tambah Jurnal
</a>

<div class="dark:text-white overflow-x-auto rounded-lg shadow">
    <table class="w-full border border-gray-200 dark:border-gray-700">
        <thead class="bg-gray-100 dark:bg-gray-800">
            <tr>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-white">
                    Tanggal
                </th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-white">
                    Isi
                </th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-white">
                    Status
                </th>
                <th class="px-4 py-3 text-center text-sm font-medium text-gray-700 dark:text-white">
                    Aksi
                </th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            <?php while ($row = mysqli_fetch_assoc($journals)): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">
                        <?php echo htmlspecialchars($row['tanggal']); ?>
                    </td>

                    <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">
                        <?php echo htmlspecialchars(substr($row['isi_jurnal'], 0, 50)) . '...'; ?>
                    </td>

                    <td class="px-4 py-2 text-sm">
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            <?php
                                echo ($row['status'] === 'disetujui')
                                    ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200'
                                    : (($row['status'] === 'ditolak')
                                        ? 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200'
                                        : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-200');
                            ?>">
                            <?php echo htmlspecialchars($row['status']); ?>
                        </span>
                    </td>

                    <td class="px-4 py-2 text-center">
                        <a href="edit_journal.php?id=<?php echo $row['id']; ?>"
                           class="inline-block px-3 py-1 text-sm font-medium text-white
                                  bg-yellow-500 rounded hover:bg-yellow-600 transition">
                            Edit
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>


    </main>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
  
</body>
</html>