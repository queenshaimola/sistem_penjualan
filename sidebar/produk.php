<?php include __DIR__ . '/../header.php'; ?>

<div class="container-fluid px-4 py-2">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        
        <!-- Header Card -->
        <div class="card-header bg-white py-3 px-4 border-bottom-0 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark mb-0">Data Produk</h5>
                <small class="text-muted">Kelola daftar produk dan stok barang penjualan</small>
            </div>
            <a href="../produk_tambah.php" class="btn btn-primary btn-sm px-3 py-2 rounded-3 fw-semibold shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Tambah Produk
            </a>
        </div>

        <!-- Tabel Data Produk -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary border-bottom">
                        <tr>
                            <th class="ps-4 py-3" width="5%">No</th>
                            <th class="py-3">Nama Produk</th>
                            <th class="py-3">Harga</th>
                            <th class="py-3">Stok</th>
                            <th class="text-center py-3" width="18%">Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include __DIR__ . '/../koneksi.php';
                        
                        $data = mysqli_query($koneksi, "SELECT * FROM produk ORDER BY produkID DESC");
                        $no = 1;
                        
                        if ($data && mysqli_num_rows($data) > 0) {
                            while ($d = mysqli_fetch_array($data)) {
                        ?>
                            <tr>
                                <td class="ps-4 fw-medium text-secondary"><?= $no++; ?></td>
                                <td class="fw-semibold text-dark"><?= htmlspecialchars($d['nama_produk'] ?? '-'); ?></td>
                                <td class="fw-semibold text-success">
                                    Rp <?= number_format($d['harga'] ?? 0, 0, ',', '.'); ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1 fw-normal">
                                        <?= number_format($d['stok'] ?? 0); ?> pcs
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="../produk_edit.php?id=<?= $d['produkID']; ?>" class="btn btn-sm btn-light text-primary border rounded-2 px-3 py-1 me-1 shadow-sm">
                                        <i class="bi bi-pencil-square me-1"></i>Edit
                                    </a>
                                    <a href="../produk_hapus.php?id=<?= $d['produkID']; ?>" class="btn btn-sm btn-light text-danger border rounded-2 px-3 py-1 shadow-sm" onclick="return confirm('Yakin ingin menghapus produk ini?')">
                                        <i class="bi bi-trash me-1"></i>Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php 
                            }
                        } else {
                            echo '<tr><td colspan="5" class="text-center py-4 text-muted">Belum ada data produk.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>