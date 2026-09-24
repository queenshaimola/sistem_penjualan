<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
$akses        = $_SESSION['akses'] ?? '';

// Hak akses: Admin (nilai 1 atau 'admin'), Kasir/Petugas (nilai selain itu)
$is_admin = ($akses == '1' || strtolower((string)$akses) === 'admin');

// Tentukan prefix folder berdasarkan posisi file yang sedang diakses
$in_sidebar_folder = (basename(dirname($_SERVER['SCRIPT_NAME'])) === 'sidebar');
$path_to_root     = $in_sidebar_folder ? '../' : './';
$path_to_sidebar  = $in_sidebar_folder ? './' : 'sidebar/';
?>

<!-- Style khusus Animasi & Efek Hover -->
<style>
    .custom-nav-link {
        transition: all 0.25s ease-in-out;
        color: #495057;
        border-radius: 10px;
    }

    .custom-nav-link:hover {
        background-color: rgba(127, 176, 251, 0.25);
        color: #6180ad;
        transform: translateX(4px);
    }

    .custom-nav-link.active {
        background-color: #436dc0 !important;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(11, 67, 151, 0.25);
    }
</style>

<div class="offcanvas offcanvas-start bg-white border-end shadow-sm" tabindex="-1" id="sidebarMenu" style="width: 270px;">

    <!-- Header Sidebar -->
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold">
            <i class="bi bi-shop text-primary me-2"></i>Sistem Penjualan
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <!-- Isi Menu Sidebar -->
    <div class="offcanvas-body d-flex flex-column p-3">
        <ul class="nav nav-pills flex-column mb-auto gap-1">

            <!-- Dashboard (Root Folder) -->
            <li>
                <a href="<?= $path_to_root; ?>dashboard.php" class="nav-link custom-nav-link fw-semibold <?= ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>

                <!-- MENU KHUSUS ADMIN (Folder sidebar/) -->
                <li>
                    <a href="<?= $path_to_sidebar; ?>pelanggan.php" class="nav-link custom-nav-link fw-semibold <?= ($current_page == 'pelanggan.php') ? 'active' : ''; ?>">
                        <i class="bi bi-people me-2"></i> Pelanggan
                    </a>
                </li>
            <?php if ($is_admin): ?>
                <li>
                    <a href="<?= $path_to_sidebar; ?>produk.php" class="nav-link custom-nav-link fw-semibold px-3 py-2 <?= ($current_page == 'produk.php') ? 'active' : ''; ?>">
                        <i class="bi bi-box-seam me-2"></i> Produk
                    </a>
                </li>
            <?php endif; ?>

            
            <!-- MENU PENJUALAN (Folder sidebar/) -->
            <li>
                <a href="<?= $path_to_sidebar; ?>penjualan.php" class="nav-link custom-nav-link fw-semibold px-3 py-2 <?= ($current_page == 'penjualan.php') ? 'active' : ''; ?>">
                    <i class="bi bi-cart3 me-2"></i> Penjualan
                </a>
            </li>

            <?php if ($is_admin): ?>
            <li>
                <a href="<?= $path_to_sidebar; ?>diskon.php" class="nav-link custom-nav-link fw-semibold px-3 py-2 <?= ($current_page == 'diskon.php') ? 'active' : ''; ?>">
                    <i class="bi bi-tags me-2"></i> Kelola Voucher
                </a>
            </li>
            <?php endif; ?>

            <?php if ($is_admin): ?>
                <!-- MENU PENGGUNA/USER (Folder sidebar/) -->
                <li>
                    <a href="<?= $path_to_sidebar; ?>user.php" class="nav-link custom-nav-link fw-semibold px-3 py-2 <?= ($current_page == 'user.php') ? 'active' : ''; ?>">
                        <i class="bi bi-person me-2"></i> Pengguna
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($is_admin): ?>
            <li>
                <a href="<?= $path_to_sidebar; ?>laporan.php" class="nav-link custom-nav-link fw-semibold px-3 py-2 <?= ($current_page == 'laporan.php') ? 'active' : ''; ?>">
                    <i class="bi bi-file-earmark-bar-graph"></i> Laporan
                </a>
            </li>
            <?php endif; ?>

        </ul>

        <hr class="my-3 text-secondary opacity-25">

        <!-- Tombol Logout (Root Folder) -->
        <div class="px-1 pb-2">
            <a href="<?= $path_to_root; ?>logout.php" class="btn btn-outline-danger w-100 fw-semibold rounded-3 py-2">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </a>
        </div>
    </div>
</div>