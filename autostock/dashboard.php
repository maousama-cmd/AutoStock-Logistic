
<?php
require_once __DIR__ . '/auth/index.php';
checkAuth();

require_once __DIR__ . '/config/database.php';

// Menghitung jumlah barang
$totalBarang = $db->query(
    "SELECT COUNT(*) FROM barang"
)->fetchColumn();

// Menghitung jumlah kategori
$totalKategori = $db->query(
    "SELECT COUNT(*) FROM kategori"
)->fetchColumn();

// Menghitung barang dengan stok kritis
$stokKritis = $db->query(
    "SELECT COUNT(*) FROM barang
     WHERE stok <= stok_min"
)->fetchColumn();

require_once __DIR__ . '/layout/header.php';
?>

<h2>Dashboard Utama</h2>

<div class="menu">

    <div class="card">
        <h3>Total Barang</h3>
        <div class="angka"><?= $totalBarang ?></div>
        <p>Jumlah barang terdaftar</p>
        <a href="modul/barang/index.php" class="menu-link">
            Kelola Barang
        </a>
    </div>

    <div class="card">
        <h3>Total Kategori</h3>
        <div class="angka"><?= $totalKategori ?></div>
        <p>Jumlah kategori barang</p>
        <a href="modul/kategori/index.php" class="menu-link">
            Kelola Kategori
        </a>
    </div>

    <div class="card">
        <h3>Stok Kritis</h3>
        <div class="angka"><?= $stokKritis ?></div>
        <p>Barang dengan stok di bawah batas minimum</p>
        <a href="modul/barang/index.php" class="menu-link">
            Lihat Data Barang
        </a>
    </div>

    <div class="card">
        <h3>Supplier</h3>
        <p>Pengelolaan data pemasok barang.</p>
        <a href="modul/supplier/index.php" class="menu-link">
            Kelola Supplier
        </a>
    </div>

    <div class="card">
        <h3>Mutasi Stok</h3>
        <p>Pencatatan barang masuk dan keluar.</p>
        <a href="modul/transaksi/index.php" class="menu-link">
            Kelola Mutasi
        </a>
    </div>

</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
