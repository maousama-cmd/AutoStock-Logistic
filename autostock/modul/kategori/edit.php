
<?php
require_once __DIR__ . '/../../auth/index.php';
checkAuth();

require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? '';

if ($id === '' || !ctype_digit((string)$id)) {
    die('ID kategori tidak valid!');
}

$stmt = $db->prepare("SELECT * FROM kategori WHERE id = :id");
$stmt->execute([':id' => $id]);

$kategori = $stmt->fetch();

if (!$kategori) {
    die('Data kategori tidak ditemukan!');
}

$pesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = trim($_POST['kode_kategori'] ?? '');
    $nama = trim($_POST['nama_kategori'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');

    if ($kode === '' || $nama === '') {
        $pesan = 'Kode dan nama kategori wajib diisi!';
    } else {
        try {
            $sql = "UPDATE kategori
                    SET kode_kategori = :kode,
                        nama_kategori = :nama,
                        keterangan = :keterangan
                    WHERE id = :id";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':kode' => $kode,
                ':nama' => $nama,
                ':keterangan' => $keterangan,
                ':id' => $id
            ]);

            header('Location: index.php');
            exit;

        } catch (PDOException $e) {
            $pesan = 'Gagal mengubah data. Pastikan kode kategori tidak digunakan kategori lain.';
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
    <h2>Edit Kategori Barang</h2>

    <?php if ($pesan !== ''): ?>
        <p class="pesan-error">
            <?= htmlspecialchars($pesan) ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Kode Kategori</label>
            <input type="text" name="kode_kategori"
                value="<?= htmlspecialchars($kategori['kode_kategori']) ?>"
                required>
        </div>

        <div class="form-group">
            <label>Nama Kategori</label>
            <input type="text" name="nama_kategori"
                value="<?= htmlspecialchars($kategori['nama_kategori']) ?>"
                required>
        </div>

        <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan"><?= htmlspecialchars($kategori['keterangan'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn-simpan">
            Simpan Perubahan
        </button>

        <a href="index.php" class="btn-kembali">Kembali</a>
    </form>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
