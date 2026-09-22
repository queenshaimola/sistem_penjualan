<?php include __DIR__ . '/../header.php'; ?>

<div class="container-fluid px-4 py-2">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        
        <!-- Header Card -->
        <div class="card-header bg-white py-3 px-4 border-bottom-0 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark mb-0">Data Pengguna</h5>
                <small class="text-muted">Kelola akun dan hak akses pengguna sistem</small>
            </div>
            <a href="user_tambah.php" class="btn btn-primary btn-sm px-3 py-2 rounded-3 fw-semibold shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Tambah Pengguna
            </a>
        </div>

        <!-- Tabel Data Pengguna -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary border-bottom">
                        <tr>
                            <th class="ps-4 py-3" width="5%">No</th>
                            <th class="py-3">Nama User</th>
                            <th class="py-3">Username</th>
                            <th class="py-3">Hak Akses</th>
                            <th class="text-center py-3" width="18%">Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include __DIR__ . '/../koneksi.php';
                        
                        $data = mysqli_query($koneksi, "SELECT * FROM user ORDER BY userID DESC");
                        $no = 1;
                        
                        if ($data && mysqli_num_rows($data) > 0) {
                            while ($d = mysqli_fetch_array($data)) {
                        ?>
                            <tr>
                                <td class="ps-4 fw-medium text-secondary"><?= $no++; ?></td>
                                <td class="fw-semibold text-dark"><?= htmlspecialchars($d['nama_user'] ?? '-'); ?></td>
                                <td class="text-secondary"><?= htmlspecialchars($d['username'] ?? '-'); ?></td>
                                <td>
                                    <?php 
                                    $akses = $d['akses'] ?? '';
                                    if ($akses == '1' || $akses == 1) {
                                        echo '<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-2">Admin</span>';
                                    } else {
                                        echo '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 rounded-2">Petugas</span>';
                                    }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <a href="user_edit.php?id=<?= $d['userID']; ?>" class="btn btn-sm btn-light text-primary border rounded-2 px-3 py-1 me-1 shadow-sm">
                                        <i class="bi bi-pencil-square me-1"></i>Edit
                                    </a>
                                    <a href="user_hapus.php?id=<?= $d['userID']; ?>" class="btn btn-sm btn-light text-danger border rounded-2 px-3 py-1 shadow-sm" onclick="return confirm('Yakin ingin menghapus pengguna ini?')">
                                        <i class="bi bi-trash me-1"></i>Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php 
                            }
                        } else {
                            echo '<tr><td colspan="5" class="text-center py-4 text-muted">Belum ada data pengguna.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
