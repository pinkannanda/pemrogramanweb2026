<?php
// Ambil DATABASE_URL dari Railway
$database_url = getenv('DATABASE_URL');

if ($database_url) {
    // Lingkungan Railway (Neon PostgreSQL)
    $dbopts = parse_url($database_url);
    
    $host     = $dbopts["host"] ?? ''; // Perbaikan: "host" tanpa spasi
    $port     = $dbopts["port"] ?? 5432;
    $user     = $dbopts["user"] ?? '';
    $password = $dbopts["pass"] ?? '';
    $dbname   = isset($dbopts["path"]) ? ltrim($dbopts["path"], '/') : '';

    try {
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    } catch (PDOException $e) {
        die("Koneksi database gagal: " . $e->getMessage());
    }
} else {
    // Lingkungan Localhost (Fallback)
    $host     = '127.0.0.1';
    $dbname   = 'neondb';
    $user     = 'postgres';
    $password = 'secret';

    try {
        $dsn = "pgsql:host=$host;dbname=$dbname";
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    } catch (PDOException $e) {
        die("Koneksi database gagal: " . $e->getMessage());
    }
}
?>