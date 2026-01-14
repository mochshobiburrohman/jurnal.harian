<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $role = $_POST['role'];

    $table = ($role == 'admin') ? 'admin' : (($role == 'guru') ? 'guru' : 'kepala_sekolah');
    $sql = "SELECT * FROM $table WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $role;
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama'] = $user['nama'];
        
        if ($role == 'admin'){
            header("Location: resource/admin/index.php");
        } elseif ($role =='kepala_sekolah'){
           header("Location: resource/kepala_sekolah/index.php");
        } elseif($role == 'guru'){
            header("Location: resource/guru/index.php");
        }
    } else {
        $error = "Login gagal! Periksa Username, Password, atau Role Anda.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Jurnal Harian Guru</title>
    <link href="src/output.css" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    
    <section class="min-h-screen flex flex-col items-center justify-center px-6 py-8 mx-auto lg:py-0">
        
        <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">
            <img class="w-8 h-8 mr-2" src="img/logo.png" alt="logo">
            SMK YPM 12 TUBAN    
        </a>

        <div class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
            <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white text-center">
                    LOGIN SISTEM
                </h1>
                
                <form method="POST" class="space-y-4 md:space-y-6">
                    
                    <div>
                        <label for="username" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Username</label>
                        <input type="text" name="username" id="username" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Masukkan username" required="">
                    </div>

                    <div>
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                        <input type="password" name="password" id="password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required="">
                    </div>
                    
                    <div>
                        <label for="role" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Role</label>
                        <select name="role" id="role" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="" disabled selected>-- Silahkan Pilih --</option>
                            <option value="admin">Admin</option>
                            <option value="kepala_sekolah">Kepala Sekolah</option>
                            <option value="guru">Guru</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Masuk
                    </button>
                    
                    <p class="text-sm font-light text-gray-500 dark:text-gray-400 text-center">
                        Belum punya akun? <a href="register.php" class="font-medium text-blue-600 hover:underline dark:text-blue-500">Daftar disini</a>
                    </p>

                    <?php if (isset($error)): ?>
                        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                            <span class="font-medium">Error!</span> <?= $error ?>
                        </div>
                    <?php endif; ?>

                </form>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
</body>
</html>