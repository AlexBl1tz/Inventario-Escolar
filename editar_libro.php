<?php
include("conexion.php");
session_start();

// 🧠 Verificar sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// 📚 Obtener el ID del libro
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("❌ No se especificó un libro para editar.");
}

$ID = $_GET['id'];

// 🔍 Consultar el libro
$CONSULTA = "SELECT * FROM libro WHERE idLibro = '$ID'";
$RESULTADO = $conn->query($CONSULTA);
$LIBRO = $RESULTADO->fetch_assoc();

if (!$LIBRO) {
    die("❌ Libro no encontrado en la base de datos.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>✏️ Editar Libro</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f3f4f6;
            color: #333;
            text-align: center;
            margin: 0;
            padding: 40px;
        }
        form {
            background: #fff;
            display: inline-block;
            padding: 30px 40px;
            border-radius: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }
        input, select, button {
            display: block;
            width: 320px;
            margin: 12px auto;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-family: 'Poppins';
            font-size: 15px;
        }
        h1 {
            color: #3c47d8;
            margin-bottom: 20px;
        }
        button {
            background-color: #3c47d8;
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: 0.3s;
        }
        button:hover {
            background-color: #2a33a8;
        }
        a {
            display: inline-block;
            margin-top: 15px;
            color: #e74c3c;
            text-decoration: none;
            font-weight: 500;
        }
        a:hover {
            text-decoration: underline;
        }
        label {
            display: block;
            text-align: left;
            margin-left: 10px;
            font-weight: 600;
            color: #444;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>✏️ Editar Libro</h1>

    <form action="actualizar_libro.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="idLibro" value="<?php echo $LIBRO['idLibro']; ?>">

        <label>Título</label>
        <input type="text" name="TITULO" value="<?php echo $LIBRO['TITULO']; ?>" required>

        <label>Autor</label>
        <input type="text" name="AUTOR" value="<?php echo $LIBRO['AUTOR']; ?>" required>

        <label>Editorial</label>
        <input type="text" name="EDITORIAL" value="<?php echo $LIBRO['EDITORIAL']; ?>">

        <label>Género</label>
        <input type="text" name="GENERO" value="<?php echo $LIBRO['GENERO']; ?>">

        <label>Salón</label>
        <select name="SALON" required>
            <option value="">-- Seleccionar salón --</option>
            <?php
            $salones = ["1°", "2°", "3°", "4°", "5°", "6°", "Dirección"];
            foreach ($salones as $salon) {
                $sel = ($LIBRO['SALON'] == $salon) ? "selected" : "";
                echo "<option value='$salon' $sel>$salon</option>";
            }
            ?>
        </select>

        <label>Año</label>
        <select name="ANIO" id="selectAnio" required>
            <option value="">-- Seleccionar año --</option>
        </select>

        <label>Cantidad</label>
        <input type="number" name="CANTIDAD" value="<?php echo $LIBRO['CANTIDAD']; ?>" required>

        <label>Estado</label>
        <select name="ESTADO" required>
            <option value="">-- Seleccionar estado --</option>
            <option value="DISPONIBLE" <?php if ($LIBRO['ESTADO'] == 'DISPONIBLE') echo 'selected'; ?>>DISPONIBLE</option>
            <option value="PRESTADO" <?php if ($LIBRO['ESTADO'] == 'PRESTADO') echo 'selected'; ?>>PRESTADO</option>
            <option value="DAÑADO" <?php if ($LIBRO['ESTADO'] == 'DAÑADO') echo 'selected'; ?>>DAÑADO</option>
            <option value="GUARDADO" <?php if ($LIBRO['ESTADO'] == 'GUARDADO') echo 'selected'; ?>>GUARDADO</option>
        </select>

        <label>📄 Subir nuevo PDF (opcional)</label>
        <input type="file" name="PDF" accept=".pdf">

        <?php if (!empty($LIBRO['PDF'])) { ?>
            <p>📘 Archivo actual: 
                <a href="uploads/<?php echo $LIBRO['PDF']; ?>" target="_blank">
                    <?php echo $LIBRO['PDF']; ?>
                </a>
            </p>
        <?php } ?>

        <button type="submit">💾 Guardar Cambios</button>
    </form>

    <a href="inventario.php">⬅️ Volver al inventario</a>

    <script>
        // 🗓️ Generar lista de años automáticamente
        const currentYear = new Date().getFullYear();
        const selectAnio = document.getElementById("selectAnio");
        const anioActual = "<?php echo $LIBRO['ANIO']; ?>";

        for (let y = currentYear; y >= 2010; y--) {
            const opt = document.createElement("option");
            opt.value = y;
            opt.textContent = y;
            if (y == anioActual) opt.selected = true;
            selectAnio.appendChild(opt);
        }
    </script>
</body>
</html>
