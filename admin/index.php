<?php
session_start();
include '../db.php';

// Pastikan hanya admin yang bisa akses
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// Ambil data admin dari database
$user_id = $_SESSION['user_id'];
$query = $conn->prepare("SELECT * FROM users WHERE id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$admin = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Sehat Selalu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 flex">

<!-- Sidebar -->
<aside class="w-64 bg-blue-500 text-white h-screen flex flex-col fixed left-0 top-0 shadow-lg">
    <div class="p-6 text-center border-b border-whitw-500">
        <h1 class="text-2xl font-bold text-white">Sehat Selalu</h1>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-2 font-medium">
        <a href="index.php" class="flex items-center gap-3 px-4 py-2 rounded-lg bg-blue-500 hover:bg-blue-400 transition">
            <span>Dashboard</span>
        </a>
        <a href="manage_products.php" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-blue-500 transition">
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
    <!-- Hero Section (font biru, tanpa background) -->
    <section class="text-center mb-10">
        <h1 class="text-4xl font-extrabold mb-3 text-blue-500">
            Selamat Datang, <?= htmlspecialchars($admin['name']) ?: 'Admin' ?>!
        </h1>
        <p class="text-lg text-gray-600">Kelola sistem apotek dengan mudah, aman, dan efisien.</p>
    
</main>

</body>
</html>
