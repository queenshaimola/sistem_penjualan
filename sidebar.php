<?php
// Dapatkan nama file yang sedang dibuka secara otomatis (misal: pelanggan.php)
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Style khusus Animasi & Efek Hover Kotak Transparan -->
<style>
    .custom-nav-link {
        transition: all 0.25s ease-in-out;
        color: #495057;
        border-radius: 10px;
    }

    /* Efek saat kursor menempel / dipencet (Kotak Biru Transparan Soft + Animasi Geser) */
    .custom-nav-link:hover {
        background-color: rgba(13, 110, 253, 0.08) !important;
        /* Transparan soft */
        color: #7ea8e8 !important;
        transform: translateX(4px);
        /* Animasi geser ke kanan 4px */
    }

    /* Style saat menu Aktif (Halaman yang dibuka) */
    .custom-nav-link.active {
        background-color: #5d7aa5 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
        /* Bayangan soft */
    }
</style>

<div class="offcanvas offcanvas-start bg-white border-end shadow-sm" tabindex="-1" id="sidebarMenu" style="width: 270px;">

    <!-- Header Sidebar -->
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 class="offcanvas-title fw-bold d-flex align-items-center gap-2 mb-0" style="color: #1e293b;">
            <i class="bi bi-shop fs-4" style="color: #7ea8e8;"></i> Sistem Penjualan
        </h5>

        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <!-- Isi Menu Sidebar -->
    <div class="offcanvas-body d-flex flex-column p-3">
        <ul class="nav nav-pills flex-column mb-auto gap-1">
            <li class="nav-item">
                <a href="/dashboard.php" class="nav-link custom-nav-link fw-semibold px-3 py-2 <?= ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="/sidebar/pelanggan.php" class="nav-link custom-nav-link fw-semibold px-3 py-2 <?= ($current_page == 'pelanggan.php') ? 'active' : ''; ?>">
                    <i class="bi bi-people me-2"></i> Pelanggan
                </a>
            </li>
            <li>
                <a href="/sidebar/produk.php" class="nav-link custom-nav-link fw-semibold px-3 py-2 <?= ($current_page == 'produk.php') ? 'active' : ''; ?>">
                    <i class="bi bi-box-seam me-2"></i> Produk
                </a>
            </li>
            <li>
                <a href="/sidebar/penjualan.php" class="nav-link custom-nav-link fw-semibold px-3 py-2 <?= ($current_page == 'penjualan.php') ? 'active' : ''; ?>">
                    <i class="bi bi-cart3 me-2"></i> Penjualan
                </a>
            </li>
            <li>
                <a href="/sidebar/user.php" class="nav-link custom-nav-link fw-semibold px-3 py-2 <?= ($current_page == 'user.php') ? 'active' : ''; ?>">
                    <i class="bi bi-person me-2"></i> Pengguna
                </a>
            </li>
        </ul>

        <hr class="my-3 text-secondary opacity-25">

        <!-- Tombol Logout -->
        <div class="px-1 pb-2">
            <a href="/logout.php" class="btn btn-outline-danger w-100 fw-semibold rounded-3 py-2">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </a>
        </div>
    </div>
</div>