<?php
session_start();
require_once '../../koneksi.php'; 

// Cek sesi login
if (!isset($_SESSION['username'])) {
    header("Location: ../../index.php");
    exit;
}

$username = $_SESSION['username'];
$table = 'kepala_sekolah';

// --- PROSES UPDATE PROFIL ---
if (isset($_POST['update_profil'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $nip = mysqli_real_escape_string($conn, $_POST['nip']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    
    // Logic Upload Foto
    $foto_query = ""; 
    if (!empty($_FILES['foto']['name'])) {
        $fotoName = time() . '_' . $_FILES['foto']['name'];
        $targetDir = "../../img/profil/";
        
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
                $err_profil = "Gagal upload gambar.";
            }
        } else {
            $err_profil = "Format file harus JPG, JPEG, PNG, atau GIF.";
        }
    }

    if (!isset($err_profil)) {
        // Query update (tanpa jabatan, sesuai struktur tabel kepala sekolah)
        $sql = "UPDATE $table SET nama='$nama', nip='$nip', alamat='$alamat' $foto_query WHERE username='$username'";
        
        if ($conn->query($sql) === TRUE) {
            $msg_profil = "Profil berhasil diperbarui!";
            // Update session nama jika berubah
            $_SESSION['nama'] = $nama;
        } else {
            $err_profil = "Error updating record: " . $conn->error;
        }
    }
}

// --- PROSES GANTI PASSWORD ---
if (isset($_POST['ganti_password'])) {
    $old_pass = md5($_POST['old_password']);
    $new_pass = $_POST['new_password'];
    $conf_pass = $_POST['confirm_password'];

    // Ambil password lama dari database
    $cek = $conn->query("SELECT password FROM $table WHERE username='$username'");
    $data_user = $cek->fetch_assoc();

    if ($data_user['password'] != $old_pass) {
        $err_pass = "Password lama salah!";
    } elseif ($new_pass != $conf_pass) {
        $err_pass = "Konfirmasi password baru tidak cocok!";
    } elseif (strlen($new_pass) < 6) {
        $err_pass = "Password baru minimal 6 karakter!";
    } else {
        // Update password baru
        $hash_new_pass = md5($new_pass);
        $sql_pass = "UPDATE $table SET password='$hash_new_pass' WHERE username='$username'";
        
        if ($conn->query($sql_pass) === TRUE) {
            $msg_pass = "Password berhasil diubah!";
        } else {
            $err_pass = "Gagal mengubah password: " . $conn->error;
        }
    }
}

// Ambil Data User Terbaru
$sql = "SELECT * FROM $table WHERE username='$username'";
$result = $conn->query($sql);
$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Kepala Sekolah</title>
    <link href="../../src/output.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900">

    <?php include '../partials/navbar.php'; ?>
    
    <?php include '../partials/sidebar_kepala_sekolah.php'; ?>

    <div class="p-4 sm:ml-64 pt-20 pb-10">
        
        <div class="max-w-4xl mx-auto bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 p-6 mb-8">
            
            <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Edit Profil
            </h2>

            <?php if(isset($msg_profil)): ?>
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert"><?= $msg_profil ?></div>
            <?php endif; ?>
            
            <?php if(isset($err_profil)): ?>
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert"><?= $err_profil ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="grid md:grid-cols-3 gap-6">
                    
                    <div class="md:col-span-1 text-center">
                        <div class="mb-4">
                            <?php 
                                $fotoPath = !empty($data['foto']) ? "../../img/profil/".$data['foto'] : "https://via.placeholder.com/150";
                            ?>
                            <img class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-gray-200 dark:border-gray-600 shadow-sm" src="<?= $fotoPath ?>" alt="Foto Profil">
                        </div>
                        
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="file_input">Ganti Foto</label>
                        <input name="foto" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="file_input" type="file">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-300" id="file_input_help">PNG, JPG or GIF (Max 2MB).</p>
                    </div>

                    <div class="md:col-span-2 space-y-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Lengkap</label>
                            <input type="text" name="nama" value="<?= $data['nama'] ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">NIP</label>
                            <input type="text" name="nip" value="<?= $data['nip'] ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat</label>
                            <textarea name="alamat" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"><?= $data['alamat'] ?></textarea>
                        </div>

                        <div class="pt-2 text-right">
                            <button type="submit" name="update_profil" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                Simpan Profil
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="max-w-4xl mx-auto bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 p-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                Ganti Password
            </h2>

            <?php if(isset($msg_pass)): ?>
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert"><?= $msg_pass ?></div>
            <?php endif; ?>
            
            <?php if(isset($err_pass)): ?>
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert"><?= $err_pass ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="space-y-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password Lama</label>
                        <input type="password" name="old_password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password Baru</label>
                            <input type="password" name="new_password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Konfirmasi Password Baru</label>
                            <input type="password" name="confirm_password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                        </div>
                    </div>

                    <div class="pt-2 text-right">
                        <button type="submit" name="ganti_password" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                            Update Password
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
</body>
</html>