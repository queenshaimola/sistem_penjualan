<?php
include '../koneksi.php';

/** @var mysqli $koneksi */

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);

    // 1. Cek apakah pelanggan ini sudah pernah bertransaksi
    $cek_transaksi = mysqli_query($koneksi, "SELECT * FROM penjualan WHERE pelangganID = '$id'");

    if (mysqli_num_rows($cek_transaksi) > 0) {
        // Jika ada di transaksi, cegah hapus
        echo "<script>alert('Pelanggan tidak bisa dihapus karena memiliki riwayat transaksi penjualan!'); window.location.href='pelanggan.php';</script>";
        exit();
    }

    // 2. Jika tidak ada relasi transaksi, eksekusi hapus
    $delete = mysqli_query($koneksi, "DELETE FROM pelanggan WHERE pelangganID = '$id'");

    if ($delete) {
        echo "<script>alert('Data pelanggan berhasil dihapus!'); window.location.href='pelanggan.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data: " . mysqli_error($koneksi) . "'); window.location.href='pelanggan.php';</script>";
    }
} else {
    header("Location: pelanggan.php");
    exit();
}
?>