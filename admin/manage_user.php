<?php
session_start();
include '../db.php';

// Pastikan hanya admin yang bisa akses
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// Tambah pengguna baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = trim($_POST['role']);

    if (!empty($name) && !empty($email) && !empty($password) && !empty($role)) {
        $query = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $query->bind_param("ssss", $name, $email, $password, $role);
        $query->execute();
        header("Location: manage_user.php?success=1");
        exit;
    }
}

// Hapus pengguna
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM users WHERE id = $id");
    header("Location: manage_user.php?deleted=1");
    exit;
}

// Ambil semua user dengan role customer saja
$users = $conn->query("SELECT * FROM users WHERE role = 'customer' ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Pengguna - Admin Sehat Selalu</title>
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
        <a href="manage_order.php" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-blue-500 transition">
            <span>Order</span>
        </a>
        <a href="manage_user.php" class="flex items-center gap-3 px-4 py-2 rounded-lg bg-blue-500 hover:bg-blue-400 transition">
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
    <h1 class="text-3xl font-bold text-blue-500 mb-6">Manajemen Pengguna</h1>

    <!-- Notifikasi -->
    <?php if (isset($_GET['success'])): ?>
        <p class="bg-green-100 text-green-700 p-3 rounded mb-4">Pengguna berhasil ditambahkan!</p>
    <?php elseif (isset($_GET['deleted'])): ?>
        <p class="bg-red-100 text-red-700 p-3 rounded mb-4">Pengguna berhasil dihapus!</p>
    <?php endif; ?>

    <!-- Tabel Pengguna -->
    <div class="bg-white shadow-lg rounded-xl p-6 mb-10">
        <h2 class="text-xl font-semibold mb-4 text-blue-500">Daftar Pengguna</h2>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-blue-500 text-white text-left">
                        <th class="p-3">ID</th>
                        <th class="p-3">Nama</th>
                        <th class="p-3">Email</th>
                        <th class="p-3 text-center">Role</th>
                        <th class="p-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users && $users->num_rows > 0): ?>
                        <?php while ($user = $users->fetch_assoc()): ?>
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="p-3"><?= $user['id'] ?></td>
                                <td class="p-3 font-semibold"><?= htmlspecialchars($user['name'] ?: '-') ?></td>
                                <td class="p-3"><?= htmlspecialchars($user['email']) ?></td>
                                <td class="p-3 text-center">
                                    <?php
                                        $role = $user['role'] ?: 'customer';
                                        $color = $role === 'admin' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700';
                                    ?>
                                    <span class="px-3 py-1 rounded-full text-sm font-medium <?= $color ?>">
                                        <?= ucfirst($role) ?>
                                    </span>
                                </td>
                                <td class="p-3 text-center">
                                    <?php if ($user['role'] !== 'admin'): ?>
                                        <a href="?delete=<?= $user['id'] ?>" onclick="return confirm('Yakin ingin menghapus pengguna ini?')" class="text-red-600 hover:underline">Hapus</a>
                                    <?php else: ?>
                                        <span class="text-gray-400 italic">Admin</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-4 text-gray-500">Belum ada pengguna.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

   
</main>

</body>
</html>
