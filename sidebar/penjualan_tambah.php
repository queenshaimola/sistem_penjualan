<?php 
include '../koneksi.php'; 
include '../header.php'; 
include '../sidebar.php';

/** @var mysqli $koneksi */
date_default_timezone_set('Asia/Jakarta');

if (isset($_POST['simpan'])) {
    // Sanitasi Input
    $tgl        = mysqli_real_escape_string($koneksi, $_POST['tanggal_penjualan']) . ' ' . date('H:i:s');
    $pelanggan  = !empty($_POST['pelangganID']) ? "'".mysqli_real_escape_string($koneksi, $_POST['pelangganID'])."'" : "NULL";
    $produkID   = mysqli_real_escape_string($koneksi, $_POST['produkID']);
    $jumlah     = (int)$_POST['jumlah'];

    // -------------------------------------------------------------
    // VALIDASI SISI SERVER (PHP)
    // -------------------------------------------------------------
    
    // 1. Validasi Input Kosong atau Jumlah Kurang dari 1
    if (empty($produkID) || $jumlah < 1) {
        echo "<script>alert('Pilih produk dan masukkan jumlah minimal 1!'); window.history.back();</script>";
        exit();
    }

    // 2. Ambil Data Produk & Validasi Stok
    $q_produk = mysqli_query($koneksi, "SELECT nama_produk, harga, stok FROM produk WHERE produkID = '$produkID'");
    $p = mysqli_fetch_assoc($q_produk);

    if (!$p) {
        echo "<script>alert('Produk tidak ditemukan!'); window.history.back();</script>";
        exit();
    }

    $nama_produk = $p['nama_produk'];
    $harga       = $p['harga'];
    $stok_ada    = $p['stok'];

    // Cek apakah stok mencukupi
    if ($jumlah > $stok_ada) {
        echo "<script>alert('Stok tidak mencukupi! Stok tersisa: $stok_ada'); window.history.back();</script>";
        exit();
    }

    // Hitung ulang total harga di server demi keamanan
    $total = $harga * $jumlah;

    // -------------------------------------------------------------
    // PROSES SIMPAN TRANSAKSI & UPDATE STOK
    // -------------------------------------------------------------
    
    $simpan = mysqli_query($koneksi, "INSERT INTO penjualan (tanggal_penjualan, total_harga, nama_produk, pelangganID) VALUES ('$tgl', '$total', '$nama_produk', $pelanggan)");

    if ($simpan) {
        // Potong stok produk
        mysqli_query($koneksi, "UPDATE produk SET stok = stok - $jumlah WHERE produkID = '$produkID'");
        echo "<script>alert('Transaksi Berhasil!'); window.location.href='penjualan.php';</script>";
        exit();
    } else {
        echo "<script>alert('Gagal menyimpan transaksi: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>

<div class="container py-4 col-md-6">
    <div class="card p-4 shadow-sm border-0 rounded-3">
        <h4 class="text-center mb-3 fw-bold">Tambah Transaksi Penjualan</h4>

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
                    $q = mysqli_query($koneksi, "SELECT * FROM pelanggan");
                    while ($pel = mysqli_fetch_assoc($q)) {
                        echo "<option value='".$pel['pelangganID']."'>Member: ".$pel['nama_pelanggan']."</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Produk</label>
                <select name="produkID" id="produkID" class="form-select" onchange="hitungTotal()" required>
                    <option value="" data-harga="0" data-stok="0">-- Pilih Produk --</option>
                    <?php
                    $qp = mysqli_query($koneksi, "SELECT * FROM produk");
                    while ($pr = mysqli_fetch_assoc($qp)) {
                        echo "<option value='".$pr['produkID']."' data-harga='".$pr['harga']."' data-stok='".$pr['stok']."'>".$pr['nama_produk']." (Stok: ".$pr['stok'].")</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Jumlah (Qty)</label>
                <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" value="1" oninput="hitungTotal()" required>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Total Harga</label>
                <input type="hidden" name="total_harga" id="total_harga" value="0">
                <input type="text" id="total_harga_display" class="form-control bg-light fw-bold text-primary" value="Rp 0" readonly>
            </div>

            <!-- Tombol Berdampingan -->
            <div class="d-flex flex-row gap-2 justify-content-end align-items-center">
                <a href="penjualan.php" class="btn btn-secondary px-4">Batal</a>
                <button type="submit" name="simpan" class="btn btn-primary px-4">Simpan Transaksi</button>
            </div>
        </form>
    </div>
</div>

<script>
function hitungTotal() {
    const opt = document.getElementById('produkID').selectedOptions[0];
    const harga = parseFloat(opt.getAttribute('data-harga')) || 0;
    const stok = parseInt(opt.getAttribute('data-stok')) || 0;
    let qty = parseInt(document.getElementById('jumlah').value) || 0;

    // Validasi singkat di JavaScript untuk memperingatkan pengguna
    if (qty > stok && stok > 0) {
        alert('Jumlah melebihi stok yang tersedia (' + stok + ')!');
        document.getElementById('jumlah').value = stok;
        qty = stok;
    }

    const total = harga * qty;
    
    document.getElementById('total_harga').value = total;
    document.getElementById('total_harga_display').value = 'Rp ' + total.toLocaleString('id-ID');
}
</script>