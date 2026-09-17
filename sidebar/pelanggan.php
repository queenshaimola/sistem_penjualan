<?php 
include '../koneksi.php';
include '../header.php';
include '../sidebar.php';
?>

<div class="container-fluid px-4 py-2">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        
        <!-- Header Card -->
        <div class="card-header bg-white py-3 px-4 border-bottom-0 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark mb-0">Data Pelanggan</h5>
                <small class="text-muted">Kelola daftar seluruh pelanggan sistem penjualan</small>
            </div>
            <a href="pelanggan_tambah.php" class="btn btn-primary btn-sm px-3 py-2 rounded-3 fw-semibold shadow-sm" >
                <i class="bi bi-plus-lg me-1"></i> Tambah Pelanggan 
            </a>
        </div>

        <!-- Tabel Data -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary border-bottom">
                        <tr>
                            <th class="ps-4 py-3" width="5%">No</th>
                            <th class="py-3">Nama</th>
                            <th class="py-3">HP</th>
                            <th class="py-3">Alamat</th>
                            <th class="text-center py-3" width="18%">Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include __DIR__ . '/../koneksi.php';
                        $data = mysqli_query($koneksi, "SELECT * FROM pelanggan ORDER BY pelangganID DESC");
                        $no = 1;
                        if (mysqli_num_rows($data) > 0) {
                            while ($d = mysqli_fetch_array($data)) {
                        ?>
                            <tr>
                                <td class="ps-4 fw-medium text-secondary"><?= $no++; ?></td>
                                <td class="fw-semibold text-dark"><?= htmlspecialchars($d['nama_pelanggan'] ?? '-'); ?></td>
                                <td class="text-secondary"><?= htmlspecialchars($d['nomor_telepon'] ?? '-'); ?></td>
                                <td class="text-secondary"><?= htmlspecialchars($d['alamat'] ?? '-'); ?></td>
                                <td class="text-center">
                                    <a href="pelanggan_edit.php?id=<?= $d['pelangganID']; ?>" class="btn btn-sm btn-light text-primary border rounded-2 px-3 py-1 me-1 shadow-sm">
                                        <i class="bi bi-pencil-square me-1"></i>Edit
                                    </a>
                                    <a href="pelanggan_hapus.php?id=<?= $d['pelangganID']; ?>" class="btn btn-sm btn-light text-danger border rounded-2 px-3 py-1 shadow-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                        <i class="bi bi-trash me-1"></i>Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php 
                            }
                        } else {
                            echo '<tr><td colspan="5" class="text-center py-4 text-muted">Belum ada data pelanggan.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
