
<?php
require_once __DIR__ . '/../../auth/index.php';
checkAuth();

require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? '';

if ($id == '' || !ctype_digit((string)$id)) {
    die("ID supplier tidak valid!");
}

$stmt = $db->prepare("SELECT * FROM supplier WHERE id = ?");
$stmt->execute([$id]);
$supplier = $stmt->fetch();

if (!$supplier) {
    die("Data supplier tidak ditemukan!");
}

$pesan = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode = trim($_POST['kode_supplier'] ?? '');
    $nama = trim($_POST['nama_supplier'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');

    if ($kode == '' || $nama == '' || $telepon == '') {
        $pesan = "Kode, nama, dan telepon wajib diisi!";
    } elseif (!preg_match('/^[0-9+\-\s]{8,20}$/', $telepon)) {
        $pesan = "Format nomor telepon tidak valid!";
    } else {
        try {
            $stmt = $db->prepare(
                "UPDATE supplier
                 SET kode_supplier = ?,
                     nama_supplier = ?,
                     telepon = ?,
                     alamat = ?
                 WHERE id = ?"
            );

            $stmt->execute([
                $kode,
                $nama,
                $telepon,
                $alamat,
                $id
            ]);

            header("Location: index.php");
            exit;

        } catch (PDOException $e) {
            $pesan = "Gagal memperbarui data. Pastikan kode supplier belum digunakan.";
        }
    }
}

require_once __DIR__ . '/../../layout/header.php';
?>

<style>
.form-container {
    max-width: 600px;
    margin: 30px auto;
    padding: 30px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.form-container h2 {
    margin-bottom: 25px;
    color: #1e293b;
}

.form-group {
    margin-bottom: 18px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #cbd5e1;
    border-radius: 7px;
    box-sizing: border-box;
    font-size: 14px;
}

.form-group textarea {
    min-height: 100px;
    resize: vertical;
}

.btn-simpan {
    background: #2563eb;
    color: white;
    padding: 11px 20px;
    border: none;
    border-radius: 7px;
    cursor: pointer;
}

.btn-kembali {
    display: inline-block;
    background: #64748b;
    color: white;
    padding: 11px 20px;
    border-radius: 7px;
    text-decoration: none;
    margin-left: 5px;
}

.pesan-error {
    color: #dc2626;
    margin-bottom: 15px;
}
</style>

<div class="form-container">
    <h2>Edit Data Supplier</h2>

    <?php if ($pesan != ''): ?>
        <p class="pesan-error">
            <?= htmlspecialchars($pesan) ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Kode Supplier</label>
            <input type="text" name="kode_supplier"
                value="<?= htmlspecialchars($supplier['kode_supplier']) ?>"
                required>
        </div>

        <div class="form-group">
            <label>Nama Supplier</label>
            <input type="text" name="nama_supplier"
                value="<?= htmlspecialchars($supplier['nama_supplier']) ?>"
                required>
        </div>

        <div class="form-group">
            <label>Nomor Telepon</label>
            <input type="text" name="telepon"
                value="<?= htmlspecialchars($supplier['telepon']) ?>"
                required>
        </div>

        <div class="form-group">
            <label>Alamat</label>
            <textarea name="alamat"><?= htmlspecialchars($supplier['alamat'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn-simpan">
            Simpan Perubahan
        </button>

        <a href="index.php" class="btn-kembali">Kembali</a>
    </form>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
