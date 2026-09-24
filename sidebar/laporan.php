<?php
include __DIR__ . '/../header.php';
include __DIR__ . '/../koneksi.php';

/** @var mysqli $koneksi */

// Tangkap Parameter Filter dari URL (GET)
$search     = isset($_GET['search']) ? trim($_GET['search']) : '';
$tgl_mulai  = isset($_GET['tgl_mulai']) ? $_GET['tgl_mulai'] : '';
$tgl_sampai = isset($_GET['tgl_sampai']) ? $_GET['tgl_sampai'] : '';
$limit      = isset($_GET['limit']) ? $_GET['limit'] : '7'; // Default Maksimal 7 Data

// -------------------------------------------------------------
// 1. HITUNG STATISTIK KOTAK RINGKASAN (1 BULAN INI)
// -------------------------------------------------------------
$bulan_ini = date('Y-m');

// Total Income Bulan Ini
$q_income = mysqli_query($koneksi, "SELECT SUM(total_harga) AS total_income FROM penjualan WHERE DATE_FORMAT(tanggal_penjualan, '%Y-%m') = '$bulan_ini'");
$d_income = mysqli_fetch_assoc($q_income);
$total_income_bulan_ini = $d_income['total_income'] ?? 0;

// Total Jumlah Produk Terjual Bulan Ini
$q_produk_terjual = mysqli_query($koneksi, "SELECT SUM(dp.jumlah) AS total_terjual 
                                           FROM detail_penjualan dp 
                                           JOIN penjualan p ON dp.penjualanID = p.penjualanID 
                                           WHERE DATE_FORMAT(p.tanggal_penjualan, '%Y-%m') = '$bulan_ini'");
$d_produk_terjual = mysqli_fetch_assoc($q_produk_terjual);
$total_terjual_bulan_ini = $d_produk_terjual['total_terjual'] ?? 0;

// Total Transaksi / Struk Bulan Ini
$q_transaksi = mysqli_query($koneksi, "SELECT COUNT(*) AS total_trx FROM penjualan WHERE DATE_FORMAT(tanggal_penjualan, '%Y-%m') = '$bulan_ini'");
$d_transaksi = mysqli_fetch_assoc($q_transaksi);
$total_trx_bulan_ini = $d_transaksi['total_trx'] ?? 0;


// -------------------------------------------------------------
// 2. KONDISI QUERY UNTUK TABEL PENJUALAN
// -------------------------------------------------------------
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

// Batas Jumlah Data (Limit Max 7)
$limit_sql = "";
if ($limit !== 'semua' && is_numeric($limit)) {
    $limit_sql = "LIMIT " . (int)$limit;
}

// Query Utama Tabel Penjualan
$query = "SELECT penjualan.*, 
                 pelanggan.nama_pelanggan, 
                 IFNULL(user.nama_user, 'Admin') AS nama_kasir 
          FROM penjualan 
          LEFT JOIN pelanggan ON penjualan.pelangganID = pelanggan.pelangganID 
          LEFT JOIN user ON penjualan.userID = user.userID
          $where_sql
          ORDER BY penjualan.penjualanID DESC 
          $limit_sql";

$data = mysqli_query($koneksi, $query);
?>

<!-- CSS ANIMASI -->
<style>
.stat-card {
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}
.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
}
.fade-in-up {
    animation: fadeInUp 0.5s ease-in-out forwards;
}
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<div class="container-fluid px-4 py-3 fade-in-up">

    <!-- Header Halaman -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold text-dark mb-0">Laporan Penjualan</h4>
            <small class="text-muted">Ringkasan performa penjualan dan riwayat transaksi</small>
        </div>
        <div>
            <a href="laporan_cetak.php?tgl_mulai=<?= $tgl_mulai; ?>&tgl_sampai=<?= $tgl_sampai; ?>&search=<?= $search; ?>" target="_blank" class="btn btn-outline-danger btn-sm fw-semibold me-1">
                <i class="bi bi-file-earmark-pdf me-1"></i> Cetak PDF
            </a>
            <a href="laporan_excel.php?tgl_mulai=<?= $tgl_mulai; ?>&tgl_sampai=<?= $tgl_sampai; ?>&search=<?= $search; ?>" target="_blank" class="btn btn-outline-success btn-sm fw-semibold">
                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
            </a>
        </div>
    </div>

    <!-- KOTAK STATISTIK RINGKASAN (1 BULAN INI) -->
    <div class="row g-3 mb-4">
        
        <!-- Box 1: Income Bulan Ini -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-success stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary small fw-semibold text-uppercase">Income (<?= date('F Y'); ?>)</span>
                        <h3 class="fw-bold text-success mb-0 mt-1">Rp <?= number_format($total_income_bulan_ini, 0, ',', '.'); ?></h3>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success fs-3">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Box 2: Produk Terjual -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-primary stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary small fw-semibold text-uppercase">Produk Terjual</span>
                        <h3 class="fw-bold text-primary mb-0 mt-1"><?= number_format($total_terjual_bulan_ini, 0, ',', '.'); ?> <span class="fs-6 text-muted fw-normal">Item</span></h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary fs-3">
                        <i class="bi bi-box-seam"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Box 3: Total Transaksi -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-info stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary small fw-semibold text-uppercase">Total Transaksi</span>
                        <h3 class="fw-bold text-info mb-0 mt-1"><?= number_format($total_trx_bulan_ini, 0, ',', '.'); ?> <span class="fs-6 text-muted fw-normal">Struk</span></h3>
                    </div>
                    <div class="bg-info bg-opacity-10 p-3 rounded-circle text-info fs-3">
                        <i class="bi bi-receipt"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- CARD TABEL & FILTER DATA -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">

        <!-- Form Filter di Atas Tabel -->
        <div class="card-body bg-light border-bottom py-3 px-4">
            <form method="GET" action="" class="row g-2 align-items-end">
                
                <!-- Pencarian -->
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-secondary mb-1">Cari Transaksi / Pelanggan</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari ID atau nama..." value="<?= htmlspecialchars($search); ?>">
                    </div>
                </div>

                <!-- Tanggal Mulai -->
                <div class="col-md-2 col-6">
                    <label class="form-label small fw-semibold text-secondary mb-1">Dari Tanggal</label>
                    <input type="date" name="tgl_mulai" class="form-control form-control-sm" value="<?= htmlspecialchars($tgl_mulai); ?>">
                </div>

                <!-- Tanggal Sampai -->
                <div class="col-md-2 col-6">
                    <label class="form-label small fw-semibold text-secondary mb-1">Sampai Tanggal</label>
                    <input type="date" name="tgl_sampai" class="form-control form-control-sm" value="<?= htmlspecialchars($tgl_sampai); ?>">
                </div>

                <!-- Limit Tampilan (Maksimal Default: 7) -->
                <div class="col-md-2 col-6">
                    <label class="form-label small fw-semibold text-secondary mb-1">Tampilkan Maksimal</label>
                    <select name="limit" class="form-select form-select-sm">
                        <option value="7" <?= $limit == '7' ? 'selected' : ''; ?>>7 Data Terbaru</option>
                        <option value="15" <?= $limit == '15' ? 'selected' : ''; ?>>15 Data</option>
                        <option value="30" <?= $limit == '30' ? 'selected' : ''; ?>>30 Data</option>
                        <option value="semua" <?= $limit == 'semua' ? 'selected' : ''; ?>>Semua Data</option>
                    </select>
                </div>

                <!-- Tombol Aksi Filter -->
                <div class="col-md-3 col-6 d-flex gap-1">
                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-semibold">
                        <i class="bi bi-filter me-1"></i> Filter
                    </button>
                    <?php if (!empty($search) || !empty($tgl_mulai) || !empty($tgl_sampai) || $limit != '7'): ?>
                        <a href="laporan.php" class="btn btn-outline-secondary btn-sm fw-semibold" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    <?php endif; ?>
                </div>

            </form>
        </div>

        <!-- Tabel Ringkasan Transaksi -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary border-bottom">
                        <tr>
                            <th class="ps-4 py-3" width="5%">No</th>
                            <th class="py-3">ID Transaksi</th>
                            <th class="py-3">Tanggal</th>
                            <th class="py-3">Pelanggan</th>
                            <th class="py-3">Kasir</th>
                            <th class="py-3">Total Harga</th>
                            <th class="text-center py-3" width="15%">Aksi</th>
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
                                        <?= !empty($d['tanggal_penjualan']) ? date('d-m-Y H:i', strtotime($d['tanggal_penjualan'])) : '-'; ?>
                                    </td>
                                    <td class="fw-semibold text-dark">
                                        <?= htmlspecialchars($d['nama_pelanggan'] ?? 'Umum'); ?>
                                    </td>
                                    <td class="text-secondary">
                                        <?= htmlspecialchars($d['nama_kasir'] ?? 'Admin'); ?>
                                    </td>
                                    <td class="fw-semibold text-success">
                                        Rp <?= number_format($d['total_harga'] ?? 0, 0, ',', '.'); ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="penjualan_detail.php?id=<?= $d['penjualanID']; ?>" class="btn btn-sm btn-light text-primary border rounded-2 px-3 py-1 shadow-sm">
                                            <i class="bi bi-eye me-1"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo '<tr><td colspan="7" class="text-center py-4 text-muted">Data transaksi tidak ditemukan.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>