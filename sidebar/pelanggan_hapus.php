<?php
include '../koneksi.php';
/** @var mysqli $koneksi */

$id = $_GET['id'];

// Jalankan query hapus
mysqli_query($koneksi, "DELETE FROM pelanggan WHERE pelangganID='$id'");

// Kembalikan ke halaman pelanggan.php
header("location:pelanggan.php");
exit();
?>