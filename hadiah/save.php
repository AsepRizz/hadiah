<?php
// File: save.php
require 'db.php';

// Ambil data dari POST
$imageData = $_POST['image'] ?? '';
$latitude = floatval($_POST['latitude'] ?? 0);
$longitude = floatval($_POST['longitude'] ?? 0);

// Validasi data
if (empty($imageData)) {
    die("Data gambar tidak valid");
}

// Ekstrak data gambar
$imageData = str_replace('data:image/jpeg;base64,', '', $imageData);
$imageData = base64_decode($imageData);

// Generate nama file unik
$filename = 'capture_' . time() . '_' . bin2hex(random_bytes(4)) . '.jpg';
$filepath = 'images/' . $filename;

// Pastikan folder images ada
if (!file_exists('images')) {
    mkdir('images', 0777, true);
}

// Simpan gambar
if (file_put_contents($filepath, $imageData)) {
    // Simpan ke database
    $stmt = $pdo->prepare("INSERT INTO captured_data (image_filename, latitude, longitude) VALUES (?, ?, ?)");
    if ($stmt->execute([$filename, $latitude, $longitude])) {
        echo "Data berhasil disimpan";
    } else {
        echo "Gagal menyimpan ke database";
    }
} else {
    echo "Gagal menyimpan gambar";
}
?>