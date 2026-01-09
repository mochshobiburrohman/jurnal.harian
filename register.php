<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Hash password
    $nama = $_POST['nama'];
    $nip = $_POST['nip'];
    $role = $_POST['role'];

    // Validasi dasar
    if (strlen($_POST['password']) < 6) {
        $error = "Password minimal 6 karakter!";
    } elseif ($role != 'guru' && $role != 'kepala_sekolah') {
        $error = "Role tidak valid!";
    } else {
        // Cek username unik
        $table = ($role == 'guru') ? 'guru' : 'kepala_sekolah';
        $check = $conn->query("SELECT id FROM $table WHERE username='$username'");
        if ($check->num_rows > 0) {
            $error = "Username sudah digunakan!";
        } else {
            // Insert data
            $sql = "INSERT INTO $table (username, password, nama, nip) VALUES ('$username', '$password', '$nama', '$nip')";
            if ($conn->query($sql)) {
                $success = "Pendaftaran berhasil! Silakan login.";
                header("refresh:2;url=index.php"); // Redirect setelah 2 detik
            } else {
                $error = "Pendaftaran gagal: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar - Jurnal Harian Guru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header text-center">Daftar Akun</div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label>Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>NIP</label>
                                <input type="text" name="nip" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Role</label>
                                <select name="role" class="form-control" required>
                                    <option value="guru">Guru</option>
                                    <option value="kepala_sekolah">Kepala Sekolah</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Daftar</button>
                        </form>
                        <p class="mt-3 text-center">Sudah punya akun? <a href="index.php">Login di sini</a></p>
                        <?php if (isset($error)) echo "<p class='text-danger mt-2'>$error</p>"; ?>
                        <?php if (isset($success)) echo "<p class='text-success mt-2'>$success</p>"; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>