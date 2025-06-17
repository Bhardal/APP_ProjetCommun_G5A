<?php
session_start();

$host   = 'mysql-bhardal.alwaysdata.net';
$dbname = 'bhardal_gustog5';
$user   = 'bhardal';    // adapte
$pass   = 'AZ3Q%h2VtyMXupM';        // adapte
$charset= 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("❌ Erreur de connexion : " . $e->getMessage());
}
