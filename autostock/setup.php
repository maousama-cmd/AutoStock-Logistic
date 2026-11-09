
<?php
// =========================================
// SETUP AUTOSTOCK K4
// Memeriksa koneksi dan tabel database
// =========================================

require_once __DIR__ . '/config/database.php';

$namaDatabase = 'db_inventaris_gudang';

$tabelWajib = [
    'barang',
    'kategori',
    'supplier',
    'transaksi_stok'
];

$berhasil = [];
$belumAda = [];

try {
    // Memeriksa nama database
    $databaseAktif = $db->query("SELECT DATABASE()")->fetchColumn();

    if ($databaseAktif !== $namaDatabase) {
        throw new Exception("Database yang digunakan tidak sesuai.");
    }

    // Memeriksa tabel satu per satu
    foreach ($tabelWajib as $tabel) {
        $stmt = $db->prepare("
            SELECT COUNT(*)
            FROM information_schema.tables
            WHERE table_schema = ?
            AND table_name = ?
        ");

        $stmt->execute([$namaDatabase, $tabel]);

        if ($stmt->fetchColumn() > 0) {
            $berhasil[] = $tabel;
        } else {
            $belumAda[] = $tabel;
        }
    }

} catch (Exception $e) {
    $pesanError = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup AutoStock K4</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            padding: 30px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
        }

        h2 {
            color: #2563eb;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>

<body>
<div class="container">

    <h2>Setup AutoStock SCM</h2>

    <?php if (isset($pesanError)): ?>

        <p class="error">
            <?= htmlspecialchars($pesanError) ?>
        </p>

    <?php else: ?>

        <p class="success">
            Koneksi database berhasil!
        </p>

        <h3>Tabel yang tersedia:</h3>

        <?php foreach ($berhasil as $tabel): ?>
            <p class="success">
                ✓ <?= htmlspecialchars($tabel) ?>
            </p>
        <?php endforeach; ?>

        <?php if (!empty($belumAda)): ?>

            <h3 class="error">Tabel yang belum tersedia:</h3>

            <?php foreach ($belumAda as $tabel): ?>
                <p class="error">
                    ✗ <?= htmlspecialchars($tabel) ?>
                </p>
            <?php endforeach; ?>

        <?php else: ?>

            <h3 class="success">
                Semua tabel sudah tersedia!
            </h3>

        <?php endif; ?>

    <?php endif; ?>

    <a href="index.php">Kembali ke Halaman Utama</a>

</div>
</body>
</html>
