
<?php
require_once __DIR__ . '/../../auth/index.php';
checkAuth();

require_once __DIR__ . '/../../config/database.php';

$pesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $kode = trim($_POST['kode_kategori'] ?? '');
    $nama = trim($_POST['nama_kategori'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');

    if ($kode === '' || $nama === '') {
        $pesan = 'Kode dan nama kategori wajib diisi!';
    } else {
        try {
            $sql = "INSERT INTO kategori
                    (kode_kategori, nama_kategori, keterangan)
                    VALUES (:kode, :nama, :keterangan)";

            $stmt = $db->prepare($sql);

            $stmt->execute([
                ':kode' => $kode,
                ':nama' => $nama,
                ':keterangan' => $keterangan
            ]);

            header('Location: index.php');
            exit;

        } catch (PDOException $e) {
            $pesan = 'Gagal menyimpan kategori. Pastikan kode kategori belum digunakan.';
        }
    }
}

require_once __DIR__ . '/../../layout/header.php';
?>

<style>
.form-kategori {
    max-width: 600px;
    margin: 20px auto;
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
}

.form-kategori h2 {
    color: #1e40af;
    margin-top: 0;
    margin-bottom: 25px;
}

.form-group {
    margin-bottom: 18px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 15px;
    font-family: Arial, sans-serif;
}

.form-group textarea {
    min-height: 100px;
    resize: vertical;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #2563eb;
}

.btn-simpan {
    background: #2563eb;
    color: white;
    border: none;
    padding: 11px 20px;
    border-radius: 6px;
    cursor: pointer;
}

.btn-kembali {
    display: inline-block;
    background: #64748b;
    color: white;
    padding: 11px 20px;
    border-radius: 6px;
    text-decoration: none;
    margin-left: 5px;
}

.pesan-error {
    background: #fee2e2;
    color: #b91c1c;
    padding: 12px;
    border-radius: 6px;
    margin-bottom: 15px;
}
</style>

<div class="form-kategori">

    <h2>Tambah Kategori Barang</h2>

    <?php if ($pesan !== ''): ?>
        <div class="pesan-error">
            <?= htmlspecialchars($pesan, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <div class="form-group">
            <label>Kode Kategori</label>
            <input type="text"
                   name="kode_kategori"
                   placeholder="Contoh: KAT004"
                   required>
        </div>

        <div class="form-group">
            <label>Nama Kategori</label>
            <input type="text"
                   name="nama_kategori"
                   placeholder="Masukkan nama kategori"
                   required>
        </div>

        <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan"
                      placeholder="Masukkan keterangan"></textarea>
        </div>

        <button type="submit" class="btn-simpan">
            Simpan Kategori
        </button>

        <a href="index.php" class="btn-kembali">
            Kembali
        </a>

    </form>

</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
