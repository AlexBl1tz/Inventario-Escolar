<?php
include("conexion.php");
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// 🟢 Tomamos el ID del préstamo desde la URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 🧩 Consulta para eliminar el préstamo
    $sql = "DELETE FROM prestamo WHERE idPrestamo = '$id'";

    if ($conn->query($sql)) {
        // ✅ Regresa al inventario o a donde se muestran los préstamos
        header("Location: inventario.php");
        exit();
    } else {
        echo "❌ Error al eliminar el préstamo: " . $conn->error;
    }
} else {
    echo "⚠️ No se proporcionó el ID del préstamo.";
}
?>
