
<?php
// Pengaman login
require_once __DIR__ . '/../../auth/index.php';
checkAuth();

// Koneksi database
require_once __DIR__ . '/../../config/database.php';

// Mengambil data transaksi beserta nama barang dan supplier
$sql = "SELECT
            t.*,
            b.kode_barang,
            b.nama_barang,
            s.nama_supplier
        FROM transaksi_stok t
        INNER JOIN barang b ON t.barang_id = b.id
        LEFT JOIN supplier s ON t.supplier_id = s.id
        ORDER BY t.id DESC";

$stmt = $db->query($sql);
$dataTransaksi = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mengamankan output
function amanTransaksi($data) {
    return htmlspecialchars(
        (string)$data,
        ENT_QUOTES,
        'UTF-8'
    );
}

// Memanggil layout utama
require_once __DIR__ . '/../../layout/header.php';
?>

<style>
    .container-transaksi {
        background: white;
        padding: 25px;
        border-radius: 10px;
    }

    .container-transaksi h2 {
        color: #1e40af;
    }

    .container-transaksi .tombol,
    .container-transaksi .dashboard {
        display: inline-block;
        padding: 10px 15px;
        margin-bottom: 15px;
        background: #2563eb;
        color: white;
        text-decoration: none;
        border-radius: 5px;
    }

    .container-transaksi .dashboard {
        background: #64748b;
    }

    .container-transaksi .table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .container-transaksi table {
        width: 100%;
        min-width: 1100px;
        border-collapse: collapse;
        background: white;
    }

    .container-transaksi th,
    .container-transaksi td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: left;
    }

    .container-transaksi th {
        background: #2563eb;
        color: white;
    }

    .container-transaksi tr:nth-child(even) {
        background: #f2f2f2;
    }

    .container-transaksi .masuk {
        color: green;
        font-weight: bold;
    }

    .container-transaksi .keluar {
        color: red;
        font-weight: bold;
    }

    .container-transaksi .hapus {
        color: red;
    }
</style>

<div class="container-transaksi">

    <h2>Histori Transaksi</h2>

    <p>Riwayat barang masuk dan barang keluar.</p>

    <a href="../../dashboard.php" class="dashboard">
        Kembali ke Dashboard
    </a>

    <a href="tambah.php" class="tombol">
        + Tambah Transaksi
    </a>

    <div class="table-wrapper">

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>No Transaksi</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Jenis</th>
                    <th>Jumlah</th>
                    <th>Supplier</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th>Petugas</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

            <?php if (count($dataTransaksi) > 0): ?>

                <?php $no = 1; ?>

                <?php foreach ($dataTransaksi as $transaksi): ?>

                    <tr>
                        <td><?= $no++ ?></td>

                        <td>
                            <?= amanTransaksi($transaksi['no_transaksi']) ?>
                        </td>

                        <td>
                            <?= amanTransaksi($transaksi['kode_barang']) ?>
                        </td>

                        <td>
                            <?= amanTransaksi($transaksi['nama_barang']) ?>
                        </td>

                        <td class="<?= $transaksi['jenis_transaksi'] == 'Masuk' ? 'masuk' : 'keluar' ?>">
                            <?= amanTransaksi($transaksi['jenis_transaksi']) ?>
                        </td>

                        <td>
                            <?= amanTransaksi($transaksi['jumlah']) ?>
                        </td>

                        <td>
                            <?= amanTransaksi($transaksi['nama_supplier'] ?? '-') ?>
                        </td>

                        <td>
                            <?= amanTransaksi($transaksi['tanggal']) ?>
                        </td>

                        <td>
                            <?= amanTransaksi($transaksi['keterangan']) ?>
                        </td>

                        <td>
                            <?= amanTransaksi($transaksi['petugas']) ?>
                        </td>

                        <td>
                            <a class="hapus"
                               href="hapus.php?id=<?= (int)$transaksi['id'] ?>"
                               onclick="return confirm('Yakin ingin membatalkan transaksi ini?')">
                                Batalkan
                            </a>
                        </td>
                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="11" style="text-align:center;">
                        Belum ada transaksi stok.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>
        </table>

    </div>

</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
