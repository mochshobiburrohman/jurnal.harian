<?php
// LOGIKA PENENTUAN PATH (JALUR FOLDER)
// Deteksi nama folder tempat file ini dibuka
$current_dir = basename(dirname($_SERVER['PHP_SELF']));

// Daftar folder yang merupakan level dashboard utama (Level 2 dari root)
$dashboard_dirs = ['admin', 'guru', 'kepala_sekolah'];

// Jika file dibuka di folder dashboard (admin/guru/kepala_sekolah), naik 2 level (../../)
// Jika file dibuka di sub-folder (kelola_jurnal, cetak, dll), naik 3 level (../../../)
if (in_array($current_dir, $dashboard_dirs)) {
    $path_root = '../../';
} else {
    $path_root = '../../../';
}
?>

<nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
  <div class="px-3 py-3 lg:px-5">
    <div class="flex items-center justify-between">
      
      <div class="flex items-center justify-start rtl:justify-end">
        <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
            <span class="sr-only">Buka sidebar</span>
            <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
               <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
            </svg>
         </button>
        
        <a href="#" class="flex items-center ms-2 md:ms-24 gap-4">
          <img src="<?= $path_root ?>img/logo.png" class="h-8" alt="Logo Sekolah" />
          <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">SI Jurnal Guru</span>
        </a>
      </div>

      <div class="flex items-center">
          <div class="flex items-center ms-3 gap-4">
            
            <div class="hidden md:block text-sm font-medium text-gray-900 dark:text-white">
                Halo, <span class="font-bold text-blue-600 dark:text-blue-400">
                    <?= htmlspecialchars($_SESSION['nama'] ?? 'Pengguna'); ?>
                </span>
            </div>

            <a href="<?= $path_root ?>logout.php" class="flex items-center gap-2 text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800">
                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 16">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 8h11m0 0L8 4m4 4-4 4m4-11h3a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-3"/>
                </svg>
                <span class="hidden sm:inline">Logout</span>
            </a>

          </div>
        </div>
    </div>
  </div>
</nav>