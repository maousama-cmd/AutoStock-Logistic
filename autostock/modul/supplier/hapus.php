<?php
require_once __DIR__ . '/../../auth/index.php';
checkAuth();
?>
<?php
require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? '';

if ($id == '' || !ctype_digit($id)) {
    die("ID supplier tidak valid!");
}

// Mengecek apakah supplier tersedia
$stmt = $db->prepare(
    "SELECT id FROM supplier WHERE id = ?"
);
$stmt->execute([$id]);

$supplier = $stmt->fetch();

if (!$supplier) {
    die("Data supplier tidak ditemukan!");
}

// Menghapus data supplier
$stmt = $db->prepare(
    "DELETE FROM supplier WHERE id = ?"
);
$stmt->execute([$id]);

header("Location: index.php");
exit;
?>