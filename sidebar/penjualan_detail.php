<?php
include '../koneksi.php';
include '../header.php';
include '../sidebar.php';

/** @var mysqli $koneksi */

// Ambil ID dari URL dan bersihkan dari karakter '#'
$id = isset($_GET['id']) ? str_replace('#', '', $_GET['id']) : '';

// Query mengambil data penjualan & nama pelanggan
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

/** @var mysqli_result|bool $query_penjualan */
$query_penjualan = mysqli_query($koneksi, $sql);

/** @var array|null $penjualan */
$penjualan = mysqli_fetch_assoc($query_penjualan);

if (!$penjualan) {
    echo "<script>alert('Transaksi tidak ditemukan!'); window.location.href='penjualan.php';</script>";
    exit();
}
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
                
                <!-- Info Status Transaksi & Pelanggan -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <span class="text-muted small d-block mb-1">Status Pelanggan</span>
                            <?php if (!empty($penjualan['nama_pelanggan'])): ?>
                                <span class="badge bg-success bg-opacity-10 text-success fw-bold px-2 py-1 mb-1">MEMBER</span>
                                <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars($penjualan['nama_pelanggan']); ?></h6>
                            <?php else: ?>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary fw-bold px-2 py-1 mb-1">NON-MEMBER</span>
                                <h6 class="fw-bold text-dark mb-0">Umum / Pembeli Biasa</h6>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <span class="text-muted small d-block mb-1">Waktu Transaksi</span>
                            <h6 class="fw-bold text-dark mb-1">
                                <i class="bi bi-calendar-event me-1"></i><?= date('d F Y', strtotime($penjualan['tanggal_penjualan'])); ?>
                            </h6>
                            <small class="text-muted"><i class="bi bi-clock me-1"></i><?= date('H:i:s', strtotime($penjualan['tanggal_penjualan'])); ?> WIB</small>
                        </div>
                    </div>
                </div>

                <!-- Tabel Item Produk -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="bg-light text-secondary">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th>Nama Produk</th>
                                <th class="text-end" width="30%">Total harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">1</td>
                                <td class="fw-semibold text-dark">
                                    <?= !empty($penjualan['nama_produk']) ? htmlspecialchars($penjualan['nama_produk']) : 'Item Penjualan'; ?>
                                </td>
                                <td class="text-end fw-bold text-dark">
                                    Rp <?= number_format($penjualan['total_harga'], 0, ',', '.'); ?>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <td colspan="2" class="text-end fw-bold text-secondary">Total Bayar:</td>
                                <td class="text-end fw-bold text-primary fs-5">
                                    Rp <?= number_format($penjualan['total_harga'], 0, ',', '.'); ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>

    </div>
</div>