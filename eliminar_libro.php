<?php
include("conexion.php");
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$ID = $_GET['id'];

// 🟢 Usa el nombre correcto de la tabla que realmente existe:
$ELIMINAR = "DELETE FROM libro WHERE idLibro = '$ID'";

if ($conn->query($ELIMINAR)) {
    header("Location: inventario.php");
    exit();
} else {
    echo "ERROR AL ELIMINAR: " . $conn->error;
}
?>
