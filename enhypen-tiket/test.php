<?php

$host = "localhost";
$port = "5432";
$db   = "dbtiket_enhypen";
$user = "postgres";
$pass = "12345678";

try {
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$db",
        $user,
        $pass
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "KONEKSI BERHASIL<br>";
    echo "Database: " . $db;
} catch (PDOException $e) {
    echo "KONEKSI GAGAL<br>";
    echo $e->getMessage();
}