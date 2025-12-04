<?php
session_start();
include 'db.php';

// Pastikan user login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Jika keranjang kosong
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$total = 0;

foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Simpan ke tabel orders
mysqli_query($conn, "INSERT INTO orders (user_id, total, status) VALUES ($user_id, $total, 'pending')");
$order_id = mysqli_insert_id($conn);

// Simpan item pesanan
foreach ($_SESSION['cart'] as $item) {
    $product_id = $item['id'];
    $quantity = $item['quantity'];
    $price = $item['price'];
    mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ($order_id, $product_id, $quantity, $price)");
}

// Kosongkan keranjang
unset($_SESSION['cart']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran Berhasil - Sehat Selalu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">
<?php include 'includes/header.php'; ?>

<section class="container mx-auto py-16 text-center">
    <h2 class="text-3xl font-bold text-green-600 mb-6">Pesanan Berhasil!</h2>
    <p class="text-lg mb-6">Terima kasih telah berbelanja di <strong>Sehat Selalu</strong>.</p>
    <p class="text-gray-600 mb-8">Nomor pesanan Anda: <span class="font-semibold">#<?= $order_id ?></span></p>
    <a href="products.php" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">Kembali ke Produk</a>
</section>

<?php include 'includes/footer.php'; ?>
</body>
</html>
