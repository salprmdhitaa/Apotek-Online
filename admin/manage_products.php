<?php
session_start();
include '../db.php';

// Pastikan hanya admin yang bisa akses
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// ===== Tambah Produk =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category_id = intval($_POST['category_id']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $image = '';
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../assets/img/";
        $image = basename($_FILES['image']['name']);
        $targetFile = $targetDir . $image;
        move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);
    }

    $query = $conn->prepare("INSERT INTO products (name, category_id, price, stock, description, image) VALUES (?, ?, ?, ?, ?, ?)");
    $query->bind_param("sidiss", $name, $category_id, $price, $stock, $description, $image);
    $query->execute();
    header("Location: manage_products.php?success=1");
    exit;
}

// ===== Hapus Produk =====
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM products WHERE id = $id");
    header("Location: manage_products.php?deleted=1");
    exit;
}

// ===== Edit Produk =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category_id = intval($_POST['category_id']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $query = "UPDATE products SET name='$name', category_id=$category_id, price=$price, stock=$stock, description='$description'";

    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../assets/img/";
        $image = basename($_FILES['image']['name']);
        $targetFile = $targetDir . $image;
        move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);
        $query .= ", image='$image'";
    }

    $query .= " WHERE id=$id";
    $conn->query($query);
    header("Location: manage_products.php?updated=1");
    exit;
}

