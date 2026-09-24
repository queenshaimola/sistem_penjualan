<?php
include '../koneksi.php';

/** @var mysqli $koneksi */

$id = isset($_GET['id']) ? str_replace('#', '', $_GET['id']) : '';

// Query data utama penjualan
$sql = "
    SELECT p.*, pel.nama_pelanggan, u.nama_user AS nama_kasir
    FROM penjualan p
    LEFT JOIN pelanggan pel ON p.pelangganID = pel.pelangganID 
    LEFT JOIN user u ON p.userID = u.userID
    WHERE p.penjualanID = '$id'
";

$query_penjualan = mysqli_query($koneksi, $sql);
$penjualan = mysqli_fetch_assoc($query_penjualan);

if (!$penjualan) {
    echo "Data transaksi tidak ditemukan.";
    exit();
}

// Query rincian item transaksi
$sql_detail = "
    SELECT dp.*, pr.nama_produk, pr.harga 
    FROM detail_penjualan dp
    LEFT JOIN produk pr ON dp.produkID = pr.produkID
    WHERE dp.penjualanID = '$id'
";
$query_detail = mysqli_query($koneksi, $sql_detail);
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

        .text-center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

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

            body {
                padding: 5mm;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <!-- Header Struk -->
    <div class="text-center">
        <h2 class="bold" style="font-size: 15px; margin: 0;">OLA MINIMARKET CIHUY 6767</h2>
        <p style="margin: 2px 0;">Jl. Raya Six Seven No. 67</p>
        <p style="margin: 2px 0;">Telp: 0812-3456-7890</p>
    </div>

    <div class="line"></div>

    <!-- Info Transaksi -->
    <div class="flex-between">
        <span>No. Struk :</span>
        <span>#TRX-<?= str_pad($penjualan['penjualanID'], 4, '0', STR_PAD_LEFT); ?></span>
    </div>
    <div class="flex-between">
        <span>Kasir :</span>
        <span><?= htmlspecialchars($penjualan['nama_kasir'] ?? 'Admin'); ?></span>
    </div>
    <div class="flex-between">
        <span>Tgl :</span>
        <span><?= date('d-m-Y H:i', strtotime($penjualan['tanggal_penjualan'])); ?></span>
    </div>
    <div class="flex-between">
        <span>Pelanggan :</span>
        <span class="bold">
            <?= !empty($penjualan['nama_pelanggan']) ? strtoupper(htmlspecialchars($penjualan['nama_pelanggan'])) : 'UMUM'; ?>
        </span>
    </div>
    <div class="flex-between">
        <span>Metode :</span>
        <span><?= htmlspecialchars($penjualan['metode_pembayaran'] ?? 'Cash'); ?></span>
    </div>

    <div class="line"></div>

    <!-- Loop Daftar Barang -->
    <?php
    if ($query_detail && mysqli_num_rows($query_detail) > 0) {
        while ($item = mysqli_fetch_assoc($query_detail)) {
            $harga_satuan = $item['jumlah'] > 0 ? ($item['subtotal'] / $item['jumlah']) : 0;
    ?>
            <div style="margin-top: 4px;" class="bold">
                <?= htmlspecialchars($item['nama_produk'] ?? 'Item'); ?>
            </div>
            <div class="flex-between">
                <span><?= $item['jumlah']; ?> x Rp <?= number_format($harga_satuan, 0, ',', '.'); ?></span>
                <span>Rp <?= number_format($item['subtotal'], 0, ',', '.'); ?></span>
            </div>
    <?php
        }
    }
    ?>

    <div class="line"></div>

    <!-- Rincian Total & Pembayaran -->
    <?php if (!empty($penjualan['diskon']) && $penjualan['diskon'] > 0): ?>
        <div class="flex-between">
            <span>DISKON</span>
            <span>- Rp <?= number_format($penjualan['diskon'], 0, ',', '.'); ?></span>
        </div>
    <?php endif; ?>

    <div class="flex-between bold" style="font-size: 13px;">
        <span>TOTAL</span>
        <span>Rp <?= number_format($penjualan['total_harga'], 0, ',', '.'); ?></span>
    </div>
    <div class="flex-between">
        <span>BAYAR</span>
        <span>Rp <?= number_format($penjualan['bayar'] ?? 0, 0, ',', '.'); ?></span>
    </div>
    <div class="flex-between">
        <span>KEMBALI</span>
        <span>Rp <?= number_format($penjualan['kembalian'] ?? 0, 0, ',', '.'); ?></span>
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