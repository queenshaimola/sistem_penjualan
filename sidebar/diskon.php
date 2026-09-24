<?php 
include __DIR__ . '/../header.php';
include __DIR__ . '/../koneksi.php'; 

/** @var mysqli $koneksi */

// Proses Tambah Diskon
if (isset($_POST['tambah_diskon'])) {
    $nama          = mysqli_real_escape_string($koneksi, $_POST['nama_diskon']);
    $kode          = strtoupper(mysqli_real_escape_string($koneksi, $_POST['kode_voucher']));
    $persen        = (int)$_POST['persen'];
    $khusus_member = (int)$_POST['khusus_member']; // 1 = Member, 0 = Umum
    $status        = 'Aktif';

    $simpan = mysqli_query($koneksi, "INSERT INTO diskon (nama_diskon, kode_voucher, persen, status, khusus_member) VALUES ('$nama', '$kode', '$persen', '$status', '$khusus_member')");
    
    if ($simpan) {
        echo "<script>alert('Voucher Berhasil Ditambahkan!'); window.location.href='diskon.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan voucher: " . mysqli_error($koneksi) . "');</script>";
    }
}

// Proses Hapus Diskon
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM diskon WHERE diskonID = '$id'");
    echo "<script>window.location.href='diskon.php';</script>";
}
?>

<div class="container-fluid px-4 py-3">
    <div class="row g-4">
        
        <!-- Form Tambah Voucher -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <h6 class="fw-bold mb-3"><i class="bi bi-tag-fill me-2 text-primary"></i>Tambah Voucher Baru</h6>
                <form method="POST">
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Nama Voucher</label>
                        <input type="text" name="nama_diskon" class="form-control form-control-sm" placeholder="Contoh: Diskon Akhir Bulan" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Kode Voucher</label>
                        <input type="text" name="kode_voucher" class="form-control form-control-sm text-uppercase" placeholder="AKHRIBULAN01" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Besar Diskon (%)</label>
                        <input type="number" name="persen" class="form-control form-control-sm" min="1" max="100" placeholder="15" required>
                    </div>

                    <!-- PILIHAN TARGET VOUCHER (TAMBAHAN KELOLA MEMBER / UMUM) -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Target Pengguna</label>
                        <select name="khusus_member" class="form-select form-select-sm" required>
                            <option value="1">Khusus Member Aktif</option>
                            <option value="0">Umum (Bisa Semua Pelanggan)</option>
                        </select>
                    </div>

                    <button type="submit" name="tambah_diskon" class="btn btn-primary btn-sm w-100 fw-semibold">
                        <i class="bi bi-plus-lg me-1"></i> Simpan Voucher
                    </button>
                </form>
            </div>
        </div>

        <!-- Tabel Daftar Voucher -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 px-4 border-0">
                    <h6 class="fw-bold mb-0">Daftar Voucher / Diskon Aktif</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">No</th>
                                <th>Nama Diskon</th>
                                <th>Kode</th>
                                <th>Potongan</th>
                                <th>Target</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $q = mysqli_query($koneksi, "SELECT * FROM diskon ORDER BY diskonID DESC");
                            $no = 1;
                            if (mysqli_num_rows($q) > 0):
                                while ($d = mysqli_fetch_assoc($q)):
                                    $is_khusus = (int)($d['khusus_member'] ?? 1); // Default 1 jika bernilai null
                            ?>
                                <tr>
                                    <td class="ps-4"><?= $no++; ?></td>
                                    <td class="fw-semibold"><?= htmlspecialchars($d['nama_diskon']); ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($d['kode_voucher']); ?></span></td>
                                    <td class="fw-bold text-success"><?= $d['persen']; ?>%</td>
                                    
                                    <!-- BADGE TARGET VOUCHER -->
                                    <td>
                                        <?php if ($is_khusus === 1): ?>
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">
                                                <i class="bi bi-star-fill me-1"></i> Member Only
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border">
                                                <i class="bi bi-people-fill me-1"></i> Umum
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td><span class="badge bg-success bg-opacity-10 text-success"><?= htmlspecialchars($d['status'] ?? 'Aktif'); ?></span></td>
                                    <td class="text-center">
                                        <a href="diskon.php?hapus=<?= $d['diskonID']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus voucher ini?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php 
                                endwhile;
                            else:
                                echo '<tr><td colspan="7" class="text-center text-muted py-3">Belum ada voucher.</td></tr>';
                            endif;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>