<?php

class Database
{
  public static function connect(): PDO
  {
    // En Docker: host=db, port=3306
    // En Windows/XAMPP: host=localhost, port=3306 (o el que tenga)
    $host = defined('DB_HOST') ? DB_HOST : (getenv('DB_HOST') ?: 'localhost');
    $port = getenv('DB_PORT') ?: '3306';
    $name = defined('DB_NAME') ? DB_NAME : (getenv('DB_NAME') ?: 'comercializadora_sosa');
    $user = defined('DB_USER') ? DB_USER : (getenv('DB_USER') ?: 'root');
    $pass = defined('DB_PASS') ? DB_PASS : (getenv('DB_PASS') ?: '');

    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

    $pdo = new PDO($dsn, $user, $pass, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    
    // ✅ Configurar zona horaria de MySQL para Guatemala
    $pdo->exec("SET time_zone = '-06:00'");
    
    return $pdo;
  }
}
