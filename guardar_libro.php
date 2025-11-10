<?php 
include("conexion.php");
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$CREADOR = $_SESSION['usuario'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 📦 Recibir datos del formulario
    $TITULO = $_POST['TITULO'] ?? '';
    $AUTOR = $_POST['AUTOR'] ?? '';
    $EDITORIAL = $_POST['EDITORIAL'] ?? '';
    $GENERO = $_POST['GENERO'] ?? '';
    $SALON = $_POST['SALON'] ?? '';
    $ANIO = $_POST['ANIO'] ?? '';
    $CANTIDAD = $_POST['CANTIDAD'] ?? '';
    $ESTADO = $_POST['ESTADO'] ?? '';
    $PDF = '';

    // 📁 Subida del archivo PDF (opcional)
    if (!empty($_FILES['PDF']['name'])) {
        $CARPETA = "uploads/";
        if (!file_exists($CARPETA)) {
            mkdir($CARPETA, 0777, true);
        }

        $nombrePDF = time() . "_" . basename($_FILES['PDF']['name']);
        $DESTINO = $CARPETA . $nombrePDF;

        if (move_uploaded_file($_FILES['PDF']['tmp_name'], $DESTINO)) {
            $PDF = $nombrePDF;
        }
    }

    // 💾 Insertar en base de datos
    $SQL = "INSERT INTO LIBRO (TITULO, AUTOR, EDITORIAL, GENERO, SALON, ANIO, CANTIDAD, ESTADO, PDF, CREADOR)
            VALUES ('$TITULO', '$AUTOR', '$EDITORIAL', '$GENERO', '$SALON', '$ANIO', '$CANTIDAD', '$ESTADO', '$PDF', '$CREADOR')";

    if ($conn->query($SQL) === TRUE) {
        header("Location: inventario.php");
        exit();
    } else {
        echo "❌ Error al guardar: " . $conn->error;
    }

} else {
    echo "⚠️ No se recibieron datos del formulario.";
}
?>
