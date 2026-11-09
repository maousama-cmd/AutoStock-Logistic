<?php
require_once __DIR__ . '/../../auth/index.php';
checkAuth();
?>
<?php
require_once __DIR__ . '/../../config/database.php';

$pesan = "";

// Mengambil daftar barang
$stmtBarang = $db->query(
    "SELECT id, kode_barang, nama_barang, stok
     FROM barang ORDER BY nama_barang ASC"
);
$dataBarang = $stmtBarang->fetchAll();

// Mengambil daftar supplier
$stmtSupplier = $db->query(
    "SELECT id, nama_supplier
     FROM supplier ORDER BY nama_supplier ASC"
);
$dataSupplier = $stmtSupplier->fetchAll();

// Memproses form transaksi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $barang_id = (int)($_POST['barang_id'] ?? 0);
    $supplier_id = !empty($_POST['supplier_id'])
        ? (int)$_POST['supplier_id'] : null;
    $jenis = $_POST['jenis_transaksi'] ?? '';
    $jumlah = filter_var($_POST['jumlah'] ?? '', FILTER_VALIDATE_INT);
    $keterangan = trim($_POST['keterangan'] ?? '');
    $petugas = trim($_POST['petugas'] ?? '');

    if (
        $barang_id <= 0 ||
        !in_array($jenis, ['Masuk', 'Keluar'], true) ||
        $jumlah === false || $jumlah <= 0 ||
        $petugas === ''
    ) {
        $pesan = "Mohon isi data transaksi dengan benar.";
    } else {
        try {
            $db->beginTransaction();

            // Mengunci data barang agar stok aman saat diperbarui
            $stmt = $db->prepare(
                "SELECT stok FROM barang WHERE id = ? FOR UPDATE"
            );
            $stmt->execute([$barang_id]);
            $barang = $stmt->fetch();

            if (!$barang) {
                throw new Exception("Barang tidak ditemukan.");
            }

            $stokSekarang = (int)$barang['stok'];

            if ($jenis === 'Keluar' && $jumlah > $stokSekarang) {
                throw new Exception("Stok tidak mencukupi.");
            }

            // Membuat nomor transaksi otomatis
            $noTransaksi = 'TRX-' . date('YmdHis') . '-' . random_int(100, 999);

            // Menyimpan transaksi
            $stmt = $db->prepare(
                "INSERT INTO transaksi_stok
                (no_transaksi, barang_id, supplier_id,
                 jenis_transaksi, jumlah, keterangan, petugas)
                VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->execute([
                $noTransaksi,
                $barang_id,
                $supplier_id,
                $jenis,
                $jumlah,
                $keterangan,
                $petugas
            ]);

            // Memperbarui stok barang
            if ($jenis === 'Masuk') {
                $sqlStok = "UPDATE barang SET stok = stok + ? WHERE id = ?";
            } else {
                $sqlStok = "UPDATE barang SET stok = stok - ? WHERE id = ?";
            }

            $stmtStok = $db->prepare($sqlStok);
            $stmtStok->execute([$jumlah, $barang_id]);

            $db->commit();

            header("Location: index.php");
            exit;

        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            $pesan = "Transaksi gagal: " . $e->getMessage();
        }
    }
}

function amanTambah($data) {
    return htmlspecialchars((string)$data, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Transaksi Stok</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 30px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px #ccc;
        }

        h2 {
            color: #2563eb;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            box-sizing: border-box;
        }

        button {
            margin-top: 20px;
            padding: 11px 18px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .pesan {
            color: red;
            margin-top: 15px;
        }

        .kembali {
            display: inline-block;
            margin-top: 20px;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Tambah Transaksi Stok</h2>
    <p>Catat barang masuk atau barang keluar gudang.</p>

    <?php if ($pesan !== ''): ?>
        <p class="pesan"><?= amanTambah($pesan) ?></p>
    <?php endif; ?>

    <form method="POST">

        <label>Barang</label>
        <select name="barang_id" required>
            <option value="">-- Pilih Barang --</option>

            <?php foreach ($dataBarang as $barang): ?>
                <option value="<?= (int)$barang['id'] ?>">
                    <?= amanTambah($barang['kode_barang']) ?>
                    - <?= amanTambah($barang['nama_barang']) ?>
                    (Stok: <?= (int)$barang['stok'] ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <label>Supplier</label>
        <select name="supplier_id">
            <option value="">-- Pilih Supplier (Opsional) --</option>

            <?php foreach ($dataSupplier as $supplier): ?>
                <option value="<?= (int)$supplier['id'] ?>">
                    <?= amanTambah($supplier['nama_supplier']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Jenis Transaksi</label>
        <select name="jenis_transaksi" required>
            <option value="">-- Pilih Jenis --</option>
            <option value="Masuk">Barang Masuk</option>
            <option value="Keluar">Barang Keluar</option>
        </select>

        <label>Jumlah</label>
        <input type="number" name="jumlah" min="1" required>

        <label>Keterangan</label>
        <textarea name="keterangan" rows="3"></textarea>

        <label>Nama Petugas</label>
        <input type="text" name="petugas" required>

        <button type="submit">Simpan Transaksi</button>

    </form>

    <a href="index.php" class="kembali">Kembali ke Histori Transaksi</a>

</div>

</body>
</html>