<?php
require_once 'config/database.php';
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) { die("Koneksi gagal: " . $conn->connect_error); }
$username = 'admin';
$password = 'admin123';
$role = 1; // 1 = admin
$password_hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
$stmt->bind_param("ssi", $username, $password_hash, $role);
if ($stmt->execute()) { echo "Pengguna admin berhasil ditambahkan! Silakan hapus file ini."; }
else { echo "Gagal menambahkan pengguna admin: " . $stmt->error; }
$stmt->close();
$conn->close();
?>