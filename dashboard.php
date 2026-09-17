<?php 
include 'koneksi.php';

// Query untuk menghitung jumlah baris di masing-masing tabel
$count_produk    = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM produk"));
$count_pelanggan = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM pelanggan"));
$count_penjualan = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM penjualan"));
$count_user      = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM user"));

include 'header.php'; 
include 'sidebar.php';
?>


<div class="container-fluid px-4 py-4">

    <!-- Banner Welcome -->
    <div class="card border-0 shadow-sm rounded-3 p-4 mb-4 bg-primary text-white">
        <h4 class="fw-bold mb-1">Selamat Datang di Dashboard! 👋</h4>
        <p class="mb-0 text-white-50">Berikut adalah ringkasan data aktual dari sistem penjualan kamu.</p>
    </div>

    <!-- Card Ringkasan Statistik -->
    <div class="row g-3 mb-4">

        <!-- Total Produk -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-primary border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-secondary small fw-semibold text-uppercase">Total Produk</span>
                        <h3 class="fw-bold mb-0 mt-1"><?= $count_produk; ?></h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3">
                        <i class="bi bi-box-seam fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Pelanggan -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-success border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-secondary small fw-semibold text-uppercase">Pelanggan</span>
                        <h3 class="fw-bold mb-0 mt-1"><?= $count_pelanggan; ?></h3>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-3">
                        <i class="bi bi-people fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Penjualan -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-warning border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-secondary small fw-semibold text-uppercase">Total Transaksi</span>
                        <h3 class="fw-bold mb-0 mt-1"><?= $count_penjualan; ?></h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-3">
                        <i class="bi bi-cart-check fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total User -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-info border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-secondary small fw-semibold text-uppercase">User System</span>
                        <h3 class="fw-bold mb-0 mt-1"><?= $count_user; ?></h3>
                    </div>
                    <div class="bg-info bg-opacity-10 text-info p-3 rounded-3">
                        <i class="bi bi-person-badge fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Shortcut Menu & Informasi Tambahan -->
    <div class="row g-3">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-3 p-4 h-100">
                <h5 class="fw-bold mb-3">Akses Cepat</h5>
                <div class="d-flex flex-wrap gap-2">
                    <a href="penjualan.php" class="btn btn-primary px-3 py-2 rounded-2">
                        + Transaksi Baru
                    </a>
                    <a href="produk.php" class="btn btn-outline-secondary px-3 py-2 rounded-2">
                        Kelola Produk
                    </a>
                    <a href="pelanggan.php" class="btn btn-outline-secondary px-3 py-2 rounded-2">
                        Data Pelanggan
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-4 h-100">
                <h5 class="fw-bold mb-2">Informasi Sistem</h5>
                <p class="text-secondary small mb-0">
                    Status DB: <span class="badge bg-success">Connected</span><br>
                    Database: <strong>penjualan</strong><br>
                    Sistem Penjualan v1.0
                </p>
            </div>
        </div>
    </div>

</div>
