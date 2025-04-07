<?php
// Activa la visualización de errores (solo en ambiente de desarrollo)
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = "localhost:3307";
$db   = "legumbreria";  // Cambia por el nombre de tu base de datos
$user = "root";               // Cambia por tu usuario de base de datos
$pass = "";            // Cambia por la contraseña correspondiente

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión: " . $e->getMessage());
}
?>
