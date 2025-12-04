<?php
include 'db.php';
session_start();

// Ambil data produk berdasarkan ID
if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$product_id = intval($_GET['id']);
$query = "SELECT * FROM products WHERE id = $product_id";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    die("<p class='text-center text-red-500 mt-10'>Produk tidak ditemukan.</p>");
}

// Jika form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $telepon = mysqli_real_escape_string($conn, $_POST['telepon']);
    $pembayaran = mysqli_real_escape_string($conn, $_POST['pembayaran']);
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $quantity = 1;
    $total = $product['price'] * $quantity;

    // Simpan order ke tabel orders (sementara user_id=1 sebagai contoh)
    $order_query = "INSERT INTO orders (user_id, total, status) VALUES (1, $total, 'pending')";
    mysqli_query($conn, $order_query);
    $order_id = mysqli_insert_id($conn);

    // Simpan ke tabel order_items
    $item_query = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                   VALUES ($order_id, $product_id, $quantity, {$product['price']})";
    mysqli_query($conn, $item_query);

    // Kurangi stok produk
    mysqli_query($conn, "UPDATE products SET stock = stock - $quantity WHERE id = $product_id");

    // Generate invoice PDF
    require_once __DIR__ . '/generate_invoice.php';
    generate_invoice_pdf($order_id, $nama, $alamat, $telepon, $pembayaran, $product);

    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Beli Produk - <?= htmlspecialchars($product['name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">

<?php include 'includes/header.php'; ?>

<section class="container mx-auto py-12 px-4">
    <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-lg p-8">
        <h2 class="text-3xl font-bold text-center text-blue-600 mb-6">Form Pembayaran</h2>

        <form method="POST" class="space-y-5">
            <!-- Nama Produk otomatis -->
            <div>
                <label class="block mb-2 font-semibold text-gray-700">Nama Produk</label>
                <input type="text" name="product_name" value="<?= htmlspecialchars($product['name']) ?>" readonly
                       class="w-full border border-gray-300 rounded-lg p-2 bg-gray-100 cursor-not-allowed">
            </div>

            <!-- Harga Produk (otomatis juga) -->
            <div>
                <label class="block mb-2 font-semibold text-gray-700">Harga Produk</label>
                <input type="text" value="Rp <?= number_format($product['price'], 0, ',', '.') ?>" readonly
                       class="w-full border border-gray-300 rounded-lg p-2 bg-gray-100 cursor-not-allowed">
            </div>

            <div>
                <label class="block mb-2 font-semibold text-gray-700">Nama Lengkap</label>
                <input type="text" name="nama" required
                       class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label class="block mb-2 font-semibold text-gray-700">Alamat Lengkap</label>
                <textarea name="alamat" required rows="3"
                          class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-400"></textarea>
            </div>

            <div>
                <label class="block mb-2 font-semibold text-gray-700">Nomor Telepon</label>
                <input type="text" name="telepon" required
                       class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label class="block mb-2 font-semibold text-gray-700">Metode Pembayaran</label>
                <select name="pembayaran" required
                        class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-400">
                    <option value="">-- Pilih Metode Pembayaran --</option>
                    <option value="Transfer Bank">Transfer Bank</option>
                    <option value="COD (Bayar di Tempat)">COD (Bayar di Tempat)</option>
                    <option value="E-Wallet (Dana/OVO/Gopay)">E-Wallet (Dana/OVO/Gopay)</option>
                </select>
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-semibold">
                Konfirmasi & Download Invoice
            </button>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
</body>
</html>
