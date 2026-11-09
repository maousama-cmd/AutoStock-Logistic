
<?php
require_once __DIR__ . '/config/database.php';

// Mengambil statistik gudang
$totalBarang = $db->query(
    "SELECT COUNT(*) FROM barang"
)->fetchColumn();

$totalStok = $db->query(
    "SELECT COALESCE(SUM(stok), 0) FROM barang"
)->fetchColumn();

$totalKategori = $db->query(
    "SELECT COUNT(*) FROM kategori"
)->fetchColumn();

$totalSupplier = $db->query(
    "SELECT COUNT(*) FROM supplier"
)->fetchColumn();

// Mengambil daftar kategori
$stmtKategori = $db->query(
    "SELECT * FROM kategori ORDER BY nama_kategori ASC"
);
$dataKategori = $stmtKategori->fetchAll();

// Mengambil input pencarian dan filter
$cari = trim($_GET['cari'] ?? '');
$kategori_id = (int)($_GET['kategori_id'] ?? 0);

// Membuat query pencarian
$sql = "SELECT
            b.*,
            k.nama_kategori
        FROM barang b
        LEFT JOIN kategori k ON b.kategori_id = k.id
        WHERE 1=1";

$params = [];

if ($cari !== '') {
    $sql .= " AND (b.nama_barang LIKE :nama
              OR b.kode_barang LIKE :kode)";

    $params[':nama'] = "%$cari%";
    $params[':kode'] = "%$cari%";
}

if ($kategori_id > 0) {
    $sql .= " AND b.kategori_id = :kategori_id";
    $params[':kategori_id'] = $kategori_id;
}

$sql .= " ORDER BY b.nama_barang ASC";

$stmtBarang = $db->prepare($sql);
$stmtBarang->execute($params);
$dataBarang = $stmtBarang->fetchAll();

