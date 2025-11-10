<?php
include("conexion.php");
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: inventario.php");
    exit();
}

$id = intval($_GET['id']);
$sql = $conn->query("SELECT * FROM prestamo WHERE idPrestamo = $id");

if ($sql->num_rows == 0) {
    echo "❌ Préstamo no encontrado.";
    exit();
}

$p = $sql->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Préstamo</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: #f4f6ff; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh;
        }
        form {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            width: 400px;
            text-align: center;
        }
        input, button {
            width: 90%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 10px;
        }
        button {
            background-color: #5563DE;
            color: white;
            border: none;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background-color: #3846B2;
        }
        a {
            display: inline-block;
            margin-top: 10px;
            color: #5563DE;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <form action="guardar_edicion_prestamo.php" method="POST">
        <h2>✏️ Editar Préstamo</h2>
        <input type="hidden" name="idPrestamo" value="<?= $p['idPrestamo'] ?>">
        <input type="number" name="idLibro" value="<?= $p['idLibro'] ?>" placeholder="ID del libro" required>
        <input type="text" name="persona" value="<?= htmlspecialchars($p['persona']) ?>" placeholder="Persona que recibe" required>
        <input type="date" name="fecPrestamo" value="<?= $p['fecPrestamo'] ?>" required>
        <input type="date" name="fecDevolucion" value="<?= $p['fecDevolucion'] ?>" required>
        <button type="submit">💾 Guardar Cambios</button>
        <br>
        <a href="inventario.php">⬅️ Volver</a>
    </form>
</body>
</html>
