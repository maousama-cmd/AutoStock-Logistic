
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoStock Logistic</title>

    <style>
        * {
            box-sizing: border-box;
        }

       body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            margin: 0;
            color: #1e293b;

            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background: #2563eb;
            color: white;
            padding: 20px 30px;
        }

        header h1 {
            margin: 0;
        }

        header p {
            margin-bottom: 0;
        }

        nav {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.08);
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        nav a {
            color: #2563eb;
            text-decoration: none;
            font-weight: bold;
            padding: 5px 0;
        }

        nav a:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }

        .container {
            flex: 0 0 auto;
            width: 100%;
            max-width: 1100px;
            margin: 15px auto 0;
            padding: 0 20px;
        }
       
        .menu {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        .card h3 {
            color: #1e40af;
        }

        .angka {
            font-size: 30px;
            font-weight: bold;
        }

        .menu-link {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
        }

        .menu-link:hover {
            background: #1d4ed8;
        }
        .footer {
            background: #1e293b;
            color: white;
            text-align: center;
            padding: 12px;
            margin-top: 0;
            width: 100%;
        }
        .footer p {
            margin: 5px 0;
        }

        @media (max-width: 600px) {
            header {
                padding: 20px;
            }

            header h1 {
                font-size: 24px;
            }

            nav {
                padding: 15px 20px;
                gap: 12px;
            }

            nav a {
                font-size: 14px;
            }
        }
            /* Tampilan tabel responsif */
            .table-wrapper {
                width: 100%;
                max-width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
        }

            /* Mencegah halaman melebar */
            html, body {
                width: 100%;
                max-width: 100%;
                overflow-x: hidden;
        }

            /* Tampilan HP */
        @media (max-width: 600px) {

            header {
                padding: 15px;
            }

            header h1 {
                font-size: 22px;
            }

            header p {
                font-size: 13px;
            }

            nav {
                padding: 12px 15px;
                gap: 12px;
            }

            nav a {
                font-size: 13px;
            }

            .card {
                padding: 15px;
            }

            .angka {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>

<header>
    <h1>AutoStock Logistic</h1>
    <p>Smart Inventory Management</p>
</header>

<nav>
    <a href="/autostock/dashboard.php">Dashboard</a>
    <a href="/autostock/modul/barang/index.php">Barang</a>
    <a href="/autostock/modul/kategori/index.php">Kategori</a>
    <a href="/autostock/modul/supplier/index.php">Supplier</a>
    <a href="/autostock/modul/transaksi/index.php">Transaksi</a>
    <a href="/autostock/auth/logout.php">Logout</a>
</nav>

<div class="container">