// ===== Ambil Data Produk =====
$products = $conn->query("SELECT p.*, c.name AS category_name FROM products p 
                          LEFT JOIN categories c ON p.category_id = c.id 
                          ORDER BY p.id DESC");
$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Produk - Admin Sehat Selalu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 flex">

<!-- Sidebar -->
<aside class="w-64 bg-blue-500 text-white h-screen flex flex-col fixed left-0 top-0 shadow-lg">
    <div class="p-6 text-center border-b border-whitw-500">
        <h1 class="text-2xl font-bold text-white">Sehat Selalu</h1>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-2 font-medium">
        <a href="index.php" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-blue-500 transition">
            <span>Dashboard</span>
        </a>
        <a href="manage_products.php" class="flex items-center gap-3 px-4 py-2 rounded-lg bg-blue-500 hover:bg-blue-400 transition">
            <span>Product</span>
        </a>
        <a href="manage_order.php" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-blue-500 transition">
            <span>Order</span>
        </a>
        <a href="manage_user.php" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-blue-500 transition">
            <span>User</span>
        </a>
    </nav>

    <div class="p-4 border-t border-white-500">
        <a href="logout.php" class="w-full bg-white-500 hover:bg-white-600 text-blue px-4 py-2 rounded-lg block text-center font-medium transition">
            Logout
        </a>
    </div>
</aside>

<!-- Main Content -->
<main class="ml-64 w-full min-h-screen p-10">
    <h1 class="text-3xl font-bold text-blue-500 mb-6">Manajemen Produk</h1>

    <!-- Notifikasi -->
    <?php if (isset($_GET['success'])): ?>
        <p class="bg-green-100 text-green-700 p-3 rounded mb-4">Produk berhasil ditambahkan!</p>
    <?php elseif (isset($_GET['deleted'])): ?>
        <p class="bg-red-100 text-red-700 p-3 rounded mb-4">Produk berhasil dihapus!</p>
    <?php elseif (isset($_GET['updated'])): ?>
        <p class="bg-blue-100 text-blue-700 p-3 rounded mb-4">Produk berhasil diperbarui!</p>
    <?php endif; ?>

    <!-- ===== Daftar Produk ===== -->
    <div class="bg-white shadow-lg rounded-xl p-6 mb-10">
        <h2 class="text-xl font-semibold mb-4 text-blue-500">Daftar Produk</h2>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-blue-500 text-white text-left">
                        <th class="p-3">Gambar</th>
                        <th class="p-3">Nama</th>
                        <th class="p-3">Kategori</th>
                        <th class="p-3 text-right">Harga</th>
                        <th class="p-3 text-center">Stok</th>
                        <th class="p-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($p = $products->fetch_assoc()): 
                        $imagePath = "../assets/img/" . ($p['image'] ?: 'apotek.jpg');
                        if (!file_exists($imagePath)) $imagePath = "../assets/img/apotek.jpg";
                    ?>
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="p-3">
                            <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="w-16 h-16 object-cover rounded">
                        </td>
                        <td class="p-3 font-semibold"><?= htmlspecialchars($p['name']) ?></td>
                        <td class="p-3"><?= htmlspecialchars($p['category_name'] ?: '-') ?></td>
                        <td class="p-3 text-right">Rp <?= number_format($p['price'], 0, ',', '.') ?></td>
                        <td class="p-3 text-center"><?= $p['stock'] ?></td>
                        <td class="p-3 text-center">
                            <button 
                                class="text-blue-600 hover:underline mr-2"
                                onclick="openEditModal(<?= htmlspecialchars(json_encode($p)) ?>)">
                                Edit
                            </button>
                            <a href="?delete=<?= $p['id'] ?>" 
                               onclick="return confirm('Yakin ingin menghapus produk ini?')" 
                               class="text-red-600 hover:underline">
                               Hapus
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ===== Form Tambah Produk ===== -->
    <div class="bg-white shadow-lg rounded-xl p-6">
        <h2 class="text-xl font-semibold mb-4 text-blue-500">Tambah Produk Baru</h2>
        <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-700">Nama Produk</label>
                <input type="text" name="name" required class="w-full border rounded-lg p-2">
            </div>
            <div>
                <label class="block text-gray-700">Kategori</label>
                <select name="category_id" required class="w-full border rounded-lg p-2">
                    <option value="">-- Pilih Kategori --</option>
                    <?php
                    $catList = $conn->query("SELECT * FROM categories ORDER BY name ASC");
                    while ($cat = $catList->fetch_assoc()):
                    ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label class="block text-gray-700">Harga</label>
                <input type="number" name="price" required class="w-full border rounded-lg p-2">
            </div>
            <div>
                <label class="block text-gray-700">Stok</label>
                <input type="number" name="stock" required class="w-full border rounded-lg p-2">
            </div>
            <div class="md:col-span-2">
                <label class="block text-gray-700">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full border rounded-lg p-2"></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-gray-700">Gambar Produk</label>
                <input type="file" name="image" accept="image/*" class="w-full border rounded-lg p-2">
            </div>
            <div class="md:col-span-2 text-right">
                <button type="submit" name="add_product" class="bg-blue-500 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                    Tambah Produk
                </button>
            </div>
        </form>
    </div>
</main>

<!-- ===== Modal Edit ===== -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center hidden">
    <div class="bg-white p-6 rounded-xl shadow-lg w-11/12 md:w-2/3 lg:w-1/2">
        <h2 class="text-2xl font-bold text-blue-600 mb-4">Edit Produk</h2>
        <form method="POST" enctype="multipart/form-data" id="editForm" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="hidden" name="id" id="edit_id">
            <div>
                <label class="block text-gray-700">Nama Produk</label>
                <input type="text" name="name" id="edit_name" required class="w-full border rounded-lg p-2">
            </div>
            <div>
                <label class="block text-gray-700">Kategori</label>
                <select name="category_id" id="edit_category" class="w-full border rounded-lg p-2">
                    <?php
                    $catList = $conn->query("SELECT * FROM categories ORDER BY name ASC");
                    while ($c = $catList->fetch_assoc()) {
                        echo "<option value='{$c['id']}'>{$c['name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div>
                <label class="block text-gray-700">Harga</label>
                <input type="number" name="price" id="edit_price" class="w-full border rounded-lg p-2">
            </div>
            <div>
                <label class="block text-gray-700">Stok</label>
                <input type="number" name="stock" id="edit_stock" class="w-full border rounded-lg p-2">
            </div>
            <div class="md:col-span-2">
                <label class="block text-gray-700">Deskripsi</label>
                <textarea name="description" id="edit_description" rows="3" class="w-full border rounded-lg p-2"></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-gray-700">Gambar Produk (opsional)</label>
                <input type="file" name="image" accept="image/*" class="w-full border rounded-lg p-2">
            </div>
            <div class="md:col-span-2 text-right space-x-3">
                <button type="button" onclick="closeEditModal()" class="bg-gray-300 px-4 py-2 rounded-lg">Batal</button>
                <button type="submit" name="edit_product" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(data) {
    document.getElementById('editModal').classList.remove('hidden');
    document.getElementById('edit_id').value = data.id;
    document.getElementById('edit_name').value = data.name;
    document.getElementById('edit_category').value = data.category_id;
    document.getElementById('edit_price').value = data.price;
    document.getElementById('edit_stock').value = data.stock;
    document.getElementById('edit_description').value = data.description;
}
function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>

</body>
</html>
