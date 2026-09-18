<?php
include '../koneksi.php';
include '../header.php';
include '../sidebar.php';

/** @var mysqli $koneksi */

// Proses saat tombol Simpan diklik
if (isset($_POST['simpan'])) {
    $nama    = trim($_POST['nama_pelanggan']);
    $alamat  = trim($_POST['alamat']);
    $telepon = trim($_POST['nomor_telepon']);
    $status  = trim($_POST['status']);

    // Menggunakan Prepared Statement agar aman dari SQL Injection
    $stmt = mysqli_prepare($koneksi, "INSERT INTO pelanggan (nama_pelanggan, alamat, nomor_telepon, status) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $nama, $alamat, $telepon, $status);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        echo "<script>window.location.href='pelanggan.php';</script>";
        exit();
    } else {
        $error = "Gagal menyimpan data: " . mysqli_error($koneksi);
    }
}
?>

<!-- Tambah class d-flex, align-items-center, dan min-vh-100 agar posisi di tengah vertikal & horizontal -->
<div class="container-fluid px-4 py-4 d-flex justify-content-center align-items-center style=" min-height: 80vh;"">
    <div class="row w-100 justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 p-4">
                <h4 class="fw-bold mb-3 text-center">Tambah Data Pelanggan</h4>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger mb-3"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Pelanggan</label>
                        <input type="text" name="nama_pelanggan" class="form-control" placeholder="Masukkan nama..." required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan alamat..." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomor Telepon</label>
                        <input type="tel" name="nomor_telepon" class="form-control" placeholder="08xxxxxxxxxx" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select" required>
                            <!-- Ganti value menjadi angka 1 dan 0 -->
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2 justify-content-end mt-4">
                        <a href="pelanggan.php" class="btn btn-outline-secondary px-4">Kembali</a>
                        <button type="submit" name="simpan" class="btn btn-primary px-4">Simpan</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>