<?php
// Aktifkan pelaporan error PHP agar jika ada crash, pesannya langsung muncul di layar (bukan HTTP 500)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$database_url = getenv('DATABASE_URL');

if ($database_url) {
    // Lingkungan Railway (Neon PostgreSQL via Environment Variable)
    $dbopts = parse_url($database_url);
    
    $host     = $dbopts["host"] ?? '';
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
        die("Koneksi Database Railway Gagal: " . $e->getMessage());
    }
} else {
    // Lingkungan Localhost / Fallback Direct Neon PostgreSQL
    $host     = 'ep-weathered-mountain-b5da5jwy-pooler.c-7.us-east-2.aws.neon.tech';
    $port     = 5432;
    $dbname   = 'neondb';
    $user     = 'neondb_owner';
    $password = 'npg_rfGHLPaNx20y';

    try {
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    } catch (PDOException $e) {
        die("Koneksi Database Fallback Gagal: " . $e->getMessage());
    }
}