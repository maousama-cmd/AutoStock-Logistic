<?php
require_once __DIR__ . '/../../auth/index.php';
checkAuth();

require_once __DIR__ . '/../../config/database.php';

// Mengambil ID barang dari URL
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0) {

    try {
        // Menghapus data barang
        $stmt = $db->prepare("DELETE FROM barang WHERE id = ?");
        $stmt->execute([$id]);

        $_SESSION['pesan'] = "Data barang berhasil dihapus.";

    } catch (PDOException $e) {

        // Jika barang masih memiliki riwayat transaksi
        if ($e->getCode() == '23000') {
            $_SESSION['pesan'] = "Barang tidak dapat dihapus karena masih memiliki riwayat transaksi.";
        } else {
            $_SESSION['pesan'] = "Terjadi kesalahan saat menghapus barang.";
        }
    }
}

header("Location: index.php");
exit;
?>