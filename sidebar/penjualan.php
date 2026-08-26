<?php include __DIR__ . '/../header.php'; ?>

<div class="container-fluid px-4 py-2">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        
        <!-- Header Card -->
        <div class="card-header bg-white py-3 px-4 border-bottom-0 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark mb-0">Data Penjualan</h5>
                <small class="text-muted">Kelola riwayat transaksi dan data penjualan</small>
            </div>
            <a href="../penjualan_tambah.php" class="btn btn-primary btn-sm px-3 py-2 rounded-3 fw-semibold shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Tambah Penjualan
            </a>
        </div>

        <!-- Tabel Data Penjualan -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary border-bottom">
                        <tr>
                            <th class="ps-4 py-3" width="5%">No</th>
                            <th class="py-3">ID Transaksi</th>
                            <th class="py-3">Tanggal</th>
                            <th class="py-3">Pelanggan</th>
                            <th class="py-3">Total Harga</th>
                            <th class="text-center py-3" width="18%">Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include __DIR__ . '/../koneksi.php';
                        
                        $query = "SELECT penjualan.*, pelanggan.nama_pelanggan 
                                  FROM penjualan 
                                  LEFT JOIN pelanggan ON penjualan.pelangganID = pelanggan.pelangganID 
                                  ORDER BY penjualanID DESC";
                        
                        $data = mysqli_query($koneksi, $query);
                        $no = 1;
                        
                        if ($data && mysqli_num_rows($data) > 0) {
                            while ($d = mysqli_fetch_array($data)) {
                        ?>
                            <tr>
                                <td class="ps-4 fw-medium text-secondary"><?= $no++; ?></td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1 fw-normal">
                                        #<?= $d['penjualanID']; ?>
                                    </span>
                                </td>
                                <td class="text-secondary">
                                    <?= !empty($d['tanggal_penjualan']) ? date('d-m-Y', strtotime($d['tanggal_penjualan'])) : '-'; ?>
                                </td>
                                <td class="fw-semibold text-dark">
                                    <?= htmlspecialchars($d['nama_pelanggan'] ?? 'Umum'); ?>
                                </td>
                                <td class="fw-semibold text-success">
                                    Rp <?= number_format($d['total_harga'] ?? 0, 0, ',', '.'); ?>
                                </td>
                                <td class="text-center">
                                    <a href="../penjualan_detail.php?id=<?= $d['penjualanID']; ?>" class="btn btn-sm btn-light text-primary border rounded-2 px-3 py-1 me-1 shadow-sm">
                                        <i class="bi bi-eye me-1"></i>Detail
                                    </a>
                                    <a href="../penjualan_hapus.php?id=<?= $d['penjualanID']; ?>" class="btn btn-sm btn-light text-danger border rounded-2 px-3 py-1 shadow-sm" onclick="return confirm('Yakin ingin menghapus data transaksi ini?')">
                                        <i class="bi bi-trash me-1"></i>Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php 
                            }
                        } else {
                            echo '<tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data penjualan.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>