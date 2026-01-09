<?php
$host = 'localhost';
$user = 'root'; // Ganti jika perlu
$pass = ''; // Ganti jika perlu
$db = 'j_guru';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>