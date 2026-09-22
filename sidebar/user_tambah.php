<?php 
include '../koneksi.php'; 
include '../header.php'; 
include '../sidebar.php';

/** @var mysqli $koneksi */

if (isset($_POST['simpan'])) {
    $username  = mysqli_real_escape_string($koneksi, $_POST['username']);
    // Password di-hash menggunakan MD5 (bisa diganti password_hash)
    $password  = md5($_POST['password']); 
    $nama_user = mysqli_real_escape_string($koneksi, $_POST['nama_user']);
    $akses     = mysqli_real_escape_string($koneksi, $_POST['akses']);

    // Cek apakah username sudah ada
    $cek_user = mysqli_query($koneksi, "SELECT * FROM user WHERE username = '$username'");
    if (mysqli_num_rows($cek_user) > 0) {
        echo "<script>alert('Username sudah digunakan!'); window.history.back();</script>";
        exit();
    }

    // Query SIMPAN data user baru
    $query = "INSERT INTO user (username, password, nama_user, akses) 
              VALUES ('$username', '$password', '$nama_user', '$akses')";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('User berhasil ditambahkan!'); window.location.href='user.php';</script>";
        exit();
    } else {
        echo "<script>alert('Gagal menambah user: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>

<div class="container py-4 col-md-6">
    <div class="card p-4 shadow-sm border-0 rounded-3">
        <h4 class="text-center mb-3 fw-bold">Tambah User Baru</h4>

        <form method="POST">
            <!-- Nama Lengkap / Nama User -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Lengkap</label>
                <input type="text" name="nama_user" class="form-control" placeholder="Masukkan nama user" required>
            </div>

            <!-- Username -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
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
                    <option value="1">1 - Admin</option>
                    <option value="2">2 - Petugas / Kasir</option>
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