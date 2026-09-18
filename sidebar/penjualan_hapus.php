<?php
include '../koneksi.php';

/** @var mysqli $koneksi */

$id = $_GET['id'];

// Hapus data transaksi penjualan
mysqli_query($koneksi, "DELETE FROM penjualan WHERE penjualanID='$id'");

// Redirect kembali ke halaman daftar penjualan
echo "<script>window.location.href='penjualan.php';</script>";
?>