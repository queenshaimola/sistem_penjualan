<?php
include __DIR__ . '/../koneksi.php';

$tgl_mulai  = isset($_GET['tgl_mulai']) ? $_GET['tgl_mulai'] : '';
$tgl_sampai = isset($_GET['tgl_sampai']) ? $_GET['tgl_sampai'] : '';
$search     = isset($_GET['search']) ? trim($_GET['search']) : '';

// Header untuk memaksa browser mengunduh format Excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Penjualan_".date('d-m-Y').".xls");

$where_clauses = [];
if (!empty($search)) {
    $search_escaped  = mysqli_real_escape_string($koneksi, $search);
    $where_clauses[] = "(penjualan.penjualanID LIKE '%$search_escaped%' OR pelanggan.nama_pelanggan LIKE '%$search_escaped%')";
}
if (!empty($tgl_mulai) && !empty($tgl_sampai)) {
    $tgl_m = mysqli_real_escape_string($koneksi, $tgl_mulai);
    $tgl_s = mysqli_real_escape_string($koneksi, $tgl_sampai);
    $where_clauses[] = "(DATE(penjualan.tanggal_penjualan) BETWEEN '$tgl_m' AND '$tgl_s')";
}

$where_sql = count($where_clauses) > 0 ? "WHERE " . implode(' AND ', $where_clauses) : "";

// Export semua data tanpa pembatasan limit
$query = "SELECT penjualan.*, 
                 pelanggan.nama_pelanggan, 
                 IFNULL(user.nama_user, 'Admin') AS nama_kasir 
          FROM penjualan 
          LEFT JOIN pelanggan ON penjualan.pelangganID = pelanggan.pelangganID 
          LEFT JOIN user ON penjualan.userID = user.userID
          $where_sql
          ORDER BY penjualan.penjualanID DESC";

$data = mysqli_query($koneksi, $query);
?>

<h2>LAPORAN PENJUALAN</h2>
<p>Periode: <?= !empty($tgl_mulai) ? date('d-m-Y', strtotime($tgl_mulai)) : 'Semua'; ?> s/d <?= !empty($tgl_sampai) ? date('d-m-Y', strtotime($tgl_sampai)) : 'Semua'; ?></p>

<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th>No</th>
            <th>ID Transaksi</th>
            <th>Tanggal</th>
            <th>Pelanggan</th>
            <th>Kasir</th>
            <th>Metode</th>
            <th>Total Harga</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        $grand_total = 0;
        if ($data && mysqli_num_rows($data) > 0) {
            while ($d = mysqli_fetch_array($data)) { 
                $grand_total += $d['total_harga'];
        ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td>#<?= $d['penjualanID']; ?></td>
                    <td><?= date('d-m-Y H:i', strtotime($d['tanggal_penjualan'])); ?></td>
                    <td><?= htmlspecialchars($d['nama_pelanggan'] ?? 'Umum'); ?></td>
                    <td><?= htmlspecialchars($d['nama_kasir'] ?? 'Admin'); ?></td>
                    <td><?= htmlspecialchars($d['metode_pembayaran'] ?? 'Cash'); ?></td>
                    <td><?= $d['total_harga']; ?></td>
                </tr>
        <?php 
            }
        } else {
            echo '<tr><td colspan="7" align="center">Data tidak ditemukan.</td></tr>';
        }
        ?>
        <tr>
            <th colspan="6" align="right">TOTAL KESELURUHAN:</th>
            <th><?= $grand_total; ?></th>
        </tr>
    </tbody>
</table>