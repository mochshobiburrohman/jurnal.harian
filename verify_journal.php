<?php
session_start();
if ($_SESSION['role'] != 'kepala_sekolah') header("Location: dashboard.php");

include 'koneksi.php';
$id = $_GET['id'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = $_POST['status'];
    $komentar = $_POST['komentar'];
    $id_verifikator = $_SESSION['user_id'];
    $conn->query("UPDATE jurnal_harian SET status='$status' WHERE id=$id");
    $conn->query("INSERT INTO verifikasi (id_jurnal, id_verifikator, status, komentar) VALUES ($id, $id_verifikator, '$status', '$komentar')");
    header("Location: dashboard.php");
}
$sql = "SELECT * FROM jurnal_harian WHERE id=$id";
$journal = $conn->query($sql)->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Jurnal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Verifikasi Jurnal</h2>
        <p><strong>Tanggal:</strong> <?php echo $journal['tanggal']; ?></p>
        <p><strong>Isi:</strong> <?php echo $journal['isi_jurnal']; ?></p>
        <form method="POST">
            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-control" required>
                    <option value="verified">Verified</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div class="mb-3"><label>Komentar</label><textarea name="komentar" class="form-control"></textarea></div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</body>
</html>