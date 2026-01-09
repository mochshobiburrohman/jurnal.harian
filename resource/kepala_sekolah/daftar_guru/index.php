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
    
    </main>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
  
</body>
</html>