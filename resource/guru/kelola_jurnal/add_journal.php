<?php
session_start();
// Cek sesi login guru
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    header("Location: ../../index.php");
    exit;
}

include '../../../koneksi.php';
// DAFTAR MATA PELAJARAN LENGKAP SMK YPM 12 TUBAN
// Mencakup: TKR, TPM, TEI, Las, Listrik + Mulok Aswaja
$daftar_mapel = [
    // --- MUATAN UMUM (A) & (B) ---
    "Pendidikan Agama dan Budi Pekerti",
    "Pendidikan Pancasila",
    "Bahasa Indonesia",
    "Matematika",
    "Sejarah",
    "Bahasa Inggris",
    "Seni Budaya",
    "Pendidikan Jasmani, Olahraga, dan Kesehatan",
    "Informatika",
    "Projek IPAS (Ilmu Pengetahuan Alam dan Sosial)",
    
    // --- MUATAN LOKAL (KHAS YPM 12 TUBAN & JATIM) ---
    "Aswaja dan Ke-NU-an",
    "Bahasa Daerah (Jawa)",

    // --- MATA PELAJARAN JURUSAN TEKNIK KENDARAAN RINGAN (TKR) ---
    "Dasar-dasar Teknik Otomotif",
    "Gambar Teknik Otomotif",
    "Pemeliharaan Mesin Kendaraan Ringan (PMKR)",
    "Pemeliharaan Sasis dan Pemindah Tenaga (PSPT)",
    "Pemeliharaan Kelistrikan Kendaraan Ringan (PKKR)",
    "Teknologi Dasar Otomotif",
    "Pekerjaan Dasar Teknik Otomotif",

    // --- MATA PELAJARAN JURUSAN TEKNIK PEMESINAN (TPM) ---
    "Dasar-dasar Teknik Mesin",
    "Gambar Teknik Mesin",
    "Pekerjaan Dasar Teknik Mesin",
    "Dasar Perancangan Teknik Mesin",
    "Teknik Pemesinan Bubut",
    "Teknik Pemesinan Frais",
    "Teknik Pemesinan Gerinda",
    "Teknik Pemesinan NC/CNC dan CAM",
    "Gambar Teknik Manufaktur",

    // --- MATA PELAJARAN JURUSAN TEKNIK ELEKTRONIKA INDUSTRI (TEI) ---
    "Dasar-dasar Teknik Elektronika",
    "Kerja Bengkel dan Gambar Teknik",
    "Dasar Listrik dan Elektronika",
    "Teknik Pemrograman, Mikroprosesor dan Mikrokontroler",
    "Penerapan Rangkaian Elektronika",
    "Sistem Pengendali Elektronik",
    "Pengendali Sistem Robotik",
    "Pembuatan dan Perbaikan Peralatan Elektronik",
    "Sistem Kontrol Elektropneumatik",
    "Pemrograman PLC dan HMI",

    // --- MATA PELAJARAN JURUSAN TEKNIK PENGELASAN (LAS) ---
    "Dasar-dasar Teknik Pengelasan",
    "Teknik Pengelasan OAW",
    "Teknik Pengelasan SMAW",
    "Teknik Pengelasan GMAW",
    "Teknik Pengelasan GTAW",
    "Cakram dan Pemeriksaan Hasil Las",
    "Gambar Teknik Pengelasan",

    // --- MATA PELAJARAN JURUSAN TEKNIK INSTALASI TENAGA LISTRIK (TITL) ---
    "Dasar-dasar Ketenagalistrikan",
    "Gambar Teknik Listrik",
    "Pekerjaan Dasar Elektromekanik",
    "Instalasi Penerangan Listrik",
    "Instalasi Tenaga Listrik",
    "Instalasi Motor Listrik",
    "Perbaikan Peralatan Listrik",

    // --- LAINNYA & PENGEMBANGAN DIRI ---
    "Produk Kreatif dan Kewirausahaan (PKK)",
    "Mata Pelajaran Pilihan",
    "Bimbingan Konseling (BK)",
    "Projek Penguatan Profil Pelajar Pancasila (P5)"
];

