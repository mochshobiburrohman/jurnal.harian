<?php
session_start();
if (!isset($_SESSION['role'])) header("Location: index.php");

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
<?php include ("../../partials/sidebar_kepala_sekolah.php")?>
    <main class="p-4 md:ml-64 h-auto pt-20">
    <div class="p-6">
    <h2 class="text-xl font-semibold text-white mb-4">
        Verifikasi Jurnal
    </h2>

    <div class="overflow-x-auto rounded-lg border border-slate-600">
        <table class="w-full text-sm text-left text-white">
            <!-- HEADER -->
            <thead class="bg-slate-800 border-b border-slate-600">
                <tr>
                    <th class="px-4 py-3 font-semibold border-r border-slate-600">Nama Guru</th>
                    <th class="px-4 py-3 font-semibold border-r border-slate-600">Tanggal</th>
                    <th class="px-4 py-3 font-semibold border-r border-slate-600">Isi Jurnal</th>
                    <th class="px-4 py-3 font-semibold border-r border-slate-600">Status</th>
                    <th class="px-4 py-3 font-semibold text-center">Aksi</th>
                </tr>
            </thead>

            <!-- BODY -->
            <tbody class="bg-slate-900">
                <?php while ($row = $journals->fetch_assoc()): ?>
                    <tr class="border-b border-slate-700 hover:bg-slate-800 transition">
                        <td class="px-4 py-3 border-r border-slate-700">
                            <?= htmlspecialchars($row['nama_guru']); ?>
                        </td>

                        <td class="px-4 py-3 border-r border-slate-700">
                            <?= $row['tanggal']; ?>
                        </td>

                        <td class="px-4 py-3 border-r border-slate-700 max-w-lg truncate">
                            <?= htmlspecialchars($row['isi_jurnal']); ?>
                        </td>

                        <td class="px-4 py-3 border-r border-slate-700 font-medium
                            <?= $row['status'] === 'verified'
                                ? 'text-green-400'
                                : 'text-yellow-400'; ?>">
                            <?= ucfirst($row['status']); ?>
                        </td>

                        <td class="px-4 py-3 text-center">
                            <a href="verify_journal.php?id=<?= $row['id']; ?>"
                               class="px-3 py-1 bg-blue-600 dark:text-blue-600 rounded
                                      hover:bg-blue-700 transition text-xs">
                                Verifikasi
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
    <a href="cetak_jurnal.php" target="_blank"
       class="px-4 py-2 bg-green-600 text-white rounded
              hover:bg-green-700 transition text-sm">
        Cetak
    </a>
</div>

    </main>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
  
</body>
</html>