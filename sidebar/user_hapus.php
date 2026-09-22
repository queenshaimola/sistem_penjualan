<?php
include '../koneksi.php';

/** @var mysqli $koneksi */

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);

    // Mencegah hapus jika data tinggal 1 (opsional/opsi keamanan sederhana)
    $cek_total = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM user");
    $row = mysqli_fetch_assoc($cek_total);

    if ($row['total'] <= 1) {
        echo "<script>alert('User tidak bisa dihapus karena tersisa 1 user di sistem!'); window.location.href='user.php';</script>";
        exit();
    }

    // Query Hapus
    $delete = mysqli_query($koneksi, "DELETE FROM user WHERE userID = '$id'");

    if ($delete) {
        echo "<script>alert('User berhasil dihapus!'); window.location.href='user.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus user: " . mysqli_error($koneksi) . "'); window.location.href='user.php';</script>";
    }
} else {
    header("Location: user.php");
    exit();
}
?>