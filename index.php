<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Penjualan - SMKN 3 Kendal</title>
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" type="text/css" href="/assets/css/bootstrap.css">
</head>
<body class="bg-light min-vh-100 d-flex align-items-center justify-content-center py-4">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                
                <!-- Judul Header -->
                <div class="text-center mb-4">
                    <h3 class="fw-bold text-uppercase mb-1">Sistem Penjualan</h3>
                    <p class="text-secondary small">REKAYASA PERANGKAT LUNAK<br><strong>SMKN 3 KENDAL</strong></p>
                </div>

                <!-- Notifikasi PHP -->
                <?php
                if (isset($_GET['pesan'])) {
                    if ($_GET['pesan'] == "gagal") {
                        echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>Login gagal! Username atau password salah.</div>";
                    } elseif ($_GET['pesan'] == "logout") {
                        echo "<div class='alert alert-info alert-dismissible fade show' role='alert'>Anda telah berhasil logout!</div>";
                    } elseif ($_GET['pesan'] == "belum_login") {
                        echo "<div class='alert alert-warning alert-dismissible fade show' role='alert'>Anda harus login untuk mengakses halaman admin!</div>";
                    }
                }
                ?>

                <!-- Card Form Login -->
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4">
                        <h2><b>Masuk</b></h2>
                        <p>Masukan Username dan Password  Anda untuk melanjutkan</p>
                        <form method="post" action="login.php">
                            <div class="mb-3">
                                <label for="username" class="form-label fw-semibold">Username</label>
                                <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username" required autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">Password</label>
                                <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2 mt-2 fw-semibold">Log In</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle (Tidak lagi memerlukan jQuery) -->
    <script type="text/javascript" src="assets/js/bootstrap.bundle.js"></script>
</body>
</html>