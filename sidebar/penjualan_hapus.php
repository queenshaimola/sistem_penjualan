<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/../koneksi.php';

/** @var mysqli $koneksi */

// Cek apakah user sudah login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:../index.php?pesan=belum_login");
    exit();
}

if (isset($_GET['id'])) {
    $penjualanID = mysqli_real_escape_string($koneksi, $_GET['id']);

    // 1. Ambil produk & jumlah dari tabel detail_penjualan
    $query_detail = mysqli_query($koneksi, "SELECT produkID, jumlah FROM detail_penjualan WHERE penjualanID='$penjualanID'");

    if ($query_detail && mysqli_num_rows($query_detail) > 0) {
        while ($detail = mysqli_fetch_assoc($query_detail)) {
            $produkID = $detail['produkID'];
            $jumlah   = (int)$detail['jumlah'];

            // 2. Kembalikan stok ke tabel produk
            mysqli_query($koneksi, "UPDATE produk SET stok = stok + $jumlah WHERE produkID='$produkID'");
        }
    }

    // 3. Hapus item dari detail_penjualan
    mysqli_query($koneksi, "DELETE FROM detail_penjualan WHERE penjualanID='$penjualanID'");

    // 4. Hapus data utama dari tabel penjualan
    $hapus_penjualan = mysqli_query($koneksi, "DELETE FROM penjualan WHERE penjualanID='$penjualanID'");

    if ($hapus_penjualan) {
        header("location:penjualan.php?pesan=hapus_berhasil");
    } else {
        header("location:penjualan.php?pesan=hapus_gagal");
    }
} else {
    header("location:penjualan.php");
}
exit();
?>