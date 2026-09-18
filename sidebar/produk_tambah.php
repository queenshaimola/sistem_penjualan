<?php
include '../koneksi.php';
include '../header.php';
include '../sidebar.php';
/** @var mysqli $koneksi */

// Proses Simpan Data
if (isset($_POST['simpan'])) {
    $nama  = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $stok  = $_POST['stok'];

    mysqli_query($koneksi, "INSERT INTO produk (nama_produk, harga, stok) VALUES ('$nama', '$harga', '$stok')");
    echo "<script>window.location.href='produk.php';</script>";
}
?>

<div class="container-fluid px-4 py-4 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="row w-100 justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 p-4">
                <h4 class="fw-bold mb-3 text-center">Tambah Data Produk</h4>
                
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Produk</label>
                        <input type="text" name="nama_produk" class="form-control" placeholder="Masukkan nama produk..." required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Harga (Rp)</label>
                        <input type="number" name="harga" class="form-control" placeholder="Masukkan harga..." required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Stok</label>
                        <input type="number" name="stok" class="form-control" placeholder="Masukkan jumlah stok..." required>
                    </div>

                    <div class="d-flex gap-2 justify-content-end mt-4">
                        <a href="produk.php" class="btn btn-outline-secondary px-4">Kembali</a>
                        <button type="submit" name="simpan" class="btn btn-primary px-4">Simpan</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>