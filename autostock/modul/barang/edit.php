<?php
require_once __DIR__ . '/../../auth/index.php';
checkAuth();
?>
<?php
require_once __DIR__ . '/../../config/database.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Mengambil data barang berdasarkan ID
$stmt = $db->prepare("SELECT * FROM barang WHERE id = ?");
$stmt->execute([$id]);
$barang = $stmt->fetch();

if (!$barang) {
    die("Data barang tidak ditemukan!");
}

// Mengambil kategori
$queryKategori = $db->query("SELECT * FROM kategori ORDER BY nama_kategori ASC");
$kategori = $queryKategori->fetchAll();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $kode = trim($_POST['kode_barang']);
    $nama = trim($_POST['nama_barang']);
    $kategori_id = (int) $_POST['kategori_id'];
    $satuan = trim($_POST['satuan']);
    $harga_beli = $_POST['harga_beli'];
    $harga_jual = $_POST['harga_jual'];
    $stok = $_POST['stok'];
    $stok_min = $_POST['stok_min'];

    if (
        $kode === '' || $nama === '' || $kategori_id <= 0 ||
        $satuan === '' ||
        !is_numeric($harga_beli) ||
        !is_numeric($harga_jual) ||
        !is_numeric($stok) ||
        !is_numeric($stok_min) ||
        $harga_beli < 0 || $harga_jual < 0 ||
        $stok < 0 || $stok_min < 0
    ) {
        $error = "Semua data harus diisi dengan benar!";
    } else {
        try {
            $sql = "UPDATE barang SET
                    kode_barang = ?,
                    nama_barang = ?,
                    kategori_id = ?,
                    satuan = ?,
                    harga_beli = ?,
                    harga_jual = ?,
                    stok = ?,
                    stok_min = ?
                    WHERE id = ?";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                $kode,
                $nama,
                $kategori_id,
                $satuan,
                $harga_beli,
                $harga_jual,
                $stok,
                $stok_min,
                $id
            ]);

            header("Location: index.php");
            exit;

        } catch (PDOException $e) {
            $error = "Gagal mengubah data. Pastikan kode barang tidak digunakan barang lain.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            padding: 30px;
        }

        .container {
            max-width: 650px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
        }

        h2 {
            color: #1e40af;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 7px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button, a {
            display: inline-block;
            margin-top: 20px;
            padding: 11px 18px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }

        button {
            background: #2563eb;
            color: white;
        }

        a {
            background: #64748b;
            color: white;
        }

        .error {
            background: #fee2e2;
            color: #b91c1c;
            padding: 10px;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Edit Data Barang</h2>

    <?php if ($error !== ''): ?>
        <p class="error">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <form method="POST">

        <label>Kode Barang</label>
        <input type="text" name="kode_barang"
            value="<?= htmlspecialchars($barang['kode_barang']) ?>" required>

        <label>Nama Barang</label>
        <input type="text" name="nama_barang"
            value="<?= htmlspecialchars($barang['nama_barang']) ?>" required>

        <label>Kategori</label>
        <select name="kategori_id" required>
            <?php foreach ($kategori as $k): ?>
                <option value="<?= $k['id'] ?>"
                    <?= $barang['kategori_id'] == $k['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($k['nama_kategori']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Satuan</label>
        <input type="text" name="satuan"
            value="<?= htmlspecialchars($barang['satuan']) ?>" required>

        <label>Harga Beli</label>
        <input type="number" name="harga_beli" min="0" step="0.01"
            value="<?= htmlspecialchars($barang['harga_beli']) ?>" required>

        <label>Harga Jual</label>
        <input type="number" name="harga_jual" min="0" step="0.01"
            value="<?= htmlspecialchars($barang['harga_jual']) ?>" required>

        <label>Stok</label>
        <input type="number" name="stok" min="0"
            value="<?= htmlspecialchars($barang['stok']) ?>" required>

        <label>Stok Minimum</label>
        <input type="number" name="stok_min" min="0"
            value="<?= htmlspecialchars($barang['stok_min']) ?>" required>

        <button type="submit">Simpan Perubahan</button>
        <a href="index.php">Kembali</a>

    </form>

</div>

</body>
</html>
