<?php
include __DIR__ . '/../header.php';
include __DIR__ . '/../koneksi.php';

// Tangkap parameter filter & pencarian dari URL (GET)
$search     = isset($_GET['search']) ? trim($_GET['search']) : '';
$tgl_mulai  = isset($_GET['tgl_mulai']) ? $_GET['tgl_mulai'] : '';
$tgl_sampai = isset($_GET['tgl_sampai']) ? $_GET['tgl_sampai'] : '';
$limit      = isset($_GET['limit']) ? $_GET['limit'] : '10';

// Buat Kondisi Query Dinamis
$where_clauses = [];

if (!empty($search)) {
    $search_escaped  = mysqli_real_escape_string($koneksi, $search);
    $where_clauses[] = "(penjualan.penjualanID LIKE '%$search_escaped%' OR pelanggan.nama_pelanggan LIKE '%$search_escaped%')";
}

if (!empty($tgl_mulai) && !empty($tgl_sampai)) {
    $tgl_m = mysqli_real_escape_string($koneksi, $tgl_mulai);
    $tgl_s = mysqli_real_escape_string($koneksi, $tgl_sampai);
    $where_clauses[] = "(DATE(penjualan.tanggal_penjualan) BETWEEN '$tgl_m' AND '$tgl_s')";
} elseif (!empty($tgl_mulai)) {
    $tgl_m = mysqli_real_escape_string($koneksi, $tgl_mulai);
    $where_clauses[] = "(DATE(penjualan.tanggal_penjualan) >= '$tgl_m')";
} elseif (!empty($tgl_sampai)) {
    $tgl_s = mysqli_real_escape_string($koneksi, $tgl_sampai);
    $where_clauses[] = "(DATE(penjualan.tanggal_penjualan) <= '$tgl_s')";
}

$where_sql = "";
if (count($where_clauses) > 0) {
    $where_sql = "WHERE " . implode(' AND ', $where_clauses);
}

// Batas Jumlah Data (Limit)
$limit_sql = "";
if ($limit !== 'semua' && is_numeric($limit)) {
    $limit_sql = "LIMIT " . (int)$limit;
}

$query = "SELECT penjualan.*, pelanggan.nama_pelanggan, user.nama_user AS nama_kasir 
          FROM penjualan 
          LEFT JOIN pelanggan ON penjualan.pelangganID = pelanggan.pelangganID 
          LEFT JOIN user ON penjualan.userID = user.userID
          $where_sql
          ORDER BY penjualanID DESC 
          $limit_sql";

$data = mysqli_query($koneksi, $query);
?>

<div class="container-fluid px-4 py-2">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">

        <!-- Header Card -->
        <div class="card-header bg-white py-3 px-4 border-bottom-0 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-0">Data Penjualan</h5>
                <small class="text-muted">Kelola riwayat transaksi dan data penjualan</small>
            </div>
            <a href="penjualan_tambah.php" class="btn btn-primary btn-sm px-3 py-2 rounded-3 fw-semibold shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Tambah Penjualan
            </a>
        </div>

        <!-- Form Filter & Pencarian -->
        <div class="card-body bg-light border-top border-bottom py-3 px-4">
            <form method="GET" action="" class="row g-2 align-items-end">

                <!-- Pencarian Kata Kunci -->
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-secondary mb-1">Cari (ID / Pelanggan)</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari ID atau nama..." value="<?= htmlspecialchars($search); ?>">
                    </div>
                </div>

                <!-- Filter Tanggal Mulai -->
                <div class="col-md-2 col-6">
                    <label class="form-label small fw-semibold text-secondary mb-1">Dari Tanggal</label>
                    <input type="date" name="tgl_mulai" class="form-control form-control-sm" value="<?= htmlspecialchars($tgl_mulai); ?>">
                </div>

                <!-- Filter Tanggal Sampai -->
                <div class="col-md-2 col-6">
                    <label class="form-label small fw-semibold text-secondary mb-1">Sampai Tanggal</label>
                    <input type="date" name="tgl_sampai" class="form-control form-control-sm" value="<?= htmlspecialchars($tgl_sampai); ?>">
                </div>

                <!-- Filter Jumlah Data (Limit) -->
                <div class="col-md-2 col-6">
                    <label class="form-label small fw-semibold text-secondary mb-1">Tampilkan</label>
                    <select name="limit" class="form-select form-select-sm">
                        <option value="10" <?= $limit == '10' ? 'selected' : ''; ?>>10 Data</option>
                        <option value="25" <?= $limit == '25' ? 'selected' : ''; ?>>25 Data</option>
                        <option value="50" <?= $limit == '50' ? 'selected' : ''; ?>>50 Data</option>
                        <option value="100" <?= $limit == '100' ? 'selected' : ''; ?>>100 Data</option>
                        <option value="semua" <?= $limit == 'semua' ? 'selected' : ''; ?>>Semua</option>
                    </select>
                </div>

                <!-- Tombol Action Filter & Reset -->
                <div class="col-md-3 col-6 d-flex gap-1">
                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-semibold">
                        <i class="bi bi-filter me-1"></i> Filter
                    </button>
                    <?php if (!empty($search) || !empty($tgl_mulai) || !empty($tgl_sampai) || $limit != '10'): ?>
                        <a href="penjualan.php" class="btn btn-outline-secondary btn-sm fw-semibold" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    <?php endif; ?>
                </div>

            </form>
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
                            <th class="py-3">Kasir</th>
                            <th class="py-3">Pelanggan</th>
                            <th class="py-3">Total Harga</th>
                            <th class="text-center py-3" width="20%">Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
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
                                    <td class="text-secondary">
                                        <?= htmlspecialchars($d['nama_kasir'] ?? 'Admin'); ?>
                                    </td>
                                    <td class="fw-semibold text-dark">
                                        <?= htmlspecialchars($d['nama_pelanggan'] ?? 'Umum'); ?>
                                    </td>
                                    <td class="fw-semibold text-success">
                                        Rp <?= number_format($d['total_harga'] ?? 0, 0, ',', '.'); ?>
                                    </td>

                                    <!-- Aksi Dibuat Sebelahan / Flexbox -->
                                    <td class="text-center text-nowrap">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <a href="penjualan_detail.php?id=<?= $d['penjualanID']; ?>" class="btn btn-sm btn-light text-primary border rounded-2 px-3 py-1 shadow-sm">
                                                <i class="bi bi-eye me-1"></i>Detail
                                            </a>
                                            <a href="penjualan_hapus.php?id=<?= $d['penjualanID']; ?>" class="btn btn-sm btn-light text-danger border rounded-2 px-3 py-1 shadow-sm" onclick="return confirm('Yakin ingin menghapus data transaksi ini?')">
                                                <i class="bi bi-trash me-1"></i>Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo '<tr><td colspan="6" class="text-center py-4 text-muted">Data penjualan tidak ditemukan.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>