<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "inventario"; // Tu base de datos creada en phpMyAdmin

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}
?>
