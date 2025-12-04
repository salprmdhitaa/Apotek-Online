<?php
session_start();
include 'db.php';

// Jika keranjang kosong, kembali ke halaman cart
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $telepon = mysqli_real_escape_string($conn, $_POST['telepon']);
    $pembayaran = mysqli_real_escape_string($conn, $_POST['pembayaran']);

    // Hitung total belanja
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    // Simpan order utama ke tabel orders
    $order_query = "INSERT INTO orders (user_id, total, status) VALUES (1, $total, 'pending')";
    if (!mysqli_query($conn, $order_query)) {
        die("Gagal menyimpan pesanan: " . mysqli_error($conn));
    }
    $order_id = mysqli_insert_id($conn);

    // Simpan semua item ke tabel order_items dan kurangi stok produk
    foreach ($_SESSION['cart'] as $item) {
        $pid = $item['id'];
        $qty = $item['quantity'];
        $price = $item['price'];

        mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, quantity, price) 
                             VALUES ($order_id, $pid, $qty, $price)");

        mysqli_query($conn, "UPDATE products SET stock = stock - $qty WHERE id = $pid");
    }

    // Simpan data item untuk invoice sebelum keranjang dihapus
    $items = $_SESSION['cart'];
    unset($_SESSION['cart']); // Hapus keranjang setelah checkout

    // Panggil generator invoice PDF
    require_once __DIR__ . '/generate_invoice_cart.php';

    // Pastikan fungsi tersedia sebelum dipanggil
    if (function_exists('generate_invoice_cart_pdf')) {
        generate_invoice_cart_pdf($order_id, $nama, $alamat, $telepon, $pembayaran, $items, $total);
    } else {
        die("Error: Fungsi generate_invoice_cart_pdf() tidak ditemukan.");
    }

    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran - Sehat Selalu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">

<?php include 'includes/header.php'; ?>

<section class="container mx-auto py-12 px-4">
    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg p-8">
        <h2 class="text-3xl font-bold text-center text-blue-600 mb-6">Form Pembayaran</h2>

        <!-- Tabel Produk yang Akan Dibeli -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-700 mb-3">Produk yang Akan Dibeli</h3>
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-blue-600 text-white text-left">
                        <th class="p-3">Nama Produk</th>
                        <th class="p-3 text-center">Jumlah</th>
                        <th class="p-3 text-right">Harga</th>
                        <th class="p-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    foreach ($_SESSION['cart'] as $item):
                        $subtotal = $item['price'] * $item['quantity'];
                        $total += $subtotal;
                    ?>
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="p-3 font-medium"><?= htmlspecialchars($item['name']) ?></td>
                        <td class="p-3 text-center"><?= $item['quantity'] ?></td>
                        <td class="p-3 text-right">Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                        <td class="p-3 text-right">Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="font-bold bg-gray-100">
                        <td colspan="3" class="p-3 text-right">Total</td>
                        <td class="p-3 text-right text-blue-600">Rp <?= number_format($total, 0, ',', '.') ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Form Pembayaran -->
        <form method="POST" class="space-y-4">
            <div>
                <label class="block mb-2 font-semibold">Nama Lengkap</label>
                <input type="text" name="nama" required
                       class="w-full border border-gray-300 rounded-lg p-2 focus:ring focus:ring-blue-300">
            </div>

            <div>
                <label class="block mb-2 font-semibold">Alamat Lengkap</label>
                <textarea name="alamat" required
                          class="w-full border border-gray-300 rounded-lg p-2 focus:ring focus:ring-blue-300"></textarea>
            </div>

            <div>
                <label class="block mb-2 font-semibold">Nomor Telepon</label>
                <input type="text" name="telepon" required
                       class="w-full border border-gray-300 rounded-lg p-2 focus:ring focus:ring-blue-300">
            </div>

            <div>
                <label class="block mb-2 font-semibold">Metode Pembayaran</label>
                <select name="pembayaran" required
                        class="w-full border border-gray-300 rounded-lg p-2 focus:ring focus:ring-blue-300">
                    <option value="">-- Pilih Metode Pembayaran --</option>
                    <option value="Transfer Bank">Transfer Bank</option>
                    <option value="COD (Bayar di Tempat)">COD (Bayar di Tempat)</option>
                    <option value="E-Wallet (Dana/OVO/Gopay)">E-Wallet (Dana/OVO/Gopay)</option>
                </select>
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                Konfirmasi & Download Invoice
            </button>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
</body>
</html>
