<?php
session_start();
include 'db.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Belanja - Sehat Selalu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">

    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="bg-blue-500 text-white py-16 text-center shadow-md">
        <h1 class="text-4xl font-bold mb-2">Keranjang Belanja</h1>
        <p class="text-lg opacity-90">Semoga lekas sembuh dan Sehat Selalu </p>
    </section>

    <!-- Konten Utama -->
    <section class="container mx-auto py-12 px-4">
        <?php if (!empty($_SESSION['cart'])): ?>
            <!-- Tabel Keranjang -->
            <div class="overflow-x-auto bg-white shadow-lg rounded-xl">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-blue-500 text-white">
                            <th class="p-4">Produk</th>
                            <th class="p-4 text-center">Harga</th>
                            <th class="p-4 text-center">Jumlah</th>
                            <th class="p-4 text-center">Subtotal</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        foreach ($_SESSION['cart'] as $item):
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;

                            // Tentukan path gambar
                            $imagePath = 'assets/img/' . htmlspecialchars($item['image']);
                            if (empty($item['image']) || !file_exists($imagePath)) {
                                $imagePath = 'assets/img/apotek.jpg'; // gambar default jika tidak ada
                            }
                        ?>
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="p-4 flex items-center gap-4">
                                <img src="<?= $imagePath ?>" 
                                     alt="<?= htmlspecialchars($item['name']) ?>" 
                                     class="w-16 h-16 object-cover rounded shadow">
                                <span class="font-medium text-gray-800">
                                    <?= htmlspecialchars($item['name']) ?>
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                Rp <?= number_format($item['price'], 0, ',', '.') ?>
                            </td>
                            <td class="p-4 text-center"><?= $item['quantity'] ?></td>
                            <td class="p-4 text-center">
                                Rp <?= number_format($subtotal, 0, ',', '.') ?>
                            </td>
                            <td class="p-4 text-center">
                                <a href="remove_from_cart.php?id=<?= $item['id'] ?>" 
                                   class="text-red-600 hover:text-red-800 hover:underline font-medium">
                                   Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Total dan Tombol Pembayaran -->
            <div class="text-right mt-8">
                <p class="text-xl font-semibold mb-4">
                    Total: 
                    <span class="text-blue-600">
                        Rp <?= number_format($total, 0, ',', '.') ?>
                    </span>
                </p>
                <a href="checkout_cart.php" 
                   class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-medium">
                   Lanjut ke Pembayaran
                </a>
            </div>

        <?php else: ?>
            <!-- Jika Keranjang Kosong -->
            <div class="text-center py-16 bg-white rounded-xl shadow-sm">
                <p class="text-gray-500 text-lg">Keranjang belanja Anda kosong 🛒</p>
                <a href="products.php" 
                   class="mt-6 inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-medium">
                   Lihat Produk
                </a>
            </div>
        <?php endif; ?>
    </section>

    <?php include 'includes/footer.php'; ?>

</body>
</html>
