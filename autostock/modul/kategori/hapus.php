<?php
require_once __DIR__ . '/../../auth/index.php';
checkAuth();
?>
<?php
// Modul Hapus Kategori Barang
require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? '';

if ($id === '' || !ctype_digit((string)$id)) {
    die('ID kategori tidak valid!');
}

// Periksa apakah kategori masih digunakan oleh barang
$stmt = $db->prepare(
    "SELECT COUNT(*) FROM barang WHERE kategori_id = :id"
);
$stmt->execute([':id' => $id]);

$jumlahBarang = $stmt->fetchColumn();

if ($jumlahBarang > 0) {
    die(
        'Kategori tidak bisa dihapus karena masih digunakan oleh ' .
        $jumlahBarang . ' barang. Ubah kategori barang tersebut terlebih dahulu. ' .
        '<a href="index.php">Kembali</a>'
    );
}

// Hapus kategori jika tidak digunakan
$stmt = $db->prepare(
    "DELETE FROM kategori WHERE id = :id"
);
$stmt->execute([':id' => $id]);

header('Location: index.php');
exit;
?>
