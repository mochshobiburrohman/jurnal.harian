<?php
session_start();
require_once '../../koneksi.php'; 

// Cek sesi login khusus Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}

$username = $_SESSION['username'];
$table = 'admin';

// Proses Update Data
if (isset($_POST['update_profil'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $nip = mysqli_real_escape_string($conn, $_POST['nip']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $jabatan = mysqli_real_escape_string($conn, $_POST['jabatan']);

    // Logic Upload Foto
    $foto_query = ""; 
    if (!empty($_FILES['foto']['name'])) {
        $fotoName = time() . '_' . $_FILES['foto']['name'];
        $targetDir = "../../img/profil/";
        
        // Buat folder jika belum ada
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $targetFile = $targetDir . basename($fotoName);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $extensions_arr = array("jpg", "jpeg", "png", "gif");

        if (in_array($imageFileType, $extensions_arr)) {
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $targetFile)) {
                $foto_query = ", foto='$fotoName'";
            } else {
                $err = "Gagal upload gambar.";
            }
        } else {
            $err = "Format file harus JPG, JPEG, PNG, atau GIF.";
        }
    }

    if (!isset($err)) {
        // Update Session Nama jika nama berubah
        $_SESSION['nama'] = $nama;

        $sql = "UPDATE $table SET nama='$nama', nip='$nip', alamat='$alamat', jabatan='$jabatan' $foto_query WHERE username='$username'";
        
        if ($conn->query($sql) === TRUE) {
            $msg = "Profil berhasil diperbarui!";
        } else {
            $err = "Gagal memperbarui data: " . $conn->error;
        }
    }
}

// Ambil Data Admin Terbaru
$sql = "SELECT * FROM $table WHERE username='$username'";
$result = $conn->query($sql);
$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Admin - Jurnal Guru</title>
    <link href="../../src/output.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900">

    <?php include '../partials/navbar.php'; ?>
    
    <?php include '../partials/sidebar_admin.php'; ?>

    <div class="p-4 sm:ml-64 pt-20">
        <div class="max-w-4xl mx-auto bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 p-6">
            
            <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Profil Admin</h2>

            <?php if(isset($msg)): ?>
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert"><?= $msg ?></div>
            <?php endif; ?>
            
            <?php if(isset($err)): ?>
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert"><?= $err ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="grid md:grid-cols-3 gap-6">
                    
                    <div class="md:col-span-1 text-center">
                        <div class="mb-4">
                            <?php 
                                $fotoPath = !empty($data['foto']) ? "../../img/profil/".$data['foto'] : "https://via.placeholder.com/150?text=Admin";
                            ?>
                            <img class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-gray-200 dark:border-gray-600" src="<?= $fotoPath ?>" alt="Foto Profil">
                        </div>
                        
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="file_input">Ganti Foto</label>
                        <input name="foto" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="file_input" type="file">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-300">JPG, PNG or GIF (Max 2MB).</p>
                    </div>

                    <div class="md:col-span-2 space-y-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Lengkap</label>
                            <input type="text" name="nama" value="<?= $data['nama'] ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">NIP / ID Admin</label>
                            <input type="text" name="nip" value="<?= isset($data['nip']) ? $data['nip'] : '' ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jabatan</label>
                            <input type="text" name="jabatan" value="<?= isset($data['jabatan']) ? $data['jabatan'] : 'Administrator' ?>" placeholder="Contoh: Administrator" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat</label>
                            <textarea name="alamat" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"><?= isset($data['alamat']) ? $data['alamat'] : '' ?></textarea>
                        </div>

                        <div class="pt-4">
                            <button type="submit" name="update_profil" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Simpan Perubahan</button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</body>
</html>