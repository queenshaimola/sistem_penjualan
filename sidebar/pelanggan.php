<?php 
include '../koneksi.php';
include '../header.php';
include '../sidebar.php';

/** @var mysqli $koneksi */

// Ambil kata kunci pencarian dari URL jika ada
$keyword = isset($_GET['cari']) ? trim($_GET['cari']) : '';

// -------------------------------------------------------------
// LOGIKA PAGINATION & LIMIT DATA DINAMIS (10, 20, 50)
// -------------------------------------------------------------
$allowed_limits = [10, 20, 50];
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
if (!in_array($limit, $allowed_limits)) {
    $limit = 10; // Fallback jika limit tidak valid
}

$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$halaman_awal = ($halaman > 1) ? ($halaman * $limit) - $limit : 0;

// Query dasar untuk pencarian
$where_clause = "";
if (!empty($keyword)) {
    $keyword_clean = mysqli_real_escape_string($koneksi, $keyword);
    $where_clause = " WHERE nama_pelanggan LIKE '%$keyword_clean%' 
                         OR nomor_telepon LIKE '%$keyword_clean%' 
                         OR alamat LIKE '%$keyword_clean%' ";
}

// Hitung total data
$query_total = "SELECT COUNT(*) AS total FROM pelanggan" . $where_clause;
$result_total = mysqli_query($koneksi, $query_total);
$row_total = mysqli_fetch_assoc($result_total);
$total_data = $row_total['total'];
$total_halaman = ceil($total_data / $limit);

// Query ambil data sesuai limit
$query_data = "SELECT * FROM pelanggan" . $where_clause . " ORDER BY pelangganID DESC LIMIT $halaman_awal, $limit";
$data = mysqli_query($koneksi, $query_data);

// Penomoran tabel dinamis
$no = $halaman_awal + 1;
?>

<div class="container-fluid px-4 py-2">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        
        <!-- Header Card & Form Pencarian -->
        <div class="card-header bg-white py-3 px-4 border-bottom-0 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-0">Data Pelanggan</h5>
                <small class="text-muted">Kelola daftar seluruh pelanggan sistem penjualan</small>
            </div>
            
            <div class="d-flex flex-wrap align-items-center gap-2">
                
                <!-- Dropdown Pilih Limit Data -->
                <form method="GET" action="pelanggan.php" class="d-flex align-items-center me-1">
                    <?php if (!empty($keyword)): ?>
                        <input type="hidden" name="cari" value="<?= htmlspecialchars($keyword); ?>">
                    <?php endif; ?>
                    <label class="small text-muted me-2 text-nowrap">Tampilkan:</label>
                    <select name="limit" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="10" <?= ($limit == 10) ? 'selected' : ''; ?>>10</option>
                        <option value="20" <?= ($limit == 20) ? 'selected' : ''; ?>>20</option>
                        <option value="50" <?= ($limit == 50) ? 'selected' : ''; ?>>50</option>
                    </select>
                </form>

                <!-- Form Cari -->
                <form method="GET" action="pelanggan.php" class="d-flex align-items-center me-2">
                    <input type="hidden" name="limit" value="<?= $limit; ?>">
                    <div class="input-group input-group-sm">
                        <input type="text" name="cari" class="form-control" placeholder="Cari nama, HP, alamat..." value="<?= htmlspecialchars($keyword); ?>">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="bi bi-search"></i> Cari
                        </button>
                        <?php if (!empty($keyword)): ?>
                            <a href="pelanggan.php?limit=<?= $limit; ?>" class="btn btn-outline-danger" title="Reset Pencarian">
                                <i class="bi bi-x-circle"></i> Reset
                            </a>
                        <?php endif; ?>
                    </div>
                </form>

                <!-- Tombol Tambah Pelanggan -->
                <a href="pelanggan_tambah.php" class="btn btn-primary btn-sm px-3 py-2 rounded-3 fw-semibold shadow-sm">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Pelanggan 
                </a>
            </div>
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
                            <th class="text-center py-3">Status</th>
                            <th class="text-center py-3" width="18%">Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($data) > 0) {
                            while ($d = mysqli_fetch_array($data)) {
                        ?>
                            <tr>
                                <td class="ps-4 fw-medium text-secondary"><?= $no++; ?></td>
                                <td class="fw-semibold text-dark"><?= htmlspecialchars($d['nama_pelanggan'] ?? '-'); ?></td>
                                <td class="text-secondary"><?= htmlspecialchars($d['nomor_telepon'] ?? '-'); ?></td>
                                <td class="text-secondary"><?= htmlspecialchars($d['alamat'] ?? '-'); ?></td>
                                
                                <!-- Kolom Status -->
                                <td class="text-center">
                                    <?php if ($d['status'] == 1 || $d['status'] == 'Aktif'): ?>
                                        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill">Tidak Aktif</span>
                                    <?php endif; ?>
                                </td>

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
                            if (!empty($keyword)) {
                                echo '<tr><td colspan="6" class="text-center py-4 text-muted">Pelanggan dengan kata kunci "<strong>' . htmlspecialchars($keyword) . '</strong>" tidak ditemukan.</td></tr>';
                            } else {
                                echo '<tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data pelanggan.</td></tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Card Footer Navigation / Pagination -->
        <?php if ($total_data > 0): ?>
        <div class="card-footer bg-white py-3 px-4 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <small class="text-muted">
                Menampilkan <strong><?= min($halaman_awal + 1, $total_data); ?> - <?= min($halaman_awal + $limit, $total_data); ?></strong> dari <strong><?= $total_data; ?></strong> data
            </small>

            <?php if ($total_halaman > 1): ?>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <!-- URL Query Builder -->
                    <?php 
                    $query_params = "limit=" . $limit;
                    if (!empty($keyword)) {
                        $query_params .= "&cari=" . urlencode($keyword);
                    }
                    ?>

                    <!-- Tombol Prev -->
                    <li class="page-item <?= ($halaman <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?halaman=<?= $halaman - 1; ?>&<?= $query_params; ?>">Previous</a>
                    </li>

                    <!-- Link Angka Halaman -->
                    <?php for ($x = 1; $x <= $total_halaman; $x++): ?>
                        <li class="page-item <?= ($halaman == $x) ? 'active' : ''; ?>">
                            <a class="page-link" href="?halaman=<?= $x; ?>&<?= $query_params; ?>"><?= $x; ?></a>
                        </li>
                    <?php endfor; ?>

                    <!-- Tombol Next -->
                    <li class="page-item <?= ($halaman >= $total_halaman) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?halaman=<?= $halaman + 1; ?>&<?= $query_params; ?>">Next</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>
</div>