<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}

include '../../../koneksi.php';

// Logika Hapus Jurnal (Opsional bagi Admin)
if (isset($_GET['hapus'])) {
    $id_jurnal = $_GET['hapus'];
    $del = $conn->query("DELETE FROM jurnal_harian WHERE id='$id_jurnal'");
    if ($del) {
        echo "<script>alert('Data jurnal berhasil dihapus'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data'); window.location='index.php';</script>";
    }
}

// Query Ambil Data Jurnal (JOIN dengan tabel Guru untuk dapat Nama)
$sql = "SELECT j.*, g.nama AS nama_guru 
        FROM jurnal_harian j 
        JOIN guru g ON j.id_guru = g.id 
        ORDER BY j.tanggal DESC, j.jam_ke DESC";
$journals = $conn->query($sql);

// Simpan ke array
$data_jurnal = [];
if ($journals->num_rows > 0) {
    while($row = $journals->fetch_assoc()) {
        $data_jurnal[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Jurnal Guru</title>
    <link href="../../../src/output.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans antialiased">

<?php include ("../../partials/navbar.php")?>
<?php include ("../../partials/sidebar_admin.php")?>

    <main class="p-4 md:ml-64 h-auto pt-24">
        
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Semua Jurnal Guru</h1>
            <p class="text-base text-gray-600 dark:text-gray-400 mt-1">Pantau seluruh aktivitas mengajar guru di sekolah.</p>
        </div>

        <div class="block md:hidden space-y-6">
            <?php if (count($data_jurnal) > 0): ?>
                <?php foreach($data_jurnal as $row): ?>
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl border-2 border-gray-200 dark:border-gray-700 overflow-hidden relative shadow-sm">
                    
                    <div class="px-5 py-3 bg-gray-200 dark:bg-gray-700 flex justify-between items-center border-b border-gray-300 dark:border-gray-600">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-gray-500 uppercase">Guru</span>
                            <span class="text-sm font-black text-gray-900 dark:text-white"><?= htmlspecialchars($row['nama_guru']) ?></span>
                        </div>
                        <span class="px-2 py-1 rounded text-[10px] font-bold border uppercase
                            <?= $row['status'] == 'verified' ? 'bg-green-100 text-green-700 border-green-300' : 
                               ($row['status'] == 'rejected' ? 'bg-red-100 text-red-700 border-red-300' : 'bg-yellow-100 text-yellow-700 border-yellow-300') ?>">
                            <?= $row['status'] ?>
                        </span>
                    </div>

                    <div class="p-5 space-y-4">
                        <div class="flex items-center gap-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <?= date('d M Y', strtotime($row['tanggal'])) ?> 
                            <span class="text-blue-600">• Jam ke-<?= $row['jam_ke'] ?></span>
                        </div>

                        <div class="flex justify-between items-start border-b border-dashed border-gray-300 pb-3">
                            <div>
                                <h3 class="text-lg font-black text-gray-900 dark:text-white leading-tight">
                                    <?= htmlspecialchars($row['mata_pelajaran']) ?>
                                </h3>
                                <div class="inline-block mt-1 bg-gray-300 dark:bg-gray-600 px-2 py-0.5 rounded text-xs font-bold text-gray-800 dark:text-gray-200">
                                    Kelas <?= htmlspecialchars($row['kelas']) ?>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 bg-transparent rounded-lg border border-gray-300 dark:border-gray-600">
                            <span class="block text-xs font-bold text-gray-500 uppercase mb-1">Materi:</span>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 leading-snug">
                                <?= htmlspecialchars(substr($row['isi_jurnal'], 0, 80)) . '...' ?>
                            </p>
                        </div>

                        <div>
                            <span class="block text-xs font-bold text-gray-500 uppercase mb-2 text-center tracking-widest">Absensi</span>
                            <div class="grid grid-cols-4 gap-2">
                                <div class="text-center p-1 border border-green-300 rounded bg-transparent">
                                    <div class="text-[10px] text-gray-500 font-bold">H</div>
                                    <div class="font-black text-green-700"><?= $row['hadir'] ?></div>
                                </div>
                                <div class="text-center p-1 border border-yellow-300 rounded bg-transparent">
                                    <div class="text-[10px] text-gray-500 font-bold">S</div>
                                    <div class="font-black text-yellow-700"><?= $row['sakit'] ?></div>
                                </div>
                                <div class="text-center p-1 border border-blue-300 rounded bg-transparent">
                                    <div class="text-[10px] text-gray-500 font-bold">I</div>
                                    <div class="font-black text-blue-700"><?= $row['izin'] ?></div>
                                </div>
                                <div class="text-center p-1 border border-red-300 rounded bg-transparent">
                                    <div class="text-[10px] text-gray-500 font-bold">A</div>
                                    <div class="font-black text-red-700"><?= $row['alpa'] ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex border-t border-gray-300 dark:border-gray-600">
                        <a href="index.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus data ini?')" class="flex-1 py-3 text-sm font-bold text-red-700 hover:bg-red-100 dark:text-red-400 dark:hover:bg-gray-700 text-center transition">
                            HAPUS DATA
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="p-6 text-center border-2 border-dashed border-gray-300 rounded-xl">
                    <p class="text-gray-500 font-medium">Belum ada data jurnal guru.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="hidden md:block rounded-xl overflow-hidden border border-gray-300 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-200 border-b-2 border-gray-300 dark:border-gray-600">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-extrabold text-center border-r border-gray-300 w-12">No</th>
                            <th scope="col" class="px-6 py-4 font-extrabold border-r border-gray-300 w-48">Guru</th>
                            <th scope="col" class="px-6 py-4 font-extrabold border-r border-gray-300 w-40">Waktu</th>
                            <th scope="col" class="px-6 py-4 font-extrabold border-r border-gray-300 w-64">Kelas & Mapel</th>
                            <th scope="col" class="px-6 py-4 font-extrabold border-r border-gray-300">Materi</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-center border-r border-gray-300 w-32">Absensi</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-center border-r border-gray-300 w-32">Status</th>
                            <th scope="col" class="px-6 py-4 font-extrabold text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-300 dark:divide-gray-700">
                        <?php if (count($data_jurnal) > 0): ?>
                            <?php $no = 1; foreach($data_jurnal as $row): ?>
                            <tr class="bg-gray-50 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition duration-150">
                                
                                <td class="px-6 py-4 font-bold text-center border-r border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white">
                                    <?= $no++ ?>
                                </td>
                                
                                <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-700">
                                    <div class="font-black text-gray-900 dark:text-white text-base">
                                        <?= htmlspecialchars($row['nama_guru']) ?>
                                    </div>
                                </td>

                                <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-700">
                                    <div class="font-bold text-gray-800 dark:text-gray-200">
                                        <?= date('d/m/Y', strtotime($row['tanggal'])) ?>
                                    </div>
                                    <span class="text-xs font-bold text-gray-500">
                                        Jam ke-<?= $row['jam_ke'] ?>
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-700">
                                    <div class="font-bold text-gray-800 dark:text-white">
                                        <?= htmlspecialchars($row['mata_pelajaran']) ?>
                                    </div>
                                    <div class="text-xs font-semibold text-gray-500 mt-1 uppercase">
                                        Kelas <?= htmlspecialchars($row['kelas']) ?>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 border-r border-gray-300 dark:border-gray-700">
                                    <p class="text-gray-700 dark:text-gray-300 font-medium line-clamp-2" title="<?= htmlspecialchars($row['isi_jurnal']) ?>">
                                        <?= htmlspecialchars($row['isi_jurnal']) ?>
                                    </p>
                                </td>
                                
                                <td class="px-6 py-4 text-center border-r border-gray-300 dark:border-gray-700">
                                    <div class="grid grid-cols-2 gap-1">
                                        <span class="text-[10px] font-bold text-green-700 border border-green-300 px-1 rounded">H:<?= $row['hadir'] ?></span>
                                        <span class="text-[10px] font-bold text-yellow-700 border border-yellow-300 px-1 rounded">S:<?= $row['sakit'] ?></span>
                                        <span class="text-[10px] font-bold text-blue-700 border border-blue-300 px-1 rounded">I:<?= $row['izin'] ?></span>
                                        <span class="text-[10px] font-bold text-red-700 border border-red-300 px-1 rounded">A:<?= $row['alpa'] ?></span>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 text-center border-r border-gray-300 dark:border-gray-700">
                                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded text-[10px] font-black border uppercase w-full
                                        <?= $row['status'] == 'verified' ? 'text-green-700 border-green-400 bg-green-50' : 
                                           ($row['status'] == 'rejected' ? 'text-red-700 border-red-400 bg-red-50' : 'text-yellow-700 border-yellow-400 bg-yellow-50') ?>">
                                        <?= $row['status'] ?>
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 text-center">
                                    <a href="index.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus data jurnal ini?')" class="text-red-600 hover:text-red-900 transition" title="Hapus">
                                        <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="px-6 py-10 text-center text-gray-500">
                                    Belum ada data jurnal.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-3 bg-gray-200 dark:bg-gray-700 border-t border-gray-300 dark:border-gray-600 text-xs text-gray-500 dark:text-gray-400 text-right">
                Total: <?= count($data_jurnal) ?> Jurnal
            </div>
        </div>

    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
</body>
</html>