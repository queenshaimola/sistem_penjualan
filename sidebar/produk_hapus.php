<?php
include '../koneksi.php';
/** @var mysqli $koneksi */

$id = $_GET['id'];

// Hapus data produk berdasarkan ID
mysqli_query($koneksi, "DELETE FROM produk WHERE produkID='$id'");

// Redirect kembali ke halaman utama produk
echo "<script>window.location.href='produk.php';</script>";
?>