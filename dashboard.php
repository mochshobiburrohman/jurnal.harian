<?php
session_start();
if (!isset($_SESSION['role'])) header("Location: index.php");

include 'koneksi.php';
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
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Jurnal Harian Guru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> 
</head>
<body>
    
     <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Jurnal Harian Guru</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text">Halo, <?php echo $nama; ?> (<?php echo ucfirst($role); ?>)</span>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <?php if ($role == 'guru'): ?>
            <h2>Jurnal Harian Saya</h2>
            <a href="add_journal.php" class="btn btn-success mb-3">Tambah Jurnal</a>
            <table class="table table-striped">
                <thead><tr><th>Tanggal</th><th>Isi</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php while ($row = $journals->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['tanggal']; ?></td>
                            <td><?php echo substr($row['isi_jurnal'], 0, 50) . '...'; ?></td>
                            <td><?php echo $row['status']; ?></td>
                            <td><a href="edit_journal.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php elseif ($role == 'kepala_sekolah'): ?>
            <h2>Verifikasi Jurnal</h2>
            <table class="table table-striped">
                <thead><tr><th>Guru</th><th>Tanggal</th><th>Isi</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php while ($row = $journals->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['nama_guru']; ?></td>
                            <td><?php echo $row['tanggal']; ?></td>
                            <td><?php echo substr($row['isi_jurnal'], 0, 50) . '...'; ?></td>
                            <td><?php echo $row['status']; ?></td>
                            <td><a href="verify_journal.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">Verifikasi</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php elseif ($role == 'admin'): ?>
            <h2>Kelola Data</h2>
            <h3>Guru</h3>
            <table class="table table-striped">
                <thead><tr><th>Nama</th><th>NIP</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php while ($row = $gurus->fetch_assoc()): ?>
                        <tr><td><?php echo $row['nama']; ?></td><td><?php echo $row['nip']; ?></td><td><a href="#" class="btn btn-sm btn-danger">Hapus</a></td></tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <h3>Kepala Sekolah</h3>
            <table class="table table-striped">
                <thead><tr><th>Nama</th><th>NIP</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php while ($row = $kepseks->fetch_assoc()): ?>
                        <tr><td><?php echo $row['nama']; ?></td><td><?php echo $row['nip']; ?></td><td><a href="#" class="btn btn-sm btn-danger">Hapus</a></td></tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>