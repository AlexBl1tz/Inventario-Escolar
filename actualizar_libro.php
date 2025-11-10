<?php 
include("conexion.php");
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// 🔹 Recoger datos del formulario
$idLibro   = $_POST['idLibro'];
$TITULO    = $_POST['TITULO'];
$AUTOR     = $_POST['AUTOR'];
$EDITORIAL = $_POST['EDITORIAL'];
$GENERO    = $_POST['GENERO'];
$SALON     = $_POST['SALON'];
$ANIO      = $_POST['ANIO'];
$CANTIDAD  = $_POST['CANTIDAD'];
$ESTADO    = $_POST['ESTADO'];

// 📂 Manejo del archivo PDF (opcional)
$PDF = "";
if (isset($_FILES['PDF']) && $_FILES['PDF']['error'] === 0) {
    $nombreArchivo = time() . "_" . basename($_FILES['PDF']['name']);
    $rutaDestino = "uploads/" . $nombreArchivo;

    if (move_uploaded_file($_FILES['PDF']['tmp_name'], $rutaDestino)) {
        $PDF = $nombreArchivo;
    }
}

// 🔧 Consulta SQL — si se subió PDF, lo actualiza también
if (!empty($PDF)) {
    $sql = "UPDATE libro 
            SET TITULO='$TITULO', AUTOR='$AUTOR', EDITORIAL='$EDITORIAL', GENERO='$GENERO',
                SALON='$SALON', ANIO='$ANIO', CANTIDAD='$CANTIDAD', ESTADO='$ESTADO', PDF='$PDF'
            WHERE idLibro='$idLibro'";
} else {
    $sql = "UPDATE libro 
            SET TITULO='$TITULO', AUTOR='$AUTOR', EDITORIAL='$EDITORIAL', GENERO='$GENERO',
                SALON='$SALON', ANIO='$ANIO', CANTIDAD='$CANTIDAD', ESTADO='$ESTADO'
            WHERE idLibro='$idLibro'";
}

// 💾 Ejecutar y verificar
if ($conn->query($sql)) {
    header("Location: inventario.php");
    exit();
} else {
    echo "❌ ERROR al actualizar el libro: " . $conn->error;
}
?>
