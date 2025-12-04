<link rel="stylesheet" href="assets/css/style.css">

<?php
include 'db.php';
session_start();

// Ambil parameter filter & pencarian
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : 'all';

// Query dasar
$query = "
    SELECT p.*, c.name AS category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE 1
";

// ====== FILTER KATEGORI ======
if ($category !== 'all') {
    if ($category === 'Obat') {
        $query .= " AND (c.name LIKE '%Obat%' OR c.name LIKE '%Resep%')";
    } elseif ($category === 'Vitamin & Suplemen') {
        $query .= " AND (c.name LIKE '%Vitamin%' OR c.name LIKE '%Suplemen%')";
    } else {
        $query .= " AND c.name = '" . mysqli_real_escape_string($conn, $category) . "'";
    }
}

// ====== FILTER PENCARIAN GLOBAL ======
if (!empty($search)) {
    $searchSafe = mysqli_real_escape_string($conn, $search);
    $query = "
        SELECT p.*, c.name AS category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE (p.name LIKE '%$searchSafe%' 
               OR p.description LIKE '%$searchSafe%' 
               OR c.name LIKE '%$searchSafe%')
        ORDER BY p.created_at DESC
    ";
} else {
    $query .= " ORDER BY p.created_at DESC";
}

$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Produk - Sehat Selalu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">

<?php include 'includes/header.php'; ?>

<!-- Notifikasi -->
<?php
if (isset($_SESSION['message'])) {
    echo '
    <div class="container mx-auto mt-6 px-4">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative text-center shadow">
            ' . htmlspecialchars($_SESSION['message']) . '
        </div>
    </div>';
    unset($_SESSION['message']);
}
?>

<section class="bg-blue-500 text-white py-16 text-center">
    <h1 class="text-4xl font-bold mb-4">Produk Sehat Selalu</h1>
    <p class="text-lg">Temukan berbagai obat, vitamin, dan alat kesehatan terbaik untuk Anda</p>
</section>

<section class="container mx-auto py-12 px-4">

    <!-- Filter dan Search -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-10">
        <!-- Kategori -->
        <div class="flex flex-wrap gap-3 justify-center">
            <?php
            $categories = [
                'all' => 'Semua',
                'Obat' => 'Obat',
                'Vitamin & Suplemen' => 'Vitamin & Suplemen',
                'Alat Kesehatan' => 'Alat Kesehatan'
            ];

            foreach ($categories as $key => $label) {
                $active = ($category === $key) 
                    ? 'bg-blue-600 text-white' 
                    : 'bg-white text-blue-600 border border-blue-600';
                echo '<a href="?category=' . urlencode($key) . '&search=' . urlencode($search) . '" 
                        class="px-4 py-2 rounded-full font-medium transition ' . $active . ' hover:bg-blue-700 hover:text-white">'
                        . $label . '</a>';
            }
            ?>
        </div>

        <!-- Search Bar -->
        <form method="GET" class="flex gap-2 w-full md:w-1/3">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                   placeholder="Cari produk di semua kategori..." 
                   class="flex-grow border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring focus:ring-blue-300">
            <button type="submit" 
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                Cari
            </button>
        </form>
    </div>

    <!-- Daftar Produk -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                // Gunakan folder assets/img/ sebagai path gambar
                $imagePath = 'assets/img/' . htmlspecialchars($row['image']);
                // Jika file tidak ada, tampilkan gambar default
                if (!file_exists($imagePath)) {
                    $imagePath = 'assets/img/apotek.jpg';
                }

                echo '
                <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition duration-300 relative">
                    <img src="' . $imagePath . '" 
                         alt="' . htmlspecialchars($row['name']) . '" 
                         class="w-full h-56 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-1 text-gray-800">' . htmlspecialchars($row['name']) . '</h3>
                        <p class="text-sm text-gray-500 mb-3">' . htmlspecialchars($row['category_name']) . '</p>
                        <p class="text-gray-600 text-sm mb-4">' . htmlspecialchars(substr($row['description'], 0, 90)) . '...</p>
                        <p class="text-blue-600 font-bold mb-3">Rp ' . number_format($row['price'], 0, ',', '.') . '</p>
                        <p class="text-gray-500 text-sm mb-4">Stok: ' . htmlspecialchars($row['stock']) . '</p>

                        <div class="flex items-center justify-between mt-4">
                            <a href="product-detail.php?id=' . $row['id'] . '" 
                               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                               Lihat Detail
                            </a>
                            <a href="add_to_cart.php?id=' . $row['id'] . '" 
                               class="bg-green-600 text-white p-2 rounded-full hover:bg-green-700 transition flex items-center justify-center"
                               title="Masukkan ke Keranjang">
                               <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                       d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.293 2.707a1 1 0 00.217 1.09l.083.083A1 1 0 007 17h10a1 1 0 00.993-.883L18 13M7 13l1.5-5h9L17 13M6 21h12a1 1 0 001-1v-1H5v1a1 1 0 001 1z" />
                               </svg>
                            </a>
                        </div>
                    </div>
                </div>';
            }
        } else {
            echo '<p class="col-span-3 text-center text-gray-500">Produk tidak ditemukan.</p>';
        }
        ?>
    </div> 
</section>

<?php include 'includes/footer.php'; ?>
</body>
</html>
