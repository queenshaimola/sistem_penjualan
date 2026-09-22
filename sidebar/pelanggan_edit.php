<?php

include '../koneksi.php';
include '../header.php';
include '../sidebar.php';

/** @var mysqli $koneksi */

// Ambil dan bersihkan ID dari URL
$id = isset($_GET['id']) ? mysqli_real_escape_string($koneksi, $_GET['id']) : '';

// Query data pelanggan
$data = mysqli_query($koneksi, "SELECT * FROM pelanggan WHERE pelangganID='$id'");
$d = mysqli_fetch_array($data);

// Jika ID tidak ditemukan di database
if (!$d) {
    echo "<script>alert('Data pelanggan tidak ditemukan!'); window.location.href='pelanggan.php';</script>";
    exit();
}

$error = '';

// Proses Update Data
if (isset($_POST['update'])) {
    $nama    = trim($_POST['nama_pelanggan']);
    $alamat  = trim($_POST['alamat']);
    $telepon = trim($_POST['nomor_telepon']);
    $status  = trim($_POST['status']);

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
        // 2. Cek Apakah Nomor Telepon Digunakan Oleh Pelanggan Lain
        $stmt_cek = mysqli_prepare($koneksi, "SELECT pelangganID FROM pelanggan WHERE nomor_telepon = ? AND pelangganID != ?");
        mysqli_stmt_bind_param($stmt_cek, "ss", $telepon, $id);
        mysqli_stmt_execute($stmt_cek);
        mysqli_stmt_store_result($stmt_cek);

        if (mysqli_stmt_num_rows($stmt_cek) > 0) {
            $error = "Nomor telepon <strong>" . htmlspecialchars($telepon) . "</strong> sudah digunakan oleh pelanggan lain!";
        }
        mysqli_stmt_close($stmt_cek);
    }

    // Jika tidak ada error, eksekusi query update
    if (empty($error)) {
        $stmt = mysqli_prepare($koneksi, "UPDATE pelanggan SET nama_pelanggan=?, alamat=?, nomor_telepon=?, status=? WHERE pelangganID=?");
        mysqli_stmt_bind_param($stmt, "sssss", $nama, $alamat, $telepon, $status, $id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            echo "<script>alert('Data pelanggan berhasil diperbarui!'); window.location.href='pelanggan.php';</script>";
            exit();
        } else {
            $error = "Gagal memperbarui data: " . mysqli_error($koneksi);
        }
    }
}

// Menentukan nilai input (menggunakan data dari POST jika ada error, atau data dari DB)
$val_nama    = isset($_POST['nama_pelanggan']) ? $_POST['nama_pelanggan'] : $d['nama_pelanggan'];
$val_alamat  = isset($_POST['alamat']) ? $_POST['alamat'] : $d['alamat'];
$val_telepon = isset($_POST['nomor_telepon']) ? $_POST['nomor_telepon'] : $d['nomor_telepon'];
$val_status  = isset($_POST['status']) ? $_POST['status'] : $d['status'];
?>

<div class="container-fluid px-4 py-4 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="row w-100 justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 p-4">
                <h4 class="fw-bold mb-3 text-center">Edit Data Pelanggan</h4>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger mb-3 alert-dismissible fade show" role="alert">
                        <?= $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Pelanggan</label>
                        <input type="text" name="nama_pelanggan" class="form-control" value="<?= htmlspecialchars($val_nama); ?>" placeholder="Masukkan nama..." required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan alamat..." required><?= htmlspecialchars($val_alamat); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomor Telepon <small class="text-muted">(10-13 digit)</small></label>
                        <input type="tel" name="nomor_telepon" id="nomor_telepon" class="form-control" value="<?= htmlspecialchars($val_telepon); ?>" placeholder="08xxxxxxxxxx" maxlength="13" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="1" <?= ($val_status == 1 || $val_status == 'Aktif') ? 'selected' : ''; ?>>Aktif</option>
                            <option value="0" <?= ($val_status == 0 || $val_status == 'Tidak Aktif') ? 'selected' : ''; ?>>Tidak Aktif</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2 justify-content-end mt-4">
                        <a href="pelanggan.php" class="btn btn-outline-secondary px-4">Kembali</a>
                        <button type="submit" name="update" class="btn btn-warning px-4 text-white fw-semibold">Update</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>