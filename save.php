<?php
$dataFile = 'data.json';
$uploadsDir = 'uploads/';

if (!file_exists($uploadsDir)) {
    mkdir($uploadsDir, 0777, true);
}

// 🔍 Cek: apakah ini JSON dari fetch()?
$contentType = $_SERVER["CONTENT_TYPE"] ?? '';
if (strpos($contentType, 'application/json') !== false) {
    // Handle dari JavaScript (edit/hapus)
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (is_array($data)) {
        file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
        echo "Data updated via fetch.";
    } else {
        echo "Invalid JSON.";
    }

    exit();
}

// 🔽 Handle dari form register.html
$data = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];

$nama = $_POST["nama"] ?? "";
$nim = $_POST["nim"] ?? "";
$email = $_POST["email"] ?? "";
$imageName = "";

// 🚫 Validasi data kosong
if (empty($nama) || empty($nim) || empty($email) || $_FILES['image']['error'] !== 0) {
    header("Location: register.html");
    exit();
}

// ✅ Upload foto
if ($_FILES['image']['error'] === 0) {
    $imageName = $uploadsDir . basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], $imageName);
}

// Tambahkan data baru
$data[] = [
    "nama" => $nama,
    "nim" => $nim,
    "email" => $email,
    "image" => $imageName
];

file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
header("Location: data.html");
exit();
