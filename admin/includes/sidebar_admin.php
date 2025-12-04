<?php
// Pastikan user login dan role admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}
?>
<!-- Sidebar -->
<aside class="w-64 bg-blue-600 text-white h-screen flex flex-col fixed left-0 top-0 shadow-lg">
    <div class="p-6 text-center border-b border-blue-500">
        <h1 class="text-2xl font-bold">Apotek Sehat</h1>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-2 font-medium">
        <a href="index.php" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-blue-500 transition">🏠 Dashboard</a>
        <a href="products_manage.php" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-blue-500 transition">💊 Produk</a>
        <a href="orders_manage.php" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-blue-500 transition">🧾 Pesanan</a>
        <a href="users_manage.php" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-blue-500 transition">👥 Pengguna</a>
    </nav>

    <div class="p-4 border-t border-blue-500">
        <a href="logout.php" class="block bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-center font-medium">
            Logout
        </a>
    </div>
</aside>
