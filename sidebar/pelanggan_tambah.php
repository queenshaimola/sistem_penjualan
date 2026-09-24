<?php
include '../koneksi.php';
include '../header.php';
include '../sidebar.php';

/** @var mysqli $koneksi */

$error = '';

// Proses saat tombol Simpan diklik
if (isset($_POST['simpan'])) {
    $nama    = trim($_POST['nama_pelanggan']);
    $alamat  = trim($_POST['alamat']);
    $telepon = trim($_POST['nomor_telepon']);
    // Status otomatis 1 (Aktif) untuk pelanggan baru
    $status  = 1; 

    // -------------------------------------------------------------
    // VALIDASI SISI SERVER (PHP)
    // -------------------------------------------------------------
    
    // 1. Validasi Format & Panjang Nomor Telepon
    if (!ctype_digit($telepon)) {
        $error = "Nomor telepon hanya boleh berisi angka!";
    } elseif (strlen($telepon) > 13) {
        $error = "Nomor telepon tidak boleh lebih dari 13 digit!";
    } elseif (strlen($telepon) < 10) {
        $error = "Nomor telepon terlalu pendek (minimal 10 digit)!";
    } else {
        // 2. Cek Apakah Nomor Telepon Sudah Terdaftar
        $stmt_cek = mysqli_prepare($koneksi, "SELECT pelangganID FROM pelanggan WHERE nomor_telepon = ?");
        mysqli_stmt_bind_param($stmt_cek, "s", $telepon);
        mysqli_stmt_execute($stmt_cek);
        mysqli_stmt_store_result($stmt_cek);

        if (mysqli_stmt_num_rows($stmt_cek) > 0) {
            $error = "Nomor telepon <strong>" . htmlspecialchars($telepon) . "</strong> sudah terdaftar pada pelanggan lain!";
        }
        mysqli_stmt_close($stmt_cek);
    }

    // Jika tidak ada error validasi, jalankan simpan data
    if (empty($error)) {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO pelanggan (nama_pelanggan, alamat, nomor_telepon, status) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssi", $nama, $alamat, $telepon, $status);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            echo "<script>alert('Data pelanggan berhasil ditambahkan!'); window.location.href='pelanggan.php';</script>";
            exit();
        } else {
            $error = "Gagal menyimpan data: " . mysqli_error($koneksi);
        }
    }
}
?>

<div class="container-fluid px-4 py-4 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="row w-100 justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 p-4">
                <h4 class="fw-bold mb-3 text-center">Tambah Data Pelanggan</h4>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger mb-3 alert-dismissible fade show" role="alert">
                        <?= $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Pelanggan</label>
                        <input type="text" name="nama_pelanggan" class="form-control" placeholder="Masukkan nama..." value="<?= isset($_POST['nama_pelanggan']) ? htmlspecialchars($_POST['nama_pelanggan']) : ''; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan alamat..." required><?= isset($_POST['alamat']) ? htmlspecialchars($_POST['alamat']) : ''; ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomor Telepon <small class="text-muted">(10-13 digit)</small></label>
                        <input type="tel" name="nomor_telepon" id="nomor_telepon" class="form-control" placeholder="08xxxxxxxxxx" maxlength="13" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '');" value="<?= isset($_POST['nomor_telepon']) ? htmlspecialchars($_POST['nomor_telepon']) : ''; ?>" required>
                    </div>

                    <!-- Status Otomatis & Dikunci -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status <small class="text-muted">(Otomatis System)</small></label>
                        <select class="form-select bg-light" disabled>
                            <option value="1" selected>Aktif</option>
                        </select>
                        <small class="text-muted d-block mt-1">*Pelanggan baru otomatis berstatus Aktif.</small>
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