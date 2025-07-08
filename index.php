<?php
session_start();

// Inisialisasi session jika belum ada
if (!isset($_SESSION['shopping_list'])) {
    $_SESSION['shopping_list'] = [];
}

if (!isset($_SESSION['checkout_history'])) {
    $_SESSION['checkout_history'] = [];
}

// Daftar item tersedia
$available_items = ["Beras", "Gula", "Minyak", "Sayuran", "Buah", "Daging", "Ikan", "Kopi"];

// Tambah item dari AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $item = htmlspecialchars($_POST['item']);
    if (!empty($item) && !in_array($item, $_SESSION['shopping_list'])) {
        $_SESSION['shopping_list'][] = $item;
    }
    echo json_encode($_SESSION['shopping_list']);
    exit;
}

// Hapus item berdasarkan index
if (isset($_GET['remove'])) {
    $index = intval($_GET['remove']);
    if (isset($_SESSION['shopping_list'][$index])) {
        unset($_SESSION['shopping_list'][$index]);
        $_SESSION['shopping_list'] = array_values($_SESSION['shopping_list']); // Reindex
    }
    header("Location: index.php");
    exit;
}

// Proses checkout
if (isset($_GET['checkout'])) {
    $items = $_SESSION['shopping_list'];
    $receipt_number = "RCPT-" . strtoupper(substr(md5(uniqid()), 0, 8));
    $timestamp = date("Y-m-d H:i:s");

    // Simpan ke riwayat checkout
    $_SESSION['checkout_history'][] = [
        'receipt' => $receipt_number,
        'items' => $items,
        'time' => $timestamp
    ];

    // Kosongkan keranjang
    $_SESSION['shopping_list'] = [];

    // Redirect agar tidak double submit
    header("Location: index.php?show_receipt=$receipt_number");
    exit;
}

// Tampilkan resi
$receipt_data = null;
if (isset($_GET['show_receipt'])) {
    foreach ($_SESSION['checkout_history'] as $record) {
        if ($record['receipt'] == $_GET['show_receipt']) {
            $receipt_data = $record;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Belanja Interaktif</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>🛒 Daftar Belanja</h1>

<!-- Formulir untuk menambah item -->
<form id="addItemForm">
    <input type="text" id="newItem" placeholder="Masukkan item..." required>
    <button type="submit">Tambah Manual</button>
</form>

<!-- Daftar belanja -->
<h2>Daftar Belanja</h2>
<ul id="shoppingList">
    <?php foreach ($_SESSION['shopping_list'] as $i => $item): ?>
        <li data-index="<?= $i ?>"><?= $item ?> <button class="delete">Hapus</button></li>
    <?php endforeach; ?>
</ul>

<!-- Tombol Checkout -->
<?php if (!empty($_SESSION['shopping_list'])): ?>
    <a href="index.php?checkout=true"><button type="button">Checkout</button></a>
<?php endif; ?>

<!-- Daftar item tersedia -->
<h2>📦 Pilih Item Tersedia</h2>
<div id="availableItems">
    <?php foreach ($available_items as $item): ?>
        <div class="item-box">
            <?= $item ?>
            <button class="addToCart" data-item="<?= $item ?>">Pilih</button>
        </div>
    <?php endforeach; ?>
</div>

<!-- Tampilan Resi -->
<?php if ($receipt_data): ?>
    <h2>📄 Resi Pembelian</h2>
    <p><strong>Nomor Resi:</strong> <?= $receipt_data['receipt'] ?></p>
    <p><strong>Waktu:</strong> <?= $receipt_data['time'] ?></p>
    <ul>
        <strong>Barang Dibeli:</strong>
        <?php foreach ($receipt_data['items'] as $item): ?>
            <li><?= $item ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<script src="script.js"></script>
</body>
</html>