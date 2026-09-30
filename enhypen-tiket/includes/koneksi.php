<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$database_url = getenv('DATABASE_URL');

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // PENTING: Mencegah error "cached plan must not change result type" di Neon PostgreSQL
    PDO::ATTR_EMULATE_PREPARES => true,
];

if ($database_url) {
    $dbopts = parse_url($database_url);
    
    $host     = $dbopts["host"] ?? '';
    $port     = $dbopts["port"] ?? 5432;
    $user     = $dbopts["user"] ?? '';
    $password = $dbopts["pass"] ?? '';
    $dbname   = isset($dbopts["path"]) ? ltrim($dbopts["path"], '/') : '';

    try {
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
        $pdo = new PDO($dsn, $user, $password, $options);
    } catch (PDOException $e) {
        die("Koneksi Database Railway Gagal: " . $e->getMessage());
    }
} else {
    $host     = 'ep-weathered-mountain-b5da5jwy-pooler.c-7.us-east-2.aws.neon.tech';
    $port     = 5432;
    $dbname   = 'neondb';
    $user     = 'neondb_owner';
    $password = 'npg_rfGHLPaNx20y';

    try {
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
        $pdo = new PDO($dsn, $user, $password, $options);
    } catch (PDOException $e) {
        die("Koneksi Database Fallback Gagal: " . $e->getMessage());
    }
}