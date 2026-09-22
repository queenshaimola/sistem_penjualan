<?php 
include '../koneksi.php'; 
include '../header.php'; 
include '../sidebar.php';

/** @var mysqli $koneksi */

$error = '';

if (isset($_POST['simpan'])) {
    $username  = trim($_POST['username']);
    $nama_user = trim($_POST['nama_user']);
    $akses     = trim($_POST['akses']);
    $password_raw = $_POST['password'];

    // Sanitasi input
    $username_clean  = mysqli_real_escape_string($koneksi, $username);
    $nama_user_clean = mysqli_real_escape_string($koneksi, $nama_user);
    $akses_clean     = mysqli_real_escape_string($koneksi, $akses);

    // -------------------------------------------------------------
    // VALIDASI SISI SERVER (PHP)
    // -------------------------------------------------------------

    // 1. Cek Username Ganda / Sudah Digunakan
    $cek_user = mysqli_query($koneksi, "SELECT * FROM user WHERE username = '$username_clean'");
    
    if (mysqli_num_rows($cek_user) > 0) {
        $error = "Username '<strong>" . htmlspecialchars($username) . "</strong>' sudah digunakan! Silakan gunakan username lain.";
    } elseif (strlen($username) < 3) {
        $error = "Username terlalu pendek (minimal 3 karakter)!";
    } elseif (strlen($password_raw) < 4) {
        $error = "Password terlalu pendek (minimal 4 karakter)!";
    }

    // Jika tidak ada error, lakukan proses simpan
    if (empty($error)) {
        // Enkripsi Password dengan MD5
        $password_hash = md5($password_raw); 

        $query = "INSERT INTO user (username, password, nama_user, akses) 
                  VALUES ('$username_clean', '$password_hash', '$nama_user_clean', '$akses_clean')";

        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('User berhasil ditambahkan!'); window.location.href='user.php';</script>";
            exit();
        } else {
            $error = "Gagal menambah user: " . mysqli_error($koneksi);
        }
    }
}
?>

<div class="container py-4 col-md-6">
    <div class="card p-4 shadow-sm border-0 rounded-3">
        <h4 class="text-center mb-3 fw-bold">Tambah User Baru</h4>

        <!-- Alert Pesan Error jika Username Sama -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger mb-3 alert-dismissible fade show" role="alert">
                <?= $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="POST">
            <!-- Nama Lengkap / Nama User -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Lengkap</label>
                <input type="text" name="nama_user" class="form-control" placeholder="Masukkan nama user" value="<?= isset($_POST['nama_user']) ? htmlspecialchars($_POST['nama_user']) : ''; ?>" required>
            </div>

            <!-- Username -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
            </div>

            <!-- Hak Akses -->
            <div class="mb-4">
                <label class="form-label fw-semibold">Hak Akses / Level</label>
                <select name="akses" class="form-select" required>
                    <option value="">-- Pilih Level Akses --</option>
                    <option value="1" <?= (isset($_POST['akses']) && $_POST['akses'] == '1') ? 'selected' : ''; ?>>1 - Admin</option>
                    <option value="2" <?= (isset($_POST['akses']) && $_POST['akses'] == '2') ? 'selected' : ''; ?>>2 - Petugas / Kasir</option>
                </select>
            </div>

            <!-- Tombol Berdampingan -->
            <div class="d-flex flex-row gap-2 justify-content-end align-items-center">
                <a href="user.php" class="btn btn-secondary px-4">Batal</a>
                <button type="submit" name="simpan" class="btn btn-primary px-4">Simpan User</button>
            </div>
        </form>
    </div>
</div>