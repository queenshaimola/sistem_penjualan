<?php 
include '../koneksi.php'; 
include '../header.php'; 

/** @var mysqli $koneksi */

// Proses Simpan Data Penjualan
if (isset($_POST['simpan'])) {
    $tanggal_penjualan = $_POST['tanggal_penjualan'];
    $id_pelanggan      = $_POST['id_pelanggan'];
    $id_produk         = $_POST['id_produk'];
    $jumlah            = $_POST['jumlah'];

    // Ambil harga produk dari DB untuk menghitung total harga
    $query_harga = mysqli_query($koneksi, "SELECT harga FROM produk WHERE id_produk = '$id_produk'");
    $data_produk = mysqli_fetch_assoc($query_harga);
    $harga       = $data_produk['harga'] ?? 0;
    $total_harga = $harga * $jumlah;

    // Insert ke tabel penjualan
    $query_insert = "INSERT INTO penjualan (tanggal_penjualan, id_pelanggan, id_produk, jumlah, total_harga) 
                     VALUES ('$tanggal_penjualan', '$id_pelanggan', '$id_produk', '$jumlah', '$total_harga')";
    
    if (mysqli_query($koneksi, $query_insert)) {
        echo "<script>alert('Transaksi berhasil ditambahkan!'); window.location='penjualan.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>

<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h5 class="fw-bold mb-0">Tambah Transaksi Penjualan</h5>
                </div>
                <div class="card-body p-4">
                    <form action="" method="POST">
                        
                        <!-- Tanggal Penjualan -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Transaksi</label>
                            <input type="date" name="tanggal_penjualan" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                        </div>

                        <!-- Pilih Pelanggan -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pelanggan</label>
                            <select name="id_pelanggan" class="form-select" required>
                                <option value="">-- Pilih Pelanggan --</option>
                                <?php
                                $get_pelanggan = mysqli_query($koneksi, "SELECT * FROM pelanggan");
                                while ($p = mysqli_fetch_assoc($get_pelanggan)) {
                                    echo "<option value='".$p['id_pelanggan']."'>".$p['nama_pelanggan']."</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Pilih Produk -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Produk</label>
                            <select name="id_produk" id="id_produk" class="form-select" onchange="hitungTotal()" required>
                                <option value="" data-harga="0">-- Pilih Produk --</option>
                                <?php
                                $get_produk = mysqli_query($koneksi, "SELECT * FROM produk");
                                while ($pr = mysqli_fetch_assoc($get_produk)) {
                                    echo "<option value='".$pr['id_produk']."' data-harga='".$pr['harga']."'>".$pr['nama_produk']." - Rp ".number_format($pr['harga'], 0, ',', '.')."</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Jumlah Beli -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jumlah (Qty)</label>
                            <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" value="1" oninput="hitungTotal()" required>
                        </div>

                        <!-- Total Harga (Otomatis) -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Total Harga</label>
                            <input type="text" id="total_harga_display" class="form-control bg-light fw-bold text-primary" value="Rp 0" readonly>
                        </div>

                        <!-- Tombol Action -->
                        <div class="d-flex gap-2">
                            <button type="submit" name="simpan" class="btn btn-primary px-4">Simpan Transaksi</button>
                            <a href="penjualan.php" class="btn btn-outline-secondary px-4">Batal</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script Hitung Total Otomatis -->
<script>
function hitungTotal() {
    const selectProduk = document.getElementById('id_produk');
    const selectedOption = selectProduk.options[selectProduk.selectedIndex];
    const harga = parseFloat(selectedOption.getAttribute('data-harga')) || 0;
    const jumlah = parseInt(document.getElementById('jumlah').value) || 0;

    const total = harga * jumlah;
    document.getElementById('total_harga_display').value = 'Rp ' + total.toLocaleString('id-ID');
}
</script>
