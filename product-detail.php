<link rel="stylesheet" href="assets/css/style.css">

<?php
include 'db.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Produk - Sehat Selalu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">

<?php include 'includes/header.php'; ?>

<section class="container mx-auto py-12 px-4">
<?php
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "
        SELECT p.*, c.name AS category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.id = $id
    ";
    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {

        // Tentukan path gambar
        $imagePath = 'assets/img/' . htmlspecialchars($row['image']);
        if (!file_exists($imagePath)) {
            $imagePath = 'assets/img/apotek.jpg'; // gambar default jika tidak ditemukan
        }

        echo '
        <div class="max-w-5xl mx-auto bg-white rounded-2xl shadow-md p-8 flex flex-col md:flex-row gap-10 items-start">
            <!-- Gambar Produk -->
            <img src="' . $imagePath . '" 
                 alt="' . htmlspecialchars($row['name']) . '" 
                 class="w-full md:w-1/2 h-96 object-cover rounded-xl shadow-sm">

            <!-- Detail Produk -->
            <div class="flex-1">
                <h2 class="text-4xl font-bold mb-3 text-gray-800">' . htmlspecialchars($row['name']) . '</h2>
                <p class="text-sm text-gray-500 mb-4 uppercase tracking-wide">' . htmlspecialchars($row['category_name']) . '</p>

                <p class="text-gray-700 mb-6 leading-relaxed">' . nl2br(htmlspecialchars($row['description'])) . '</p>

                <p class="text-blue-600 font-bold text-2xl mb-4">Rp ' . number_format($row['price'], 0, ',', '.') . '</p>
                <p class="text-gray-500 mb-8">Stok tersedia: <span class="font-semibold text-gray-700">' . htmlspecialchars($row['stock']) . '</span></p>

                <div class="flex items-center gap-4">
                    <!-- Tombol Beli Sekarang -->
                    <a href="buy.php?id=' . $row['id'] . '" 
                       class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                       Beli Sekarang
                    </a>

                    <!-- Tombol Masukkan Keranjang (diperbaiki posisi & ukuran) -->
                    <a href="add_to_cart.php?id=' . $row['id'] . '" 
                       class="bg-green-600 text-white w-12 h-12 flex items-center justify-center rounded-full hover:bg-green-700 transition ml-2"
                       title="Masukkan ke Keranjang">
                       <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                               d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.293 2.707a1 1 0 00.217 1.09l.083.083A1 1 0 007 17h10a1 1 0 00.993-.883L18 13M7 13l1.5-5h9L17 13M6 21h12a1 1 0 001-1v-1H5v1a1 1 0 001 1z" />
                       </svg>
                    </a>
                </div>
            </div>
        </div>';
    } else {
        echo '<p class="text-center text-gray-500">Produk tidak ditemukan.</p>';
    }
} else {
    echo '<p class="text-center text-gray-500">Tidak ada produk yang dipilih.</p>';
}
?>
</section>

<?php include 'includes/footer.php'; ?>
</body>
</html>
