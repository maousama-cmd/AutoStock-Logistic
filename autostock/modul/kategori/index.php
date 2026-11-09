
<?php
// Pengaman login
require_once __DIR__ . '/../../auth/index.php';
checkAuth();

// Koneksi database
require_once __DIR__ . '/../../config/database.php';

// Mengambil seluruh data kategori
$stmt = $db->query(
    "SELECT * FROM kategori ORDER BY id DESC"
);

$dataKategori = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mengamankan output
function amanKategori($data) {
    return htmlspecialchars((string)$data, ENT_QUOTES, 'UTF-8');
}

// Memanggil layout
require_once __DIR__ . '/../../layout/header.php';
?>

<style>
    .container-kategori {
        background: white;
        padding: 25px;
        border-radius: 10px;
    }

    .container-kategori h2 {
        color: #1e40af;
    }

    .container-kategori .tambah,
    .container-kategori .dashboard {
        display: inline-block;
        padding: 10px 15px;
        border-radius: 5px;
        text-decoration: none;
        margin-bottom: 15px;
    }

    .container-kategori .tambah {
        background: #2563eb;
        color: white;
    }

    .container-kategori .dashboard {
        background: #64748b;
        color: white;
    }

    .container-kategori table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    .container-kategori th,
    .container-kategori td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: left;
    }

    .container-kategori th {
        background: #2563eb;
        color: white;
    }

    .container-kategori .edit {
        color: blue;
    }

    .container-kategori .hapus {
        color: red;
    }
    .table-wrapper {
    width: 100%;
    overflow-x: auto;
}

.table-wrapper table {
    min-width: 750px;
}
</style>

<div class="container-kategori">

    <h2>Data Kategori Barang</h2>

    <p>Daftar kategori barang inventaris.</p>

    <a href="../../dashboard.php" class="dashboard">
        Kembali ke Dashboard
    </a>

    <a href="tambah.php" class="tambah">
        + Tambah Kategori
    </a>

    <div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Kategori</th>
                <th>Nama Kategori</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>

        <?php if (count($dataKategori) > 0): ?>

            <?php $no = 1; ?>

            <?php foreach ($dataKategori as $kat): ?>
                <tr>
                    <td><?= $no++ ?></td>

                    <td>
                        <?= amanKategori($kat['kode_kategori']) ?>
                    </td>

                    <td>
                        <?= amanKategori($kat['nama_kategori']) ?>
                    </td>

                    <td>
                        <?= amanKategori($kat['keterangan']) ?>
                    </td>

                    <td>
                        <a class="edit"
                           href="edit.php?id=<?= (int)$kat['id'] ?>">
                            Edit
                        </a>

                        |

                        <a class="hapus"
                           href="hapus.php?id=<?= (int)$kat['id'] ?>"
                           onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                            Hapus
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>

        <?php else: ?>
            <tr>
                <td colspan="5" style="text-align:center;">
                    Belum ada data kategori.
                </td>
            </tr>
        <?php endif; ?>

        </tbody>
    </table>
        </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
