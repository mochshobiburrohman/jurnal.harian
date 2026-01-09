<?php
session_start();
if ($_SESSION['role'] != 'guru') header("Location: dashboard.php");

include 'koneksi.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_guru = $_SESSION['user_id'];
    $tanggal = $_POST['tanggal'];
    $isi = $_POST['isi'];
    $sql = "INSERT INTO jurnal_harian (id_guru, tanggal, isi_jurnal) VALUES ($id_guru, '$tanggal', '$isi')";
    if ($conn->query($sql)) header("Location: dashboard.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Jurnal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Tambah Jurnal Harian</h2>
        <form method="POST">
            <div class="mb-3"><label>Tanggal</label><input type="date" name="tanggal" class="form-control" required></div>
            <div class="mb-3"><label>Isi Jurnal</label><textarea name="isi" class="form-control" rows="5" required></textarea></div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</body>
</html>