<?php
include '../koneksi.php';
include '../header.php';
include '../sidebar.php';
/** @var mysqli $koneksi */

$id = $_GET['id'];
$data = mysqli_query($koneksi, "SELECT * FROM produk WHERE produkID='$id'");
$d = mysqli_fetch_array($data);

// Proses Update Data
if (isset($_POST['update'])) {
    $nama  = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $stok  = $_POST['stok'];

    mysqli_query($koneksi, "UPDATE produk SET nama_produk='$nama', harga='$harga', stok='$stok' WHERE produkID='$id'");
    echo "<script>window.location.href='produk.php';</script>";
}
?>

<div class="container-fluid px-4 py-4 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="row w-100 justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 p-4">
                <h4 class="fw-bold mb-3 text-center">Edit Data Produk</h4>
                
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Produk</label>
                        <input type="text" name="nama_produk" class="form-control" value="<?= $d['nama_produk']; ?>" placeholder="Masukkan nama produk..." required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Harga (Rp)</label>
                        <input type="number" name="harga" class="form-control" value="<?= $d['harga']; ?>" placeholder="Masukkan harga..." required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Stok</label>
                        <input type="number" name="stok" class="form-control" value="<?= $d['stok']; ?>" placeholder="Masukkan jumlah stok..." required>
                    </div>

                    <div class="d-flex gap-2 justify-content-end mt-4">
                        <a href="produk.php" class="btn btn-outline-secondary px-4">Kembali</a>
                        <button type="submit" name="update" class="btn btn-warning px-4">Update</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>