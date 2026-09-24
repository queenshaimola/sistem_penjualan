<?php
include __DIR__ . '/../koneksi.php';
include __DIR__ . '/../header.php';

/** @var mysqli $koneksi */

$id = isset($_GET['id']) ? str_replace('#', '', $_GET['id']) : '';

// Query data utama penjualan & pelanggan
$sql = "
    SELECT 
        p.*, 
        pel.nama_pelanggan,
        u.nama_user AS nama_kasir
    FROM penjualan p
    LEFT JOIN pelanggan pel ON p.pelangganID = pel.pelangganID 
    LEFT JOIN user u ON p.userID = u.userID
    WHERE p.penjualanID = '$id'
";

$query_penjualan = mysqli_query($koneksi, $sql);
$penjualan = mysqli_fetch_assoc($query_penjualan);

if (!$penjualan) {
    echo "<script>alert('Transaksi tidak ditemukan!'); window.location.href='penjualan.php';</script>";
    exit();
}

// Query rincian barang dari detail_penjualan
$sql_detail = "
    SELECT dp.*, pr.nama_produk 
    FROM detail_penjualan dp
    LEFT JOIN produk pr ON dp.produkID = pr.produkID
    WHERE dp.penjualanID = '$id'
";
$query_detail = mysqli_query($koneksi, $sql_detail);
?>

<div class="container-fluid px-4 py-4 d-flex justify-content-center">
    <div class="col-md-9 col-lg-8">

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

            <!-- Header Card -->
            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Detail Transaksi Penjualan</h5>
                    <small class="text-muted">Kode Struk: #TRX-<?= str_pad($penjualan['penjualanID'], 4, '0', STR_PAD_LEFT); ?></small>
                </div>
                <div class="d-flex gap-2">
                    <a href="penjualan_cetak.php?id=<?= $penjualan['penjualanID']; ?>" target="_blank" class="btn btn-primary btn-sm px-3 rounded-3 fw-semibold">
                        <i class="bi bi-printer me-1"></i> Cetak Struk
                    </a>
                    <a href="penjualan.php" class="btn btn-outline-secondary btn-sm px-3 rounded-3">Kembali</a>
                </div>
            </div>

            <div class="card-body p-4">

                <!-- Info Pelanggan & Transaksi -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <span class="text-muted small d-block mb-1">Pelanggan</span>
                            <?php if (!empty($penjualan['nama_pelanggan'])): ?>
                                <span class="badge bg-success bg-opacity-10 text-success fw-bold px-2 py-1 mb-1">MEMBER</span>
                                <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars($penjualan['nama_pelanggan']); ?></h6>
                            <?php else: ?>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary fw-bold px-2 py-1 mb-1">NON-MEMBER</span>
                                <h6 class="fw-bold text-dark mb-0">Umum / Non-Member</h6>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <span class="text-muted small d-block mb-1">Waktu Transaksi & Kasir</span>
                            <h6 class="fw-bold text-dark mb-1">
                                <i class="bi bi-calendar-event me-1"></i><?= date('d-m-Y H:i', strtotime($penjualan['tanggal_penjualan'])); ?> WIB
                            </h6>
                            <div class="d-flex align-items-center gap-2 mt-2">
                                <span class="badge bg-info text-dark fw-semibold"><?= htmlspecialchars($penjualan['metode_pembayaran'] ?? 'Cash'); ?></span>
                                <span class="badge bg-secondary text-white fw-normal">
                                    <i class="bi bi-person-badge me-1"></i>Kasir: <?= htmlspecialchars($penjualan['nama_kasir'] ?? 'Admin'); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Detail Barang -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="bg-light text-secondary">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th>Nama Produk</th>
                                <th class="text-center" width="15%">Qty</th>
                                <th class="text-end" width="25%">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $subtotal_kotor = 0;
                            if ($query_detail && mysqli_num_rows($query_detail) > 0) {
                                while ($item = mysqli_fetch_assoc($query_detail)) {
                                    $subtotal_kotor += $item['subtotal'];
                            ?>
                                    <tr>
                                        <td class="text-center"><?= $no++; ?></td>
                                        <td class="fw-semibold text-dark"><?= htmlspecialchars($item['nama_produk'] ?? 'Produk Dihapus'); ?></td>
                                        <td class="text-center"><?= $item['jumlah']; ?></td>
                                        <td class="text-end">Rp <?= number_format($item['subtotal'], 0, ',', '.'); ?></td>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo '<tr><td colspan="4" class="text-center text-muted">Rincian item tidak ditemukan.</td></tr>';
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end fw-semibold text-secondary">Subtotal:</td>
                                <td class="text-end fw-semibold">Rp <?= number_format($subtotal_kotor, 0, ',', '.'); ?></td>
                            </tr>
                            <?php if (!empty($penjualan['diskon']) && $penjualan['diskon'] > 0): ?>
                                <tr>
                                    <td colspan="3" class="text-end fw-semibold text-danger">Diskon:</td>
                                    <td class="text-end fw-semibold text-danger">- Rp <?= number_format($penjualan['diskon'], 0, ',', '.'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <tr class="table-light">
                                <td colspan="3" class="text-end fw-bold text-dark fs-6">Grand Total:</td>
                                <td class="text-end fw-bold text-primary fs-5">Rp <?= number_format($penjualan['total_harga'], 0, ',', '.'); ?></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end text-secondary">Uang Bayar:</td>
                                <td class="text-end fw-semibold">Rp <?= number_format($penjualan['bayar'] ?? 0, 0, ',', '.'); ?></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end text-secondary">Kembalian:</td>
                                <td class="text-end fw-semibold text-success">Rp <?= number_format($penjualan['kembalian'] ?? 0, 0, ',', '.'); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>

    </div>
</div>