<?php

$koneksi = mysqli_connect("127.0.0.01", "root", "", "penjualan");

if (mysqli_connect_errno()) {
    echo "Koneksi database gagal: " . mysqli_connect_error();
    exit();
}

$sql_auto_nonaktif = "
    UPDATE pelanggan p
    LEFT JOIN (
        SELECT pelangganID, MAX(tanggal_penjualan) AS last_trx
        FROM penjualan
        WHERE pelangganID IS NOT NULL
        GROUP BY pelangganID
    ) t ON p.pelangganID = t.pelangganID
    SET p.status = 0
    WHERE p.status = 1
      AND t.last_trx IS NOT NULL 
      AND t.last_trx < NOW() - INTERVAL 14 DAY
";

@mysqli_query($koneksi, $sql_auto_nonaktif);

?>