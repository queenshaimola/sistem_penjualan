<?php
include '../koneksi.php';
include '../header.php';
include '../sidebar.php';
/** @var mysqli $koneksi */

$id = $_GET['id'];
$data = mysqli_query($koneksi, "SELECT * FROM pelanggan WHERE pelangganID='$id'");
$d = mysqli_fetch_array($data);

// Proses Update Data
if (isset($_POST['update'])) {
    $nama    = $_POST['nama_pelanggan'];
    $alamat  = $_POST['alamat'];
    $telepon = $_POST['nomor_telepon'];
    $status  = $_POST['status'];

    mysqli_query($koneksi, "UPDATE pelanggan SET nama_pelanggan='$nama', alamat='$alamat', nomor_telepon='$telepon', status='$status' WHERE pelangganID='$id'");
    echo "<script>window.location.href='pelanggan.php';</script>";
}
?>

<div class="container-fluid px-4 py-4 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="row w-100 justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 p-4">
                <h4 class="fw-bold mb-3 text-center">Edit Data Pelanggan</h4>
                
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Pelanggan</label>
                        <input type="text" name="nama_pelanggan" class="form-control" value="<?= $d['nama_pelanggan']; ?>" placeholder="Masukkan nama..." required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan alamat..." required><?= $d['alamat']; ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomor Telepon</label>
                        <input type="text" name="nomor_telepon" class="form-control" value="<?= $d['nomor_telepon']; ?>" placeholder="08xxxxxxxxxx" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="1" <?= ($d['status'] == 1 || $d['status'] == 'Aktif') ? 'selected' : ''; ?>>Aktif</option>
                            <option value="0" <?= ($d['status'] == 0 || $d['status'] == 'Tidak Aktif') ? 'selected' : ''; ?>>Tidak Aktif</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2 justify-content-end mt-4">
                        <a href="pelanggan.php" class="btn btn-outline-secondary px-4">Kembali</a>
                        <button type="submit" name="update" class="btn btn-warning px-4">Update</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>