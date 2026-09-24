<?php
include __DIR__ . '/../koneksi.php';
include __DIR__ . '/../header.php';

/** @var mysqli $koneksi */
date_default_timezone_set('Asia/Jakarta');

// Proses Simpan Transaksi Penjualan
if (isset($_POST['simpan'])) {
    $tgl           = mysqli_real_escape_string($koneksi, $_POST['tanggal_penjualan']) . ' ' . date('H:i:s');
    $pelangganID   = !empty($_POST['pelangganID']) ? mysqli_real_escape_string($koneksi, $_POST['pelangganID']) : null;
    $pelanggan     = $pelangganID ? "'$pelangganID'" : "NULL";
    $metode        = mysqli_real_escape_string($koneksi, $_POST['metode_pembayaran']);
    $diskon_persen = (float)($_POST['diskon'] ?? 0);
    $bayar         = (float)($_POST['bayar'] ?? 0);

    $produk_ids = $_POST['produkID'] ?? [];
    $jumlahs    = $_POST['jumlah'] ?? [];

    if (empty($produk_ids) || count($produk_ids) < 1) {
        echo "<script>alert('Pilih minimal 1 produk!'); window.history.back();</script>";
        exit();
    }

    // -------------------------------------------------------------
    // PROTEKSI BACKEND (SERVER-SIDE): Validasi Member & Voucher
    // Status Database: 1 = Aktif, 0 = Non-Aktif
    // -------------------------------------------------------------
    $is_member_aktif = false;
    if (!empty($pelangganID)) {
        $q_cek_pel = mysqli_query($koneksi, "SELECT * FROM pelanggan WHERE pelangganID = '$pelangganID'");
        $d_pel = mysqli_fetch_assoc($q_cek_pel);
        if ($d_pel && (int)($d_pel['status'] ?? 0) === 1) {
            $is_member_aktif = true;
        }
    }

    // Jika memilih diskon/voucher tapi bukan member aktif, batalkan!
    if ($diskon_persen > 0 && !$is_member_aktif) {
        $q_cek_diskon = mysqli_query($koneksi, "SELECT * FROM diskon WHERE persen = '$diskon_persen' OR diskon = '$diskon_persen' LIMIT 1");
        $d_diskon = mysqli_fetch_assoc($q_cek_diskon);
        $khusus_member = $d_diskon['khusus_member'] ?? 1;

        if ($khusus_member == 1) {
            echo "<script>alert('Voucher ini khusus untuk Member Aktif! Transaksi dibatalkan.'); window.history.back();</script>";
            exit();
        }
    }

    $subtotal = 0;
    $items    = [];

    // Validasi & Hitung Subtotal di Server
    foreach ($produk_ids as $index => $pid) {
        $pid = mysqli_real_escape_string($koneksi, $pid);
        $qty = (int)($jumlahs[$index] ?? 0);

        if (empty($pid) || $qty < 1) continue;

        $q_p = mysqli_query($koneksi, "SELECT * FROM produk WHERE produkID = '$pid'");
        $p   = mysqli_fetch_assoc($q_p);

        if ($p) {
            if ($qty > $p['stok']) {
                echo "<script>alert('Stok " . $p['nama_produk'] . " tidak mencukupi! Tersisa: " . $p['stok'] . "'); window.history.back();</script>";
                exit();
            }

            $total_item = $p['harga'] * $qty;
            $subtotal += $total_item;

            $items[] = [
                'produkID' => $pid,
                'nama'     => $p['nama_produk'],
                'harga'    => $p['harga'],
                'qty'      => $qty,
                'total'    => $total_item
            ];
        }
    }

    $nominal_diskon = ($subtotal * $diskon_persen) / 100;
    $grand_total    = $subtotal - $nominal_diskon;

    if ($metode === 'Cash' && $bayar < $grand_total) {
        echo "<script>alert('Uang pembayaran kurang!'); window.history.back();</script>";
        exit();
    }

    $kembalian = ($metode === 'Cash') ? ($bayar - $grand_total) : 0;
    if ($metode !== 'Cash') $bayar = $grand_total;

    // Ambil ID User/Kasir yang sedang login
    $userID = $_SESSION['userID'] ?? $_SESSION['id_user'] ?? 'NULL';

    // Simpan Ke Tabel Penjualan
    $simpan = mysqli_query($koneksi, "INSERT INTO penjualan 
    (tanggal_penjualan, total_harga, diskon, bayar, kembalian, metode_pembayaran, pelangganID, userID) 
    VALUES ('$tgl', '$grand_total', '$nominal_diskon', '$bayar', '$kembalian', '$metode', $pelanggan, '$userID')");

    if ($simpan) {
        $penjualanID = mysqli_insert_id($koneksi);

        foreach ($items as $item) {
            $pid           = $item['produkID'];
            $qty           = $item['qty'];
            $subtotal_item = $item['total'];

            mysqli_query($koneksi, "INSERT INTO detail_penjualan (penjualanID, produkID, jumlah, subtotal) 
                                    VALUES ('$penjualanID', '$pid', '$qty', '$subtotal_item')");

            mysqli_query($koneksi, "UPDATE produk SET stok = stok - $qty WHERE produkID = '$pid'");
        }

        echo "<script>alert('Transaksi Berhasil!'); window.location.href='penjualan.php';</script>";
        exit();
    } else {
        echo "<script>alert('Gagal menyimpan transaksi: " . mysqli_error($koneksi) . "');</script>";
    }
}

// Ambil Data Produk untuk Dropdown JS
$q_produk = mysqli_query($koneksi, "SELECT * FROM produk WHERE stok > 0");
$produk_list = [];
while ($row = mysqli_fetch_assoc($q_produk)) {
    $produk_list[] = $row;
}
?>

<div class="container-fluid px-4 py-3">
    <form method="POST" id="formKasir">
        <div class="row g-3">

            <!-- Kolom Kiri: Pilih Barang Multi-Item -->
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-3 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0"><i class="bi bi-cart-plus me-2 text-primary"></i>Item Transaksi</h5>
                        <button type="button" class="btn btn-outline-primary btn-sm fw-semibold" onclick="tambahBaris()">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Barang
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle" id="tabelKasir">
                            <thead class="bg-light">
                                <tr>
                                    <th width="40%">Produk</th>
                                    <th width="20%">Harga</th>
                                    <th width="15%">Qty</th>
                                    <th width="20%">Total</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody id="containerBarang">
                                <!-- Baris Pertama Dinamis dari JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Rincian Pembayaran & Informasi -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3 p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-receipt me-2 text-primary"></i>Rincian Bayar</h5>

                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Tanggal</label>
                        <input type="date" name="tanggal_penjualan" class="form-control form-control-sm" value="<?= date('Y-m-d'); ?>" required>
                    </div>

                    <!-- DROPDOWN PELANGGAN / MEMBER -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small fw-semibold mb-0">Pelanggan</label>
                            <a href="pelanggan_tambah.php" class="small text-decoration-none">+ Pelanggan Baru</a>
                        </div>
                        <select name="pelangganID" id="pelangganID" class="form-select form-select-sm" onchange="cekAksesVoucher()">
                            <option value="" data-status="0">-- Umum / Non-Member --</option>
                            <?php
                            $q_pel = mysqli_query($koneksi, "SELECT * FROM pelanggan ORDER BY nama_pelanggan ASC");
                            while ($pel = mysqli_fetch_assoc($q_pel)) {
                                $status_val = (int)($pel['status'] ?? 0); // 1 = Aktif, 0 = Non-Aktif
                                $label_status = ($status_val === 1) ? '' : ' (Non-Aktif)';
                                echo "<option value='" . $pel['pelangganID'] . "' data-status='" . $status_val . "'>Member: " . htmlspecialchars($pel['nama_pelanggan']) . $label_status . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <hr class="my-2">

                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-secondary small">Subtotal</span>
                        <span class="fw-semibold small" id="textSubtotal">Rp 0</span>
                    </div>

                    <!-- DROPDOWN VOUCHER / DISKON -->
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Pilih Diskon / Voucher</label>
                        <select name="diskon" id="diskon" class="form-select form-select-sm" onchange="hitungKasir()">
                            <option value="0">-- Tanpa Diskon --</option>
                            <?php
                            $q_diskon = mysqli_query($koneksi, "SELECT * FROM diskon WHERE status = 'Aktif' OR status = '1'");
                            if ($q_diskon && mysqli_num_rows($q_diskon) > 0) {
                                while ($d = mysqli_fetch_assoc($q_diskon)) {
                                    $persen = $d['persen'] ?? 0;
                                    $nama   = $d['nama_diskon'] ?? $d['kode_voucher'];
                                    // Mengambil kolom khusus_member (Default 1 jika tidak ditemukan)
                                    $khusus = $d['khusus_member'] ?? 1;

                                    echo "<option value='" . $persen . "' data-khusus-member='" . $khusus . "'>"
                                        . htmlspecialchars($nama) . " (" . $persen . "%)"
                                        . "</option>";
                                }
                            }
                            ?>
                        </select>
                        <small id="pesanVoucher" class="text-danger small d-none">* Voucher khusus untuk Member Aktif!</small>
                    </div>

                    <div class="d-flex justify-content-between align-items-center bg-primary bg-opacity-10 p-2 rounded mb-3">
                        <span class="fw-bold text-primary">Grand Total</span>
                        <span class="fw-bold fs-5 text-primary" id="textGrandTotal">Rp 0</span>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Metode Pembayaran</label>
                        <select name="metode_pembayaran" id="metode_pembayaran" class="form-select form-select-sm" onchange="toggleMetode()">
                            <option value="Cash">Cash / Tunai</option>
                            <option value="QRIS">QRIS</option>
                            <option value="Transfer Bank">Transfer Bank</option>
                        </select>
                    </div>

                    <!-- Input khusus Cash -->
                    <div id="boxCash">
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Uang Diterima (Rp)</label>
                            <input type="number" name="bayar" id="bayar" class="form-control form-control-sm" placeholder="0" oninput="hitungKasir()">
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-secondary small">Kembalian</span>
                            <span class="fw-bold text-success" id="textKembalian">Rp 0</span>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-3">
                        <button type="submit" name="simpan" class="btn btn-primary fw-semibold py-2">
                            <i class="bi bi-printer me-1"></i> Simpan & Transaksi
                        </button>
                        <a href="penjualan.php" class="btn btn-outline-secondary btn-sm">Batal</a>
                    </div>

                </div>
            </div>

        </div>
    </form>
</div>

<script>
    const listProduk = <?= json_encode($produk_list); ?>;
    let globalGrandTotal = 0;

    // FUNGSI CEK MEMBER & VOUCHER
    function cekAksesVoucher() {
        const selectPelanggan = document.getElementById('pelangganID');
        const optPelanggan = selectPelanggan.selectedOptions[0];
        const statusPelanggan = parseInt(optPelanggan.getAttribute('data-status')) || 0;

        const selectDiskon = document.getElementById('diskon');
        const optionsDiskon = selectDiskon.querySelectorAll('option');
        const pesanVoucher = document.getElementById('pesanVoucher');

        let memberAktif = (statusPelanggan === 1);

        optionsDiskon.forEach(opt => {
            const isKhususMember = opt.getAttribute('data-khusus-member');
            if (isKhususMember == "1" && !memberAktif) {
                opt.disabled = true;
                if (!opt.text.includes('[Member Only]')) {
                    opt.text = opt.text + ' [Member Only]';
                }
            } else {
                opt.disabled = false;
                opt.text = opt.text.replace(' [Member Only]', '');
            }
        });

        if (selectDiskon.selectedOptions[0].disabled) {
            selectDiskon.value = "0";
            pesanVoucher.classList.remove('d-none');
            alert('Pelanggan Umum atau Member Non-Aktif tidak dapat menggunakan voucher ini!');
        } else {
            pesanVoucher.classList.add('d-none');
        }

        hitungKasir();
    }

    function tambahBaris() {
        const container = document.getElementById('containerBarang');
        const rowId = Date.now();

        let options = '<option value="" data-harga="0" data-stok="0">-- Pilih Produk --</option>';
        listProduk.forEach(p => {
            options += `<option value="${p.produkID}" data-harga="${p.harga}" data-stok="${p.stok}">${p.nama_produk} (Stok: ${p.stok})</option>`;
        });

        const tr = document.createElement('tr');
        tr.id = `baris-${rowId}`;
        tr.innerHTML = `
        <td>
            <select name="produkID[]" class="form-select form-select-sm produk-select" onchange="pilihProduk(this, ${rowId})" required>
                ${options}
            </select>
        </td>
        <td class="harga-text small fw-semibold">Rp 0</td>
        <td>
            <input type="number" name="jumlah[]" class="form-control form-control-sm qty-input" min="1" value="1" oninput="hitungBaris(${rowId})" required>
        </td>
        <td class="total-text small fw-bold text-primary" data-total="0">Rp 0</td>
        <td>
            <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="hapusBaris(${rowId})">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
        container.appendChild(tr);
    }

    function hapusBaris(rowId) {
        const row = document.getElementById(`baris-${rowId}`);
        if (document.querySelectorAll('#containerBarang tr').length > 1) {
            row.remove();
            hitungKasir();
        } else {
            alert('Minimal 1 barang dalam transaksi!');
        }
    }

    function pilihProduk(selectElem, rowId) {
        const opt = selectElem.selectedOptions[0];
        const row = document.getElementById(`baris-${rowId}`);
        const harga = parseFloat(opt.getAttribute('data-harga')) || 0;

        row.querySelector('.harga-text').innerText = 'Rp ' + harga.toLocaleString('id-ID');
        hitungBaris(rowId);
    }

    function hitungBaris(rowId) {
        const row = document.getElementById(`baris-${rowId}`);
        const opt = row.querySelector('.produk-select').selectedOptions[0];
        const harga = parseFloat(opt.getAttribute('data-harga')) || 0;
        const stok = parseInt(opt.getAttribute('data-stok')) || 0;
        let qtyInput = row.querySelector('.qty-input');
        let qty = parseInt(qtyInput.value) || 0;

        if (qty > stok && stok > 0) {
            alert('Stok tersisa: ' + stok);
            qtyInput.value = stok;
            qty = stok;
        }

        const total = harga * qty;
        row.querySelector('.total-text').innerText = 'Rp ' + total.toLocaleString('id-ID');
        row.querySelector('.total-text').setAttribute('data-total', total);

        hitungKasir();
    }

    function hitungKasir() {
        let subtotal = 0;
        document.querySelectorAll('.total-text').forEach(el => {
            subtotal += parseFloat(el.getAttribute('data-total')) || 0;
        });

        const diskonPersen = parseFloat(document.getElementById('diskon').value) || 0;
        const nominalDiskon = (subtotal * diskonPersen) / 100;
        const grandTotal = subtotal - nominalDiskon;
        globalGrandTotal = grandTotal;

        document.getElementById('textSubtotal').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
        document.getElementById('textGrandTotal').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');

        // Hitung Kembalian jika Cash
        const uangBayar = parseFloat(document.getElementById('bayar').value) || 0;
        const kembalian = uangBayar - grandTotal;

        const elemKembalian = document.getElementById('textKembalian');
        if (kembalian < 0) {
            elemKembalian.innerText = 'Kurang Rp ' + Math.abs(kembalian).toLocaleString('id-ID');
            elemKembalian.className = 'fw-bold text-danger';
        } else {
            elemKembalian.innerText = 'Rp ' + kembalian.toLocaleString('id-ID');
            elemKembalian.className = 'fw-bold text-success';
        }
    }

    function toggleMetode() {
        const metode = document.getElementById('metode_pembayaran').value;
        const boxCash = document.getElementById('boxCash');
        if (metode === 'Cash') {
            boxCash.style.display = 'block';
        } else {
            boxCash.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        tambahBaris();
        cekAksesVoucher();
    });
</script>