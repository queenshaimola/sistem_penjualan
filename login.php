<?php
session_start(); 
include 'koneksi.php'; 

$username = $_POST['username'];
$password = $_POST['password'];

$login = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username' AND password='$password'");
$cek   = mysqli_num_rows($login);

if ($cek > 0) {
    $data = mysqli_fetch_assoc($login);

    // SIMPAN DATA KEDALAM SESSION
    $_SESSION['username'] = $data['username'];
    $_SESSION['nama_user'] = $data['nama_user'];
    $_SESSION['status']   = "login";

    header("location:/dashboard.php");
} else {
    header("location:index.php?pesan=gagal");
}
?>