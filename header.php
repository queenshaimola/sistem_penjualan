<?php 
session_start();

if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:index.php?pesan=belum_login");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Penjualan</title>
    <link rel="stylesheet" href="/assets/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">

    <div class="d-flex">
        
        <!-- Panggil Sidebar Offcanvas -->
        <?php include 'sidebar.php'; ?>

        <!-- Konten Sebelah Kanan -->
        <div class="flex-grow-1">
            
            <!-- Navbar Topbar -->
            <nav class="navbar bg-white border-bottom py-3 px-4 mb-4 shadow-sm">
                <div class="container-fluid p-0 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-light border-0 p-2 rounded-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
                            <i class="bi bi-three-dots-vertical fs-4 text-dark"></i>
                        </button>
                        <span class="fs-4 fw-bold text-dark mb-0">Sistem Penjualan</span>
                    </div>
                    <div>
                        <button class="btn btn-outline-secondary px-3 py-2 rounded-3 fs-6">
                            Halo, <?= htmlspecialchars($_SESSION['nama_user'] ?? $_SESSION['username'] ?? 'User'); ?>
                        </button>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>