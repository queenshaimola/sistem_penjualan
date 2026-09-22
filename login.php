<?php
session_start(); 
include 'koneksi.php'; 

/** @var mysqli $koneksi */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $password = md5($_POST['password']); // Hash MD5

    $query = "SELECT * FROM user WHERE username='$username' AND password='$password'";
    $login = mysqli_query($koneksi, $query);

    if ($login && mysqli_num_rows($login) > 0) {
        $data = mysqli_fetch_assoc($login);
        
        // Sesuaikan dengan variabel session yang dipakai halaman lain
        $_SESSION['userID']    = $data['userID'];
        $_SESSION['username']  = $data['username'];
        $_SESSION['nama_user'] = $data['nama_user'];
        $_SESSION['akses']     = $data['akses'];
        $_SESSION['status']    = "login"; // Menggunakan key status login kamu

        header("Location: dashboard.php");
        exit();
    } else {
        header("Location: index.php?pesan=gagal");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>