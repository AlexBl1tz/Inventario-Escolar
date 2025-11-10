<?php
session_start();
include("conexion.php");

$error = ""; // variable vacía para evitar el warning
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/style.css">
    <title>Login - Inventario</title>
</head>
<body>
    <div class="login-container">
        <h2>Inventario de Libros</h2>
        <a href="login.php">
            <button>Login</button>
        </a>
    </div>

    <footer>
        © 2025 Sistema Escolar
        Esc. Prim. Juan Enrique Pestalozzi
    </footer>
</body>
</html>
