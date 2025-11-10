<?php
ob_start();
include("conexion.php");
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idLibro = $_POST['idLibro'] ?? '';
    $persona = $_POST['persona'] ?? ''; // <-- nombre de quien recibe
    $fecPrestamo = $_POST['fecPrestamo'] ?? '';
    $fecDevolucion = $_POST['fecDevolucion'] ?? '';
    $usuario = $_SESSION['usuario'];
    $CREADOR = $_SESSION['usuario'];

    if (empty($idLibro) || empty($persona) || empty($fecPrestamo) || empty($fecDevolucion)) {
        $_SESSION['error'] = "⚠️ Todos los campos son obligatorios.";
        header("Location: inventario.php?mensaje=error_campos");
        exit();
    }

    // 💾 Insertar préstamo con el nombre de la persona
    $sql = "INSERT INTO prestamo (idLibro, persona, usuario, CREADOR, fecPrestamo, fecDevolucion)
            VALUES ('$idLibro', '$persona', '$usuario', '$CREADOR', '$fecPrestamo', '$fecDevolucion')";

    if ($conn->query($sql)) {
        $conn->query("UPDATE libro SET ESTADO='PRESTADO' WHERE idLibro='$idLibro'");
        header("Location: inventario.php?mensaje=prestamo_ok");
        exit();
    } else {
        $_SESSION['error'] = "❌ Error al guardar el préstamo: " . $conn->error;
        header("Location: inventario.php?mensaje=error_sql");
        exit();
    }
} else {
    header("Location: inventario.php?mensaje=sin_datos");
    exit();
}

ob_end_flush();
?>
