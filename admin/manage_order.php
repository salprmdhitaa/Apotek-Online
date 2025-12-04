<?php
session_start();
include '../db.php';

// Pastikan hanya admin yang bisa akses
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// Ambil semua pesanan urut dari ID terkecil
$query = "
    SELECT o.id, o.user_id, o.total AS total_price, o.status, o.created_at,
           u.name AS customer_name, u.email
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    ORDER BY o.id ASC
";
$orders = $conn->query($query);

// Jika query gagal, tampilkan pesan error
if (!$orders) {
    die("<p style='color:red; padding:20px;'>Query gagal: " . $conn->error . "</p>");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Pesanan - Admin Sehat Selalu</title>
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
        <a href="manage_products.php" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-blue-500 transition">
            <span>Product</span>
        </a>
        <a href="manage_order.php" class="flex items-center gap-3 px-4 py-2 rounded-lg bg-blue-500 hover:bg-blue-400 transition">
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
    <h1 class="text-3xl font-bold text-blue-500 mb-6">Manajemen Pesanan</h1>

    <div class="bg-white shadow-lg rounded-xl p-6">
        <h2 class="text-xl font-semibold mb-4 text-blue-500">Daftar Pesanan</h2>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-blue-500 text-white text-left">
                        <th class="p-3 text-center">ID</th>
                        <th class="p-3 text-center">User ID</th>
                        <th class="p-3 text-right">Total</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-center">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($orders->num_rows > 0): ?>
                        <?php while ($order = $orders->fetch_assoc()): ?>
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="p-3 text-center font-semibold text-blue-600"><?= $order['id'] ?></td>
                                <td class="p-3 text-center"><?= htmlspecialchars($order['user_id']) ?></td>
                                <td class="p-3 text-right">Rp <?= number_format($order['total_price'], 0, ',', '.') ?></td>
                                <td class="p-3 text-center">
                                    <?php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-700',
                                            'processing' => 'bg-blue-100 text-blue-700',
                                            'completed' => 'bg-green-100 text-green-700',
                                            'cancelled' => 'bg-red-100 text-red-700'
                                        ];
                                        $color = $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-700';
                                    ?>
                                    <span class="px-3 py-1 rounded-full text-sm font-medium <?= $color ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </td>
                                <td class="p-3 text-center"><?= date('d M Y H:i', strtotime($order['created_at'])) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-gray-500">Belum ada pesanan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

</body>
</html>