// Sortir abjad agar guru lebih mudah mencari
sort($daftar_mapel);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_guru = $_SESSION['user_id'];
    $tanggal = $_POST['tanggal'];
    $jam_ke  = $_POST['jam_ke'];
    $mapel   = $_POST['mata_pelajaran'];
    $kelas   = $_POST['kelas'];
    
    // PERBAIKAN: Ambil data dari form name='materi', tapi simpan ke variabel $isi
    $isi     = $_POST['materi']; 
    
    // Data Absensi
    $hadir   = $_POST['hadir'];
    $sakit   = $_POST['sakit'];
    $izin    = $_POST['izin'];
    $alpa    = $_POST['alpa'];
    
    $status  = 'pending';

    // PERBAIKAN QUERY SQL:
    // Kolom 'materi' diganti menjadi 'isi_jurnal' sesuai struktur database asli
    $sql = "INSERT INTO jurnal_harian (id_guru, tanggal, jam_ke, mata_pelajaran, kelas, isi_jurnal, hadir, sakit, izin, alpa, status)
            VALUES ('$id_guru', '$tanggal', '$jam_ke', '$mapel', '$kelas', '$isi', '$hadir', '$sakit', '$izin', '$alpa', '$status')";
 
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Berhasil Menambahkan Jurnal');window.location='index.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal: " . $conn->error . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Jurnal</title>
    <link href="../../../src/output.css" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-gray-900">

<?php include ("../../partials/navbar.php")?>
<?php include ("../../partials/sidebar_guru.php")?>

<main class="mx-auto p-4 md:ml-64 h-auto pt-20">
    <div class="max-w-2xl mx-auto mt-5 bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">
            Tambah Jurnal Harian
        </h2>

        <form method="POST" class="space-y-4">
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" required class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Jam Ke-</label>
                    <input type="text" name="jam_ke" placeholder="Contoh: 1-2" required class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:text-white">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
            <div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Kelas</label>
    <select name="kelas" required class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:text-white">
        <option value="" disabled selected>Pilih Kelas</option>
        <option value="X TKR A">X TKR A</option>
        <option value="X TKR B">X TKR B</option>
        <option value="X TKR C">X TKR C</option>
        <option value="X TPM A">X TPM A</option>
        <option value="X TPM B">X TPM B</option>
        <option value="X TEI">X TEI</option>
        <option value="X T.LIST">X T.LIST</option>
        <option value="X T.LAS">X T.LAS</option>
        <option value="XI TEI ">XI TEI </option>
        <option value="XI TPM">XI TPM </option>
        <option value="XI TKR A">XI TKR A</option>
        <option value="XI TKR B">XI TKR B</option>
        <option value="XI T.LIST">XI T.LIST</option>
        <option value="XI T.LAS">XI T.LAS</option>
        <option value="XII TEI ">XII TEI </option>
        <option value="XII TPM A">XII TPM A</option>
        <option value="XII TPM B">XII TPM B</option>
        <option value="XII TKR A">XII TKR A</option>
        <option value="XII TKR B">XII TKR B</option>
        <option value="XII TKR C">XII TKR B</option>
        <option value="XII T.LIST">XII T.LIST</option>
        <option value="XII T.LAS">XII T.LAS</option>
        </select>
</div>
<div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Mata Pelajaran</label>
                    <input list="list_mapel" type="text" name="mata_pelajaran" placeholder="Ketik / Pilih Mapel..." required autocomplete="off" class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                    <datalist id="list_mapel">
                        <?php foreach ($daftar_mapel as $m): ?>
                            <option value="<?= $m ?>"></option>
                        <?php endforeach; ?>
                    </datalist>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Materi Pembelajaran</label>
                <textarea name="materi" rows="3" required class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:text-white"></textarea>
            </div>

            <div class="p-4 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Data Absensi Siswa</h3>
                <div class="grid grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Hadir</label>
                        <input type="number" name="hadir" value="0" min="0" required class="w-full px-2 py-1 border rounded text-sm dark:bg-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Sakit</label>
                        <input type="number" name="sakit" value="0" min="0" required class="w-full px-2 py-1 border rounded text-sm dark:bg-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Izin</label>
                        <input type="number" name="izin" value="0" min="0" required class="w-full px-2 py-1 border rounded text-sm dark:bg-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Alpa</label>
                        <input type="number" name="alpa" value="0" min="0" required class="w-full px-2 py-1 border rounded text-sm dark:bg-gray-600 dark:text-white">
                    </div>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full px-5 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition">
                    Simpan Jurnal
                </button>
            </div>
        </form>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
</body>
</html>