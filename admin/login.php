<?php
session_start();
include '../db.php';

$message = "";

// === REGISTER ===
if (isset($_POST['register'])) {
    $name = trim($_POST['nama']); // 🔹 ambil input nama
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $phone = trim($_POST['phone']);
    $role = trim($_POST['role']);

    if (!empty($name) && !empty($email) && !empty($password) && !empty($phone) && !empty($role)) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // cek apakah email sudah terdaftar
        $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $message = "Email sudah terdaftar!";
        } else {
            // 🔹 Simpan nama ke kolom `name`
            $query = $conn->prepare("INSERT INTO users (name, email, password, phone, role, address) VALUES (?, ?, ?, ?, ?, '')");
            $query->bind_param("sssss", $name, $email, $hashedPassword, $phone, $role);
            if ($query->execute()) {
                $message = "Registrasi berhasil! Silakan login.";
            } else {
                $message = "Terjadi kesalahan saat mendaftar.";
            }
        }
    } else {
        $message = "Semua kolom wajib diisi.";
    }
}

// === LOGIN ===
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['user_role'] = $row['role'];

            // arahkan ke halaman sesuai role
            if ($row['role'] === 'admin') {
                header("Location: ../admin/index.php");
            } else {
                header("Location: ../index.php");
            }
            exit;
        } else {
            $message = "Password salah!";
        }
    } else {
        $message = "Akun tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Sehat Selalu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function toggleForm() {
            document.getElementById('login-form').classList.toggle('hidden');
            document.getElementById('register-form').classList.toggle('hidden');
        }
    </script>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">

    <div class="bg-white shadow-lg rounded-2xl p-8 w-full max-w-md">
        <h2 class="text-3xl font-bold text-center text-blue-500 mb-6">Sehat Selalu</h2>

        <?php if (!empty($message)): ?>
            <p class="text-center text-red-500 font-medium mb-4"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <!-- FORM LOGIN -->
        <form id="login-form" method="POST" class="space-y-4 <?= isset($_POST['register']) ? 'hidden' : '' ?>">
            <h3 class="text-xl font-semibold text-gray-700 text-center">Masuk ke Akun</h3>

            <div>
                <label class="block text-gray-700">Email</label>
                <input type="email" name="email" required class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label class="block text-gray-700">Password</label>
                <input type="password" name="password" required class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-400">
            </div>

            <button type="submit" name="login" class="bg-blue-500 text-white w-full py-2 rounded-lg hover:bg-blue-700 transition">
                Login
            </button>

            <p class="text-center text-gray-600 mt-4 text-sm">
                Belum punya akun?
                <button type="button" onclick="toggleForm()" class="text-blue-500 font-semibold hover:underline">
                    Daftar akun baru
                </button>
            </p>
        </form>

        <!-- FORM REGISTER -->
        <form id="register-form" method="POST" class="space-y-4 <?= isset($_POST['register']) ? '' : 'hidden' ?>">
            <h3 class="text-xl font-semibold text-gray-700 text-center">Daftar Akun Baru</h3>

            <div>
                <label class="block text-gray-700">Nama</label>
                <input type="text" name="nama" required class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-green-400">
            </div>

            <div>
                <label class="block text-gray-700">Email</label>
                <input type="email" name="email" required class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-green-400">
            </div>

            <div>
                <label class="block text-gray-700">Password</label>
                <input type="password" name="password" required class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-green-400">
            </div>

            <div>
                <label class="block text-gray-700">Nomor Telepon</label>
                <input type="text" name="phone" required class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-green-400">
            </div>

            <div>
                <label class="block text-gray-700">Role</label>
                <select name="role" required class="w-full border rounded-lg p-2">
                    <option value="user">Customer</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <button type="submit" name="register" class="bg-blue-500 text-white w-full py-2 rounded-lg hover:bg-blue-700 transition">
                Daftar
            </button>

            <p class="text-center text-gray-600 mt-4 text-sm">
                Sudah punya akun?
                <button type="button" onclick="toggleForm()" class="text-blue-500 font-semibold hover:underline">
                    Login sekarang
                </button>
            </p>
        </form>
    </div>

</body>
</html>
