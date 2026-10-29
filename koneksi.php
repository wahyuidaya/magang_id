<?php
$host     = trim($_ENV['DB_HOST']     ?? getenv('DB_HOST')     ?: 'localhost');
$username = trim($_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME') ?: 'root');
$password = trim($_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: '');
$database = trim($_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE') ?: 'dbmagangid');
$useSSL   = filter_var(trim($_ENV['DB_SSL'] ?? getenv('DB_SSL') ?: 'false'), FILTER_VALIDATE_BOOLEAN);

$conn = mysqli_init();

if ($useSSL) {
    mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);
    mysqli_real_connect($conn, $host, $username, $password, $database, 4000, NULL, MYSQLI_CLIENT_SSL);
} else {
    mysqli_real_connect($conn, $host, $username, $password, $database);
}

if (mysqli_connect_errno()) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>
