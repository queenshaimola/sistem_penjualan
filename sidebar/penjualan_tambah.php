<?php 
include '../koneksi.php'; 
include '../header.php'; 
include '../sidebar.php';

/** @var mysqli $koneksi */
date_default_timezone_set('Asia/Jakarta');

if (isset($_POST['simpan'])) {
    // Gabungkan tanggal input dengan jam saat ini
    $tanggal_penjualan = $_POST['tanggal_penjualan'] . ' ' . date('H:i:s');
    $pelangganID        = !empty($_POST['pelangganID']) ? "'".$_POST['pelangganID']."'" : "NULL";
    $nama_produk        = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $total_harga        = $_POST['total_harga'];

    // Simpan data lengkap ke database
    $query_insert = "INSERT INTO penjualan (tanggal_penjualan, total_harga, nama_produk, pelangganID) 
                     VALUES ('$tanggal_penjualan', '$total_harga', '$nama_produk', $pelangganID)";
    
    if (mysqli_query($koneksi, $query_insert)) {
        echo "<script>alert('Transaksi Berhasil!'); window.location.href='penjualan.php';</script>";
        exit();
    }
}
?>

<div class="container-fluid px-4 py-4 d-flex justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-3 p-4">
            <h4 class="fw-bold mb-3 text-center">Tambah Transaksi Penjualan</h4>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tanggal Transaksi</label>
                    <input type="date" name="tanggal_penjualan" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Pelanggan</label>
                    <select name="pelangganID" class="form-select">
                        <option value="">-- Umum / Non-Member --</option>
                        <?php
                        $get_pelanggan = mysqli_query($koneksi, "SELECT * FROM pelanggan ORDER BY nama_pelanggan ASC");
                        while ($p = mysqli_fetch_assoc($get_pelanggan)) {
                            echo "<option value='".$p['pelangganID']."'>Member: ".$p['nama_pelanggan']."</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Produk</label>
                    <select name="nama_produk" id="produkID" class="form-select" onchange="hitungTotal()" required>
                        <option value="" data-harga="0">-- Pilih Produk --</option>
                        <?php
                        $get_produk = mysqli_query($koneksi, "SELECT * FROM produk ORDER BY nama_produk ASC");
                        while ($pr = mysqli_fetch_assoc($get_produk)) {
                            echo "<option value='".htmlspecialchars($pr['nama_produk'])."' data-harga='".$pr['harga']."'>".$pr['nama_produk']." - Rp ".number_format($pr['harga'], 0, ',', '.')."</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Jumlah (Qty)</label>
                    <input type="number" id="jumlah" class="form-control" min="1" value="1" oninput="hitungTotal()" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Total Harga</label>
                    <input type="hidden" name="total_harga" id="total_harga" value="0">
                    <input type="text" id="total_harga_display" class="form-control bg-light fw-bold text-primary" value="Rp 0" readonly>
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <a href="penjualan.php" class="btn btn-outline-secondary px-4">Batal</a>
                    <button type="submit" name="simpan" class="btn btn-primary px-4">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function hitungTotal() {
    const selectProduk = document.getElementById('produkID');
    const selectedOption = selectProduk.options[selectProduk.selectedIndex];
    const harga = parseFloat(selectedOption.getAttribute('data-harga')) || 0;
    const jumlah = parseInt(document.getElementById('jumlah').value) || 0;

    const total = harga * jumlah;
    document.getElementById('total_harga').value = total;
    document.getElementById('total_harga_display').value = 'Rp ' + total.toLocaleString('id-ID');
}
</script>