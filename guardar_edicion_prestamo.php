<?php
include("conexion.php");
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$id = $_POST['idPrestamo'];
$idLibro = $_POST['idLibro'];
$persona = $_POST['persona'];
$fecPrestamo = $_POST['fecPrestamo'];
$fecDevolucion = $_POST['fecDevolucion'];

$update = $conn->prepare("
    UPDATE prestamo 
    SET idLibro=?, persona=?, fecPrestamo=?, fecDevolucion=? 
    WHERE idPrestamo=?
");
$update->bind_param("isssi", $idLibro, $persona, $fecPrestamo, $fecDevolucion, $id);

if ($update->execute()) {
    header("Location: inventario.php");
    exit();
} else {
    echo "❌ Error al actualizar préstamo: " . $conn->error;
}
?>
