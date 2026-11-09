
<?php
// Pengaman login
require_once __DIR__ . '/../../auth/index.php';
checkAuth();

// Koneksi database
require_once __DIR__ . '/../../config/database.php';

// Mengambil seluruh data supplier
$stmt = $db->query(
    "SELECT * FROM supplier ORDER BY id DESC"
);

$dataSupplier = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mengamankan output
function amanSupplier($data) {
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
    .container-supplier {
        background: white;
        padding: 25px;
        border-radius: 10px;
    }

    .container-supplier h2 {
        color: #1e40af;
    }

    .container-supplier .tombol,
    .container-supplier .dashboard {
        display: inline-block;
        padding: 10px 15px;
        margin-bottom: 15px;
        background: #2563eb;
        color: white;
        text-decoration: none;
        border-radius: 5px;
    }

    .container-supplier .dashboard {
        background: #64748b;
    }

    .container-supplier table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        background: white;
    }

    .container-supplier th,
    .container-supplier td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: left;
    }

    .container-supplier th {
        background: #2563eb;
        color: white;
    }

    .container-supplier tr:nth-child(even) {
        background: #f2f2f2;
    }

    .container-supplier .edit {
        color: blue;
    }

    .container-supplier .hapus {
        color: red;
    }
    .table-wrapper {
    width: 100%;
    overflow-x: auto;
}

.table-wrapper table {
    min-width: 850px;
}
</style>

<div class="container-supplier">

    <h2>Daftar Supplier</h2>

    <p>Data Supplier Rekanan AutoStock K4.</p>

    <a href="../../dashboard.php" class="dashboard">
        Kembali ke Dashboard
    </a>

    <a href="tambah.php" class="tombol">
        + Tambah Supplier
    </a>

    <div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Supplier</th>
                <th>Nama Supplier</th>
                <th>Telepon</th>
                <th>Alamat</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>

        <?php if (count($dataSupplier) > 0): ?>

            <?php $no = 1; ?>

            <?php foreach ($dataSupplier as $supplier): ?>

                <tr>
                    <td><?= $no++ ?></td>

                    <td>
                        <?= amanSupplier($supplier['kode_supplier']) ?>
                    </td>

                    <td>
                        <?= amanSupplier($supplier['nama_supplier']) ?>
                    </td>

                    <td>
                        <?= amanSupplier($supplier['telepon']) ?>
                    </td>

                    <td>
                        <?= amanSupplier($supplier['alamat']) ?>
                    </td>

                    <td>
                        <a class="edit"
                           href="edit.php?id=<?= (int)$supplier['id'] ?>">
                            Edit
                        </a>

                        |

                        <a class="hapus"
                           href="hapus.php?id=<?= (int)$supplier['id'] ?>"
                           onclick="return confirm('Yakin ingin menghapus supplier ini?')">
                            Hapus
                        </a>
                    </td>
                </tr>

            <?php endforeach; ?>

        <?php else: ?>

            <tr>
                <td colspan="6" style="text-align:center;">
                    Belum ada data supplier.
                </td>
            </tr>

        <?php endif; ?>

        </tbody>
    </table>
        </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
