
<?php
require_once __DIR__ . '/../../auth/index.php';
checkAuth();

require_once __DIR__ . '/../../config/database.php';

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
                "INSERT INTO supplier
                (kode_supplier, nama_supplier, telepon, alamat)
                VALUES (?, ?, ?, ?)"
            );

            $stmt->execute([$kode, $nama, $telepon, $alamat]);

            header("Location: index.php");
            exit;

        } catch (PDOException $e) {
            $pesan = "Gagal menambahkan supplier. Pastikan kode supplier belum digunakan.";
        }
    }
}

require_once __DIR__ . '/../../layout/header.php';
?>

<style>
.form-supplier {
    max-width: 600px;
    margin: 20px auto;
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
}

.form-supplier h2 {
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

<div class="form-supplier">

    <h2>Tambah Data Supplier</h2>

    <?php if ($pesan != ''): ?>
        <div class="pesan-error">
            <?= htmlspecialchars($pesan, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <div class="form-group">
            <label>Kode Supplier</label>
            <input type="text"
                   name="kode_supplier"
                   placeholder="Contoh: SUP001"
                   required>
        </div>

        <div class="form-group">
            <label>Nama Supplier</label>
            <input type="text"
                   name="nama_supplier"
                   placeholder="Masukkan nama supplier"
                   required>
        </div>

        <div class="form-group">
            <label>Nomor Telepon</label>
            <input type="text"
                   name="telepon"
                   placeholder="Masukkan nomor telepon"
                   required>
        </div>

        <div class="form-group">
            <label>Alamat</label>
            <textarea name="alamat"
                      placeholder="Masukkan alamat supplier"></textarea>
        </div>

        <button type="submit" class="btn-simpan">
            Simpan Supplier
        </button>

        <a href="index.php" class="btn-kembali">
            Kembali
        </a>

    </form>

</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
