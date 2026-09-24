<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/koneksi.php';

/** @var mysqli $koneksi */

// Tanggal hari ini
$today = date('Y-m-d');

// Query untuk menghitung jumlah baris di masing-masing tabel
$count_produk    = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM produk"));
$count_pelanggan = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM pelanggan"));
$count_penjualan = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM penjualan"));
$count_user      = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM user"));

// Query Data Penjualan Hari Ini Saja
$query_today = "SELECT penjualan.*, 
                       pelanggan.nama_pelanggan, 
                       IFNULL(user.nama_user, 'Admin') AS nama_kasir 
                FROM penjualan 
                LEFT JOIN pelanggan ON penjualan.pelangganID = pelanggan.pelangganID 
                LEFT JOIN user ON penjualan.userID = user.userID
                WHERE DATE(penjualan.tanggal_penjualan) = '$today'
                ORDER BY penjualan.penjualanID DESC";

$data_today = mysqli_query($koneksi, $query_today);

// Hitung total pendapatan hari ini
$q_income_today = mysqli_query($koneksi, "SELECT SUM(total_harga) AS total FROM penjualan WHERE DATE(tanggal_penjualan) = '$today'");
$d_income_today = mysqli_fetch_assoc($q_income_today);
$total_income_today = $d_income_today['total'] ?? 0;

// Cukup panggil header.php saja (sidebar sudah di-include otomatis di dalam header.php)
include __DIR__ . '/header.php'; 
?>

<!-- CSS Tambahan untuk Animasi Hover Card -->
<style>
    .card-hover {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }
    .card-hover:hover .bi {
        transform: scale(1.1);
        transition: transform 0.3s ease;
    }
</style>

<div class="container-fluid px-4 py-4">

    <!-- Banner Welcome -->
    <div class="card border-0 shadow-sm rounded-3 p-4 mb-4 bg-primary text-white card-hover">
        <h4 class="fw-bold mb-1">Selamat Datang di Dashboard! 👋</h4>
        <p class="mb-0 text-white-50">Berikut adalah ringkasan data aktual dari sistem penjualan kamu.</p>
    </div>

    <!-- Card Ringkasan Statistik -->
    <div class="row g-3 mb-4">

        <!-- Total Produk -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-primary border-4 card-hover">
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
            <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-success border-4 card-hover">
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
            <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-warning border-4 card-hover">
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
            <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-info border-4 card-hover">
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
    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-3 p-4 h-100 card-hover">
                <h5 class="fw-bold mb-3">Akses Cepat</h5>
                <div class="d-flex flex-wrap gap-2">
                    <a href="sidebar/penjualan_tambah.php" class="btn btn-primary px-3 py-2 rounded-2">
                        <i class="bi bi-plus-circle me-1"></i> Transaksi Baru
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-4 h-100 card-hover">
                <h5 class="fw-bold mb-2">Informasi Sistem</h5>
                <p class="text-secondary small mb-0">
                    Status DB: <span class="badge bg-success">Connected</span><br>
                    Database: <strong>penjualan</strong><br>
                    Sistem Penjualan v1.0
                </p>
            </div>
        </div>
    </div>

    <!-- TABEL PENJUALAN HARI INI -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden card-hover">
        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom-0">
            <div>
                <h5 class="fw-bold text-dark mb-0">
                    <i class="bi bi-clock-history text-primary me-2"></i>Transaksi Hari Ini 
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill fs-7 ms-1">
                        <?= date('d M Y'); ?>
                    </span>
                </h5>
            </div>
            <div>
                <span class="text-muted small me-1">Total Pendapatan Hari Ini:</span>
                <span class="fw-bold text-success fs-6">Rp <?= number_format($total_income_today, 0, ',', '.'); ?></span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary border-bottom">
                        <tr>
                            <th class="ps-4 py-3" width="5%">No</th>
                            <th class="py-3">ID Transaksi</th>
                            <th class="py-3">Jam</th>
                            <th class="py-3">Pelanggan</th>
                            <th class="py-3">Kasir</th>
                            <th class="py-3">Metode</th>
                            <th class="py-3">Total Harga</th>
                            <th class="text-center py-3" width="12%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        if ($data_today && mysqli_num_rows($data_today) > 0) {
                            while ($row = mysqli_fetch_array($data_today)) {
                        ?>
                                <tr>
                                    <td class="ps-4 fw-medium text-secondary"><?= $no++; ?></td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-2 py-1 fw-normal">
                                            #<?= $row['penjualanID']; ?>
                                        </span>
                                    </td>
                                    <td class="text-secondary">
                                        <?= date('H:i', strtotime($row['tanggal_penjualan'])); ?> WIB
                                    </td>
                                    <td class="fw-semibold text-dark">
                                        <?= htmlspecialchars($row['nama_pelanggan'] ?? 'Umum'); ?>
                                    </td>
                                    <td class="text-secondary">
                                        <?= htmlspecialchars($row['nama_kasir'] ?? 'Admin'); ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                            <?= htmlspecialchars($row['metode_pembayaran'] ?? 'Cash'); ?>
                                        </span>
                                    </td>
                                    <td class="fw-semibold text-success">
                                        Rp <?= number_format($row['total_harga'] ?? 0, 0, ',', '.'); ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="sidebar/penjualan_detail.php?id=<?= $row['penjualanID']; ?>" class="btn btn-sm btn-light text-primary border rounded-2 px-2 py-1">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo '<tr><td colspan="8" class="text-center py-4 text-muted"><i class="bi bi-inbox me-1"></i> Belum ada transaksi penjualan untuk hari ini.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>