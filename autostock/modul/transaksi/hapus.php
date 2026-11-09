<?php
require_once __DIR__ . '/../../auth/index.php';
checkAuth();
?>
<?php
require_once __DIR__ . '/../../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("ID transaksi tidak valid.");
}

try {
    $db->beginTransaction();

    // Mengambil dan mengunci data transaksi
    $stmt = $db->prepare(
        "SELECT * FROM transaksi_stok WHERE id = ? FOR UPDATE"
    );
    $stmt->execute([$id]);

    $transaksi = $stmt->fetch();

    if (!$transaksi) {
        throw new Exception("Transaksi tidak ditemukan.");
    }

    // Mengunci data barang
    $stmtBarang = $db->prepare(
        "SELECT stok FROM barang WHERE id = ? FOR UPDATE"
    );
    $stmtBarang->execute([$transaksi['barang_id']]);

    $barang = $stmtBarang->fetch();

    if (!$barang) {
        throw new Exception("Barang tidak ditemukan.");
    }

    $jumlah = (int)$transaksi['jumlah'];
    $stok = (int)$barang['stok'];

    // Mengembalikan perubahan stok
    if ($transaksi['jenis_transaksi'] === 'Masuk') {

        if ($stok < $jumlah) {
            throw new Exception(
                "Transaksi tidak bisa dibatalkan karena stok saat ini tidak mencukupi."
            );
        }

        $sql = "UPDATE barang SET stok = stok - ? WHERE id = ?";

    } else {

        $sql = "UPDATE barang SET stok = stok + ? WHERE id = ?";
    }

    $stmtUpdate = $db->prepare($sql);
    $stmtUpdate->execute([$jumlah, $transaksi['barang_id']]);

    // Menghapus transaksi
    $stmtHapus = $db->prepare(
        "DELETE FROM transaksi_stok WHERE id = ?"
    );
    $stmtHapus->execute([$id]);

    $db->commit();

    header("Location: index.php");
    exit;

} catch (Throwable $e) {

    if ($db->inTransaction()) {
        $db->rollBack();
    }

    die("Pembatalan gagal: " . htmlspecialchars(
        $e->getMessage(),
        ENT_QUOTES,
        'UTF-8'
    ));
}
?>