<?php
require_once __DIR__ . '/../../auth/index.php';
checkAuth();
?>
<?php
require_once __DIR__ . '/../../config/database.php';

$pesan = '';
$error = '';

// Mengambil data kategori
$queryKategori = $db->query("SELECT * FROM kategori ORDER BY nama_kategori ASC");
$kategori = $queryKategori->fetchAll(PDO::FETCH_ASSOC);

// Proses simpan barang
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $kode_barang = trim($_POST['kode_barang']);
    $nama_barang = trim($_POST['nama_barang']);
    $kategori_id = (int) $_POST['kategori_id'];
    $satuan = trim($_POST['satuan']);
    $harga_beli = $_POST['harga_beli'];
    $harga_jual = $_POST['harga_jual'];
    $stok = $_POST['stok'];
    $stok_min = $_POST['stok_min'];

    if (
        $kode_barang === '' ||
        $nama_barang === '' ||
        $kategori_id <= 0 ||
        $satuan === '' ||
        !is_numeric($harga_beli) ||
        !is_numeric($harga_jual) ||
        !is_numeric($stok) ||
        !is_numeric($stok_min) ||
        $harga_beli < 0 ||
        $harga_jual < 0 ||
        $stok < 0 ||
        $stok_min < 0
    ) {
        $error = "Semua data harus diisi dengan benar!";
    } else {
        try {
            $sql = "INSERT INTO barang
                    (kode_barang, kategori_id, nama_barang, satuan,
                    harga_beli, harga_jual, stok, stok_min)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $db->prepare($sql);

            $stmt->execute([
                $kode_barang,
                $kategori_id,
                $nama_barang,
                $satuan,
                $harga_beli,
                $harga_jual,
                $stok,
                $stok_min
            ]);

            header("Location: index.php");
            exit;

        } catch (PDOException $e) {
            $error = "Gagal menyimpan barang. Pastikan kode barang belum digunakan.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang - AutoStock SCM</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 650px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        h2 {
            color: #1e40af;
            margin-top: 0;
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

        button, .kembali {
            display: inline-block;
            margin-top: 20px;
            padding: 11px 18px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }

        button {
            background: #2563eb;
            color: white;
        }

        .kembali {
            background: #64748b;
            color: white;
        }

        .error {
            background: #fee2e2;
            color: #b91c1c;
            padding: 10px;
            border-radius: 5px;
        }

        .info {
            background: #fef3c7;
            color: #92400e;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Tambah Data Barang</h2>
    <p>Silakan isi informasi barang berikut.</p>

    <?php if ($error !== ''): ?>
        <p class="error">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <?php if (count($kategori) === 0): ?>
        <p class="info">
            Data kategori belum tersedia. Tambahkan kategori terlebih dahulu.
        </p>
    <?php else: ?>

    <form method="POST">

        <label>Kode Barang</label>
        <input type="text" name="kode_barang"
               placeholder="Contoh: BRG001" required>

        <label>Nama Barang</label>
        <input type="text" name="nama_barang"
               placeholder="Nama barang" required>

        <label>Kategori</label>
        <select name="kategori_id" required>
            <option value="">-- Pilih Kategori --</option>

            <?php foreach ($kategori as $k): ?>
                <option value="<?= $k['id'] ?>">
                    <?= htmlspecialchars($k['nama_kategori']) ?>
                </option>
            <?php endforeach; ?>

        </select>

        <label>Satuan</label>
        <input type="text" name="satuan"
               placeholder="Contoh: Unit, Pcs, Box" required>

        <label>Harga Beli</label>
        <input type="number" name="harga_beli"
               min="0" step="0.01" required>

        <label>Harga Jual</label>
        <input type="number" name="harga_jual"
               min="0" step="0.01" required>

        <label>Stok</label>
        <input type="number" name="stok"
               min="0" required>

        <label>Stok Minimum</label>
        <input type="number" name="stok_min"
               min="0" required>

        <button type="submit">Simpan Barang</button>

        <a href="index.php" class="kembali">Kembali</a>

    </form>

    <?php endif; ?>

</div>

</body>
</html>
