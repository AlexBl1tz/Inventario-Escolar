<?php
session_start();
include("conexion.php");

$error = ""; // variable vacía para evitar el warning

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = $_POST['usuario'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    if (!empty($usuario) && !empty($contrasena)) {
        $sql = "SELECT * FROM usuario WHERE nombre = '$usuario' AND contrasena = '$contrasena'";
        $resultado = $conn->query($sql);

        if ($resultado && $resultado->num_rows > 0) {
            $_SESSION['usuario'] = $usuario;
            header("Location: inventario.php");
            exit();
        } else {
            $error = "❌ Usuario o contraseña incorrectos.";
        }
    } else {
        $error = "⚠️ Por favor, completa todos los campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./css/style.css">
    <title>Login - Inventario</title>
</head>
<body>
    <div class="login-container">
        <h2>📚 Iniciar Sesión</h2>
        <form method="POST" action="login.php">
            <input type="text" name="usuario" placeholder="Usuario" required>
            <input type="password" name="contrasena" placeholder="Contraseña" required>
            <button type="submit">Entrar</button>
        </form>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    </div>

    <footer>© 2025 Sistema Escolar</footer>
</body>
</html>
