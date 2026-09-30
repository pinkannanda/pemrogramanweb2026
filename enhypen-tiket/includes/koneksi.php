<?php
// Mengambil DATABASE_URL dari environment Railway
$database_url = getenv('DATABASE_URL');

if ($database_url) {
    // Jalur Koneksi untuk Railway / Neon
    $dbopts = parse_url($database_url);
    
    $host     = $dbopts["host"];
    $port     = $dbopts["port"] ?? 5432;
    $user     = $dbopts["user"];
    $password = $dbopts["pass"];
    $dbname   = ltrim($dbopts["path"], '/');

    try {
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    } catch (PDOException $e) {
        die("Koneksi database gagal: " . $e->getMessage());
    }
} else {
    // Jalur Koneksi untuk Localhost
    $host     = '127.0.0.1';
    $dbname   = 'neondb';
    $user     = 'postgres';
    $password = 'secret';

    try {
        $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    } catch (PDOException $e) {
        die("Koneksi database gagal: " . $e->getMessage());
    }
}
?>