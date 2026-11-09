
<?php
// Pengaman login
require_once __DIR__ . '/../../auth/index.php';
checkAuth();

// Koneksi database
require_once __DIR__ . '/../../config/database.php';

// Mengambil data kategori
$queryKategori = $db->query(
    "SELECT * FROM kategori ORDER BY nama_kategori ASC"
);
$kategori = $queryKategori->fetchAll(PDO::FETCH_ASSOC);

// Menangkap pencarian dan filter
$cari = trim($_GET['cari'] ?? '');
$filterKategori = $_GET['kategori'] ?? '';

// Query data barang
$sql = "SELECT barang.*, kategori.nama_kategori
        FROM barang
        JOIN kategori ON barang.kategori_id = kategori.id
        WHERE 1=1";

$params = [];

if ($cari !== '') {
    $sql .= " AND (
        barang.nama_barang LIKE :nama
        OR barang.kode_barang LIKE :kode
    )";

    $params[':nama'] = "%$cari%";
    $params[':kode'] = "%$cari%";
}

if ($filterKategori !== '' && ctype_digit((string)$filterKategori)) {
    $sql .= " AND barang.kategori_id = :kategori";
    $params[':kategori'] = (int)$filterKategori;
}

$sql .= " ORDER BY barang.id DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$dataBarang = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fungsi mengamankan output
function aman($data) {
    return htmlspecialchars((string)$data, ENT_QUOTES, 'UTF-8');
}

// Memanggil layout
require_once __DIR__ . '/../../layout/header.php';
?>
<?php if (isset($_SESSION['pesan'])): ?>
    <div style="
        background: #dbeafe;
        color: #1e40af;
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 15px;
    ">
        <?= htmlspecialchars($_SESSION['pesan']) ?>
    </div>
    <?php unset($_SESSION['pesan']); ?>
<?php endif; ?>

<style>
    .container-barang {
        background: white;
        padding: 25px;
        border-radius: 10px;
    }

    .container-barang h2 {
        color: #1e40af;
    }

    .container-barang input,
    .container-barang select,
    .container-barang button,
    .container-barang .tambah {
        padding: 10px;
        margin: 5px 0;
        border-radius: 5px;
    }

    .container-barang input,
    .container-barang select {
        border: 1px solid #ccc;
    }

    .container-barang button,
    .container-barang .tambah {
        background: #2563eb;
        color: white;
        border: none;
        cursor: pointer;
        text-decoration: none;
    }

    .container-barang table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .container-barang th,
    .container-barang td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }

    .container-barang th {
        background: #2563eb;
        color: white;
    }

    .tersedia { color: green; font-weight: bold; }
    .kritis { color: orange; font-weight: bold; }
    .habis { color: red; font-weight: bold; }
    .edit { color: blue; }
    .hapus { color: red; }

    .kembali {
        display: inline-block;
        padding: 10px 15px;
        background: #64748b;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        margin-top: 20px;
    }
    .table-wrapper {
    width: 100%;
    overflow-x: auto;
}

.table-wrapper table {
    min-width: 900px;
}
</style>

<div class="container-barang">

    <h2>Data Barang</h2>
    <p>Daftar inventaris barang yang tersedia.</p>

    <a href="tambah.php" class="tambah">+ Tambah Barang</a>

    <form method="GET" action="index.php">

        <input
            type="text"
            name="cari"
            placeholder="Cari nama atau kode barang"
            value="<?= aman($cari) ?>"
        >

        <select name="kategori">
            <option value="">Semua Kategori</option>

            <?php foreach ($kategori as $kat): ?>
                <option
                    value="<?= (int)$kat['id'] ?>"
                    <?= ((string)$filterKategori === (string)$kat['id']) ? 'selected' : '' ?>
                >
                    <?= aman($kat['nama_kategori']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Cari / Filter</button>
        <a href="index.php">Reset</a>

    </form>

    <div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Satuan</th>
                <th>Harga Beli</th>
                <th>Harga Jual</th>
                <th>Stok</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>

        <?php if (count($dataBarang) > 0): ?>

            <?php $no = 1; ?>

            <?php foreach ($dataBarang as $barang): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= aman($barang['kode_barang']) ?></td>
                    <td><?= aman($barang['nama_barang']) ?></td>
                    <td><?= aman($barang['nama_kategori']) ?></td>
                    <td><?= aman($barang['satuan']) ?></td>
                    <td>Rp <?= number_format($barang['harga_beli'], 0, ',', '.') ?></td>
                    <td>Rp <?= number_format($barang['harga_jual'], 0, ',', '.') ?></td>
                    <td><?= aman($barang['stok']) ?></td>

                    <td>
                        <?php if ($barang['stok'] <= 0): ?>
                            <span class="habis">Habis</span>
                        <?php elseif ($barang['stok'] <= $barang['stok_min']): ?>
                            <span class="kritis">Stok Kritis</span>
                        <?php else: ?>
                            <span class="tersedia">Tersedia</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <a class="edit"
                           href="edit.php?id=<?= aman($barang['id']) ?>">
                            Edit
                        </a>
                        |
                        <a class="hapus"
                           href="hapus.php?id=<?= aman($barang['id']) ?>"
                           onclick="return confirm('Yakin ingin menghapus barang ini?')">
                            Hapus
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>

        <?php else: ?>
            <tr>
                <td colspan="10" style="text-align:center;">
                    Data barang belum tersedia.
                </td>
            </tr>
        <?php endif; ?>

        </tbody>
        </table>
</div>

    <a href="../../dashboard.php" class="kembali">
        ← Kembali ke Dashboard
    </a>

</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
