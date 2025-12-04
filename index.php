
<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sehat Selalu - Solusi Kesehatan Anda</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">

<?php include 'includes/header.php'; ?>

<section class="bg-blue-500 text-white py-16 text-center">
    <h1 class="text-4xl font-bold mb-4">Selamat Datang di Sehat Selalu</h1>
    <p class="text-lg">Belanja Obat dengan aman, mudah, dan cepat</p>
</section>

<section class="container mx-auto py-16 px-4">
    <h2 class="text-3xl font-bold text-center mb-12 text-blue-600">Tentang Kami</h2>
    
    <div class="flex flex-col md:flex-row items-center md:items-start md:space-x-8 max-w-5xl mx-auto">
        <!-- Gambar di sebelah kiri -->
        <div class="w-full md:w-1/2 mb-8 md:mb-0">
            <img src="assets/img/apotek.jpg" alt="Tentang Apotek Sehat" class="rounded-lg shadow-lg w-full h-auto object-cover">
        </div>

        <!-- Teks di sebelah kanan -->
        <div class="w-full md:w-1/2 text-center md:text-left">
            <p class="text-lg leading-relaxed">
                Sehat Selalu menyediakan berbagai macam obat, suplemen, dan alat kesehatan dengan harga terjangkau. 
                Kami berkomitmen untuk mendukung kesehatan masyarakat dengan layanan cepat dan terpercaya.
            </p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
</body>
</html>
