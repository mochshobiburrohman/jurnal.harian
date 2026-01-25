<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../index.php");
    exit;
}

include '../../../koneksi.php';

$action = $_POST['action'] ?? '';

// === FITUR TAMBAH GURU ===
if ($action === 'add_guru') {
    $nama = $_POST['nama'];
    $nip = $_POST['nip'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Enkripsi password

    $stmt = $conn->prepare("INSERT INTO guru (nama, nip, username, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama, $nip, $username, $password);

    if ($stmt->execute()) {
        echo "<script>alert('Berhasil menambah guru!'); window.location='guru.php';</script>";
    } else {
        echo "<script>alert('Gagal menambah guru: " . $conn->error . "'); window.location='guru.php';</script>";
    }
}

// === FITUR EDIT USER (GURU & KEPSEK) ===
elseif ($action === 'edit_user') {
    $id = $_POST['id'];
    $old_role = $_POST['old_role'];
    $new_role = $_POST['new_role'];
    
    $nama = $_POST['nama'];
    $nip = $_POST['nip'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cek apakah password diisi (untuk diupdate)
    $password_query_part = "";
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $password_query_part = ", password='$hashed_password'";
    }

    // SKENARIO 1: Role TIDAK berubah (Update biasa)
    if ($old_role === $new_role) {
        $table = ($old_role === 'guru') ? 'guru' : 'kepala_sekolah';
        
        $sql = "UPDATE $table SET nama='$nama', nip='$nip', username='$username' $password_query_part WHERE id='$id'";
        
        if ($conn->query($sql)) {
            $redirect = ($old_role === 'guru') ? 'guru.php' : 'kepala_sekolah.php';
            echo "<script>alert('Data berhasil diperbarui!'); window.location='$redirect';</script>";
        } else {
            echo "Error updating record: " . $conn->error;
        }
    } 
    
    // SKENARIO 2: Role BERUBAH (Pindah Tabel)
    else {
        // Ambil password lama jika password baru kosong
        if (empty($password)) {
            $old_table = ($old_role === 'guru') ? 'guru' : 'kepala_sekolah';
            $res = $conn->query("SELECT password FROM $old_table WHERE id='$id'");
            $row = $res->fetch_assoc();
            $final_password = $row['password'];
        } else {
            $final_password = password_hash($password, PASSWORD_DEFAULT);
        }

        // Mulai Transaksi Database
        $conn->begin_transaction();

        try {
            // 1. Insert ke tabel baru
            $new_table = ($new_role === 'guru') ? 'guru' : 'kepala_sekolah';
            $stmt_insert = $conn->prepare("INSERT INTO $new_table (nama, nip, username, password) VALUES (?, ?, ?, ?)");
            $stmt_insert->bind_param("ssss", $nama, $nip, $username, $final_password);
            $stmt_insert->execute();

            // 2. Hapus dari tabel lama
            $old_table = ($old_role === 'guru') ? 'guru' : 'kepala_sekolah';
            $stmt_delete = $conn->prepare("DELETE FROM $old_table WHERE id = ?");
            $stmt_delete->bind_param("i", $id);
            $stmt_delete->execute();

            $conn->commit();
            
            $redirect = ($new_role === 'guru') ? 'guru.php' : 'kepala_sekolah.php';
            echo "<script>alert('Role berhasil diubah dan data dipindahkan!'); window.location='$redirect';</script>";
            
        } catch (Exception $e) {
            $conn->rollback();
            echo "Gagal mengubah role: " . $e->getMessage();
        }
    }
}
?>