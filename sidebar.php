<!-- sidebar.php -->
<div class="offcanvas offcanvas-start bg-white border-end shadow-sm" tabindex="-1" id="sidebarMenu" style="width: 260px;">
    
    <!-- Header Sidebar (Judul + Tombol Close X) -->
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold">
            <i class="bi bi-shop me-2 text-primary"></i>Sistem Penjualan
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <!-- Isi Menu Sidebar -->
    <div class="offcanvas-body d-flex flex-column p-3">
        
        <!-- Navigasi Menu -->
        <ul class="nav nav-pills flex-column mb-auto gap-1">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link active fw-semibold">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="/sidebar/pelanggan.php" class="nav-link text-dark fw-semibold">
                    <i class="bi bi-people me-2"></i> Pelanggan
                </a>
            </li>
            <li>
                <a href="/sidebar/produk.php" class="nav-link text-dark fw-semibold">
                    <i class="bi bi-box-seam me-2"></i> Produk
                </a>
            </li>
            <li>
                <a href="/sidebar/penjualan.php" class="nav-link text-dark fw-semibold">
                    <i class="bi bi-cart3 me-2"></i> Penjualan
                </a>
            </li>
            <li>
                <a href="/sidebar/user.php" class="nav-link text-dark fw-semibold">
                    <i class="bi bi-person me-2"></i> Pengguna
                </a>
            </li>
        </ul>

        <hr class="my-3">

        <!-- Tombol Logout -->
        <div class="px-2 pb-2">
            <a href="logout.php" class="btn btn-outline-danger w-100 fw-semibold">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </a>
        </div>

    </div>
</div>