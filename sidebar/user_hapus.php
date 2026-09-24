<?php
include '../koneksi.php';

/** @var mysqli $koneksi */

$id = mysqli_real_escape_string($koneksi, $_GET['id'] ?? '');

if (!empty($id)) {
    // 1. Kembalikan stok produk sebelum item transaksi dihapus (Opsional tapi disarankan)
    $q_detail = mysqli_query($koneksi, "SELECT * FROM detail_penjualan WHERE penjualanID='$id'");
    while ($item = mysqli_fetch_assoc($q_detail)) {
        $pid = $item['produkID'];
        $qty = $item['jumlah'];
        mysqli_query($koneksi, "UPDATE produk SET stok = stok + $qty WHERE produkID='$pid'");
    }

    // 2. Hapus detail item penjualan
    mysqli_query($koneksi, "DELETE FROM detail_penjualan WHERE penjualanID='$id'");

    // 3. Hapus data utama penjualan
    mysqli_query($koneksi, "DELETE FROM penjualan WHERE penjualanID='$id'");
}

// Redirect kembali ke halaman daftar penjualan
echo "<script>alert('Transaksi berhasil dihapus dan stok telah dikembalikan.'); window.location.href='penjualan.php';</script>";
?>