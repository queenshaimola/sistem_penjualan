<?php 
include '../koneksi.php'; 
include '../header.php'; 
include '../sidebar.php';

/** @var mysqli $koneksi */

// Ambil ID User dari URL
$id = $_GET['id'] ?? '';

// Ambil data user berdasarkan ID
$query_get = mysqli_query($koneksi, "SELECT * FROM user WHERE userID = '$id'");
$d = mysqli_fetch_assoc($query_get);

if (!$d) {
    echo "<script>alert('Data user tidak ditemukan!'); window.location.href='user.php';</script>";
    exit();
}

if (isset($_POST['update'])) {
    $username  = mysqli_real_escape_string($koneksi, $_POST['username']);
    $nama_user = mysqli_real_escape_string($koneksi, $_POST['nama_user']);
    $akses     = mysqli_real_escape_string($koneksi, $_POST['akses']);
    $password  = $_POST['password'];

    // Cek apakah username dipakai user lain
    $cek_username = mysqli_query($koneksi, "SELECT * FROM user WHERE username = '$username' AND userID != '$id'");
    if (mysqli_num_rows($cek_username) > 0) {
        echo "<script>alert('Username sudah digunakan oleh user lain!'); window.history.back();</script>";
        exit();
    }

    // Jika password diisi, update password baru dengan HASH MD5
    if (!empty($password)) {
        $password_hash = md5($password);
        $query = "UPDATE user SET 
                    nama_user = '$nama_user', 
                    username = '$username', 
                    password = '$password_hash', 
                    akses = '$akses' 
                  WHERE userID = '$id'";
    } else {
        // Jika password dikosongkan, jangan ubah passwordnya
        $query = "UPDATE user SET 
                    nama_user = '$nama_user', 
                    username = '$username', 
                    akses = '$akses' 
                  WHERE userID = '$id'";
    }

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data user berhasil diperbarui!'); window.location.href='user.php';</script>";
        exit();
    } else {
        echo "<script>alert('Gagal mengupdate user: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>

<div class="container py-4 col-md-6">
    <div class="card p-4 shadow-sm border-0 rounded-3">
        <h4 class="text-center mb-3 fw-bold">Edit Data User</h4>

        <form method="POST">
            <!-- Nama Lengkap -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Lengkap</label>
                <input type="text" name="nama_user" class="form-control" value="<?= htmlspecialchars($d['nama_user']); ?>" required>
            </div>

            <!-- Username -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Username</label>
                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($d['username']); ?>" required>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Password Baru <small class="text-muted fw-normal">(Kosongkan jika tidak ingin diubah)</small></label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan password baru">
            </div>

            <!-- Hak Akses -->
            <div class="mb-4">
                <label class="form-label fw-semibold">Hak Akses / Level</label>
                <select name="akses" class="form-select" required>
                    <option value="1" <?= $d['akses'] == '1' ? 'selected' : ''; ?>>1 - Admin</option>
                    <option value="2" <?= $d['akses'] == '2' ? 'selected' : ''; ?>>2 - Petugas / Kasir</option>
                </select>
            </div>

            <!-- Tombol Berdampingan -->
            <div class="d-flex flex-row gap-2 justify-content-end align-items-center">
                <a href="user.php" class="btn btn-secondary px-4">Batal</a>
                <button type="submit" name="update" class="btn btn-primary px-4">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>