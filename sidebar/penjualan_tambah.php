<?php 
include '../koneksi.php'; 
include '../header.php'; 
include '../sidebar.php';

/** @var mysqli $koneksi */
date_default_timezone_set('Asia/Jakarta');

if (isset($_POST['simpan'])) {
    $tgl        = $_POST['tanggal_penjualan'] . ' ' . date('H:i:s');
    $pelanggan  = !empty($_POST['pelangganID']) ? "'".$_POST['pelangganID']."'" : "NULL";
    $produkID   = $_POST['produkID'];
    $jumlah     = $_POST['jumlah'];
    $total      = $_POST['total_harga'];

    // 1. Ambil Nama Produk
    $p = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama_produk FROM produk WHERE produkID = '$produkID'"));
    $nama_produk = $p['nama_produk'];

    // 2. Simpan Transaksi
    $simpan = mysqli_query($koneksi, "INSERT INTO penjualan (tanggal_penjualan, total_harga, nama_produk, pelangganID) VALUES ('$tgl', '$total', '$nama_produk', $pelanggan)");

    // 3. Potong Stok Produk
    if ($simpan) {
        mysqli_query($koneksi, "UPDATE produk SET stok = stok - $jumlah WHERE produkID = '$produkID'");
        echo "<script>alert('Transaksi Berhasil!'); window.location.href='penjualan.php';</script>";
        exit();
    }
}
?>

<div class="container py-4 col-md-6">
    <div class="card p-4 shadow-sm">
        <h4 class="text-center mb-3 fw-bold">Tambah Transaksi Penjualan</h4>

        <form method="POST">
            <div class="mb-3">
                <label>Tanggal Transaksi</label>
                <input type="date" name="tanggal_penjualan" class="form-control" value="<?= date('Y-m-d'); ?>" required>
            </div>

            <div class="mb-3">
                <label>Pelanggan</label>
                <select name="pelangganID" class="form-select">
                    <option value="">-- Umum / Non-Member --</option>
                    <?php
                    $q = mysqli_query($koneksi, "SELECT * FROM pelanggan");
                    while ($pel = mysqli_fetch_assoc($q)) {
                        echo "<option value='".$pel['pelangganID']."'>Member: ".$pel['nama_pelanggan']."</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Produk</label>
                <select name="produkID" id="produkID" class="form-select" onchange="hitungTotal()" required>
                    <option value="" data-harga="0">-- Pilih Produk --</option>
                    <?php
                    $qp = mysqli_query($koneksi, "SELECT * FROM produk");
                    while ($pr = mysqli_fetch_assoc($qp)) {
                        echo "<option value='".$pr['produkID']."' data-harga='".$pr['harga']."'>".$pr['nama_produk']." (Stok: ".$pr['stok'].")</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Jumlah (Qty)</label>
                <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" value="1" oninput="hitungTotal()" required>
            </div>

            <div class="mb-4">
                <label>Total Harga</label>
                <input type="hidden" name="total_harga" id="total_harga" value="0">
                <input type="text" id="total_harga_display" class="form-control bg-light fw-bold text-primary" value="Rp 0" readonly>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="penjualan.php" class="btn btn-secondary">Batal</a>
                <button type="submit" name="simpan" class="btn btn-primary">Simpan Transaksi</button>
            </div>
        </form>
    </div>
</div>

<script>
function hitungTotal() {
    const opt = document.getElementById('produkID').selectedOptions[0];
    const harga = parseFloat(opt.getAttribute('data-harga')) || 0;
    const qty = parseInt(document.getElementById('jumlah').value) || 0;
    const total = harga * qty;
    
    document.getElementById('total_harga').value = total;
    document.getElementById('total_harga_display').value = 'Rp ' + total.toLocaleString('id-ID');
}
</script>