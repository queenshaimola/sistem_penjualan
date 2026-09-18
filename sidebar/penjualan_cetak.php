<?php
include '../koneksi.php';

/** @var mysqli $koneksi */

// Ambil ID dari URL dan bersihkan karakter '#'
$id = isset($_GET['id']) ? str_replace('#', '', $_GET['id']) : '';

// Query mengambil data dari tabel penjualan dan pelanggan
$sql = "
    SELECT 
        p.penjualanID, 
        p.tanggal_penjualan, 
        p.total_harga, 
        p.nama_produk,
        pel.nama_pelanggan
    FROM penjualan p
    LEFT JOIN pelanggan pel ON p.pelangganID = pel.pelangganID 
    WHERE p.penjualanID = '$id'
";

$query_penjualan = mysqli_query($koneksi, $sql);
$penjualan = mysqli_fetch_assoc($query_penjualan);

if (!$penjualan) {
    echo "Data transaksi tidak ditemukan.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #<?= $penjualan['penjualanID']; ?></title>
    <style>
        * {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
        }
        body {
            width: 78mm;
            margin: 0 auto;
            padding: 10px;
        }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        .line {
            border-bottom: 1px dashed #000;
            margin: 8px 0;
        }
        .flex-between {
            display: flex;
            justify-content: space-between;
        }
        @media print {
            @page {
                size: 80mm auto;
                margin: 0;
            }
            body { padding: 5mm; }
        }
    </style>
</head>
<body onload="window.print()">

    <!-- Header Struk -->
    <div class="text-center">
        <h2 class="bold" style="font-size: 15px; margin: 0;">SERBA ADA MART</h2>
        <p style="margin: 2px 0;">Jl. Raya Penjualan No. 123</p>
        <p style="margin: 2px 0;">Telp: 0812-3456-7890</p>
    </div>

    <div class="line"></div>

    <!-- Info Transaksi -->
    <div class="flex-between">
        <span>No. Struk :</span>
        <span>#TRX-<?= str_pad($penjualan['penjualanID'], 4, '0', STR_PAD_LEFT); ?></span>
    </div>
    <div class="flex-between">
        <span>Tgl       :</span>
        <span><?= date('d-m-Y H:i:s', strtotime($penjualan['tanggal_penjualan'])); ?></span>
    </div>
    <div class="flex-between">
        <span>Kasir     :</span>
        <span>Admin</span>
    </div>
    <div class="flex-between">
        <span>Pelanggan :</span>
        <span class="bold">
            <?= !empty($penjualan['nama_pelanggan']) ? strtoupper(htmlspecialchars($penjualan['nama_pelanggan'])) : 'UMUM'; ?>
        </span>
    </div>

    <div class="line"></div>

    <!-- Daftar Barang -->
    <div style="margin-bottom: 4px;" class="bold">
        <?= !empty($penjualan['nama_produk']) ? htmlspecialchars($penjualan['nama_produk']) : 'Item Penjualan'; ?>
    </div>
    <div class="flex-between">
        <span>1 x Rp <?= number_format($penjualan['total_harga'], 0, ',', '.'); ?></span>
        <span>Rp <?= number_format($penjualan['total_harga'], 0, ',', '.'); ?></span>
    </div>

    <div class="line"></div>

    <!-- Rincian Pembayaran -->
    <div class="flex-between bold" style="font-size: 13px;">
        <span>TOTAL BAYAR</span>
        <span>Rp <?= number_format($penjualan['total_harga'], 0, ',', '.'); ?></span>
    </div>
    <div class="flex-between">
        <span>TUNAI</span>
        <span>Rp <?= number_format($penjualan['total_harga'], 0, ',', '.'); ?></span>
    </div>
    <div class="flex-between">
        <span>KEMBALI</span>
        <span>Rp 0</span>
    </div>

    <div class="line"></div>

    <!-- Footer Struk -->
    <div class="text-center" style="margin-top: 10px;">
        <p style="margin: 2px 0;" class="bold">--- TERIMA KASIH ---</p>
        <p style="margin: 2px 0;">BARANG YANG SUDAH DIBELI</p>
        <p style="margin: 2px 0;">TIDAK DAPAT DITUKAR/DIKEMBALIKAN</p>
    </div>

</body>
</html>