// Fungsi keamanan output
function amanPublik($data) {
    return htmlspecialchars(
        (string)$data,
        ENT_QUOTES,
        'UTF-8'
    );
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AutoStock K4 - Smart Inventory Management</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            line-height: 1.6;
        }

        header {
            background: #0f172a;
            color: white;
            padding: 20px 7%;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }

        nav h2 {
            color: #38bdf8;
        }

        nav a {
            color: white;
            text-decoration: none;
            background: #2563eb;
            padding: 10px 18px;
            border-radius: 6px;
        }

        .hero {
            background: #1e3a8a;
            color: white;
            padding: 65px 7%;
            text-align: center;
        }

        .hero h1 {
            font-size: 36px;
            margin-bottom: 12px;
        }

        .hero p {
            max-width: 650px;
            margin: auto;
            color: #dbeafe;
        }

        .container {
            max-width: 1150px;
            margin: 35px auto;
            padding: 0 20px;
        }

        .statistik {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
        }

        .kartu {
            background: white;
            padding: 22px;
            border-radius: 10px;
            box-shadow: 0 2px 8px #00000010;
        }

        .kartu h3 {
            color: #64748b;
            font-size: 15px;
        }

        .kartu h2 {
            color: #2563eb;
            font-size: 28px;
            margin-top: 8px;
        }

        .judul {
            margin: 35px 0 18px;
        }

        .filter {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        input, select {
            padding: 11px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
        }

        input {
            flex: 1;
        }

        button {
            background: #2563eb;
            color: white;
            border: none;
            padding: 11px 20px;
            border-radius: 6px;
            cursor: pointer;
        }

        .tabel {
            overflow-x: auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px #00000010;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 650px;
        }

        caption {
            font-weight: bold;
            padding: 15px;
            text-align: left;
        }

        th, td {
            padding: 13px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }

        th {
            background: #1e3a8a;
            color: white;
        }

        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .tersedia {
            background: #dcfce7;
            color: #166534;
        }

        .kritis {
            background: #fef3c7;
            color: #92400e;
        }

        .habis {
            background: #fee2e2;
            color: #991b1b;
        }

        .kosong {
            text-align: center;
            padding: 25px;
        }

        footer {
            background: #0f172a;
            color: white;
            text-align: center;
            padding: 22px;
            margin-top: 50px;
        }

        @media (max-width: 768px) {
            .statistik {
                grid-template-columns: repeat(2, 1fr);
            }

            .hero h1 {
                font-size: 27px;
            }

            .filter {
                flex-direction: column;
            }

            nav {
                flex-direction: column;
            }
        }

        @media (max-width: 450px) {
            .statistik {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<header>
    <nav>
        <h2>AutoStock Logistic</h2>

        <a href="/autostock/auth/login.php">
    Portal Petugas
</a>
    </nav>
</header>

<section class="hero">
    <h1>Smart Inventory Management</h1>

    <p>
        Selamat datang di AutoStock Logistic.
        Sistem informasi untuk melihat katalog suku cadang,
        memantau persediaan, dan mengetahui kondisi stok gudang.
    </p>
</section>

<main class="container">

    <section>
        <h2 class="judul">Ringkasan</h2>

        <div class="statistik">

            <article class="kartu">
                <h3>Total Jenis Barang</h3>
                <h2><?= amanPublik($totalBarang) ?></h2>
            </article>

            <article class="kartu">
                <h3>Total Stok Fisik</h3>
                <h2><?= amanPublik($totalStok) ?></h2>
            </article>

            <article class="kartu">
                <h3>Total Kategori</h3>
                <h2><?= amanPublik($totalKategori) ?></h2>
            </article>

            <article class="kartu">
                <h3>Total Supplier</h3>
                <h2><?= amanPublik($totalSupplier) ?></h2>
            </article>

        </div>
    </section>

    <section>
        <h2 class="judul">Katalog dan Cek Stok Barang</h2>

        <form method="GET" class="filter">

            <input
                type="text"
                name="cari"
                placeholder="Cari kode atau nama barang..."
                value="<?= amanPublik($cari) ?>"
            >

            <select name="kategori_id">
                <option value="0">Semua Kategori</option>

                <?php foreach ($dataKategori as $kategori): ?>
                    <option
                        value="<?= (int)$kategori['id'] ?>"
                        <?= $kategori_id == $kategori['id']
                            ? 'selected' : '' ?>
                    >
                        <?= amanPublik($kategori['nama_kategori']) ?>
                    </option>
                <?php endforeach; ?>

            </select>

            <button type="submit">Cari Barang</button>

        </form>

        <div class="tabel">

            <table>
                <caption>Daftar Persediaan Barang</caption>

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th>Stok</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (count($dataBarang) > 0): ?>

                        <?php $no = 1; ?>

                        <?php foreach ($dataBarang as $barang): ?>

                            <?php
                            if ($barang['stok'] <= 0) {
                                $status = 'Habis';
                                $kelas = 'habis';
                            } elseif ($barang['stok'] <= $barang['stok_min']) {
                                $status = 'Stok Kritis';
                                $kelas = 'kritis';
                            } else {
                                $status = 'Tersedia';
                                $kelas = 'tersedia';
                            }
                            ?>

                            <tr>
                                <td><?= $no++ ?></td>

                                <td>
                                    <?= amanPublik($barang['kode_barang']) ?>
                                </td>

                                <td>
                                    <?= amanPublik($barang['nama_barang']) ?>
                                </td>

                                <td>
                                    <?= amanPublik(
                                        $barang['nama_kategori'] ?? '-'
                                    ) ?>
                                </td>

                                <td>
                                    <?= amanPublik($barang['satuan']) ?>
                                </td>

                                <td>
                                    <?= amanPublik($barang['stok']) ?>
                                </td>

                                <td>
                                    <span class="status <?= $kelas ?>">
                                        <?= $status ?>
                                    </span>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="7" class="kosong">
                                Data barang tidak ditemukan.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>
            </table>

        </div>
    </section>

</main>

<footer>
    <p>
        &copy; <?= date('Y') ?> AutoStock Logistic.
        Smart Inventory Management.
    </p>
</footer>

</body>
</html>
