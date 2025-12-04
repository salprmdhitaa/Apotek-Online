<?php
session_start();
include 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = mysqli_query($conn, "SELECT * FROM products WHERE id = $id");

    if ($row = mysqli_fetch_assoc($result)) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Jika produk sudah ada di keranjang, tambah jumlah
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity']++;
        } else {
            // Tambahkan produk baru ke keranjang
            $_SESSION['cart'][$id] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'price' => $row['price'],
                'image' => $row['image'],
                'quantity' => 1
            ];
        }

        $_SESSION['message'] = "Produk " . htmlspecialchars($row['name']) . " berhasil dimasukkan ke keranjang!";
    } else {
        $_SESSION['message'] = "Produk tidak ditemukan.";
    }
} else {
    $_SESSION['message'] = "ID produk tidak valid.";
}

// Kembali ke halaman produk
header("Location: products.php");
exit;
