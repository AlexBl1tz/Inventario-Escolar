<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: usuario.php");
    exit();
}

$USUARIO = $_SESSION['usuario'];

// Por defecto, mostramos libros
$vista = $_GET['vista'] ?? 'libros';

// Consultas según vista
if ($vista === 'prestamos') {
    $QUERY = "SELECT p.idPrestamo, l.titulo, pe.nombre AS persona, p.fecPrestamo, p.fecDevolucion 
              FROM prestamo p
              JOIN libro l ON p.idLibro = l.idLibro
              JOIN persona pe ON p.idPersona = pe.idPersona
              ORDER BY p.idPrestamo DESC";
} else {
    $QUERY = "SELECT * FROM libro ORDER BY idLibro DESC";
}
$RESULTADO = $conn->query($QUERY);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>📚 Inventario de Libros</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="header">
    <div class="header-info">
        <h1>📚 Inventario Escolar</h1>
        <p>Bienvenido: <strong><?php echo htmlspecialchars($USUARIO); ?></strong></p>
    </div>
    <button class="logouta-btn"> 
        <a href="logout.php" class="logouta-btn">Cerrar Sesión</a> 
    </button>
</header>

<main>
    <section class="panel">
        <div class="botones-superior">
            <button id="agregarLibroBtn" class="btn-agregar">➕ Agregar Libro</button>
            <button id="agregarPrestamoBtn" class="btn-prestamo">📖 Registrar Préstamo</button>
        </div>

        <div class="buscador-container">
            <input type="text" id="buscador" placeholder="🔍 Buscar...">
        </div>

        <!-- Formulario Agregar Libro -->
        <div class="form-popup" id="formLibro">
            <form action="guardar_libro.php" method="POST" enctype="multipart/form-data" class="formulario">
                <h2>Agregar Libro</h2>
                <input type="text" name="TITULO" placeholder="Título" required>
                <input type="text" name="AUTOR" placeholder="Autor" required>
                <input type="number" name="CANTIDAD" placeholder="Cantidad" required>
                <select name="ESTADO">
                    <option value="">-- Estado --</option>
                    <option value="Disponible">Disponible</option>
                    <option value="Prestado">Prestado</option>
                    <option value="Dañado">Dañado</option>
                </select>
                <button type="submit" class="btn-guardar">💾 Guardar</button>
                <button type="button" class="btn-cerrar" onclick="cerrarForm()">❌ Cancelar</button>
            </form>
        </div>

        <!-- Formulario Agregar Préstamo -->
        <div class="form-popup" id="formPrestamo">
            <form action="guardar_prestamo.php" method="POST" class="formulario">
                <h2>Registrar Préstamo</h2>
                <select name="idLibro" required>
                    <option value="">-- Selecciona Libro --</option>
                    <?php
                    $libros = $conn->query("SELECT idLibro, titulo FROM libro WHERE estado <> 'Prestado'");
                    while ($lib = $libros->fetch_assoc()) {
                        echo "<option value='{$lib['idLibro']}'>{$lib['titulo']}</option>";
                    }
                    ?>
                </select>
                <select name="idPersona" required>
                    <option value="">-- Selecciona Persona --</option>
                    <?php
                    $personas = $conn->query("SELECT idPersona, nombre FROM persona");
                    while ($per = $personas->fetch_assoc()) {
                        echo "<option value='{$per['idPersona']}'>{$per['nombre']}</option>";
                    }
                    ?>
                </select>
                <label>Fecha de Préstamo</label>
                <input type="date" name="fecPrestamo" required>
                <label>Fecha de Devolución</label>
                <input type="date" name="fecDevolucion" required>
                <button type="submit" class="btn-guardar">💾 Guardar</button>
                <button type="button" class="btn-cerrar" onclick="cerrarForm()">❌ Cancelar</button>
            </form>
        </div>
    </section>

    <section class="tabla">
        <h2><?php echo $vista === 'prestamos' ? "📖 Lista de Préstamos" : "📚 Lista de Libros"; ?></h2>
        <div class="cambiar-vista">
            <a href="libro.php?vista=libros" class="btn cambiar">📚 Ver Libros</a>
            <a href="libro.php?vista=prestamos" class="btn cambiar">📖 Ver Préstamos</a>
        </div>
        <table>
            <thead>
                <?php if ($vista === 'prestamos') { ?>
                    <tr>
                        <th>ID</th>
                        <th>Libro</th>
                        <th>Persona</th>
                        <th>Fecha Préstamo</th>
                        <th>Fecha Devolución</th>
                    </tr>
                <?php } else { ?>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Cantidad</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                <?php } ?>
            </thead>
            <tbody>
                <?php
                if ($RESULTADO->num_rows > 0) {
                    while ($ROW = $RESULTADO->fetch_assoc()) {
                        if ($vista === 'prestamos') {
                            echo "<tr>
                                <td>{$ROW['idPrestamo']}</td>
                                <td>{$ROW['titulo']}</td>
                                <td>{$ROW['persona']}</td>
                                <td>{$ROW['fecPrestamo']}</td>
                                <td>{$ROW['fecDevolucion']}</td>
                            </tr>";
                        } else {
                            echo "<tr>
                                <td>{$ROW['idLibro']}</td>
                                <td>{$ROW['titulo']}</td>
                                <td>{$ROW['autor']}</td>
                                <td>{$ROW['cantidad']}</td>
                                <td>{$ROW['estado']}</td>
                                <td>
                                    <a href='editar_libro.php?id={$ROW['idLibro']}'>✏️</a>
                                    <a href='eliminar_libro.php?id={$ROW['idLibro']}' onclick=\"return confirm('¿Eliminar libro?')\">🗑️</a>
                                </td>
                            </tr>";
                        }
                    }
                } else {
                    echo "<tr><td colspan='6'>No hay registros</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </section>
</main>

<script>
const formLibro = document.getElementById("formLibro");
const formPrestamo = document.getElementById("formPrestamo");

document.getElementById("agregarLibroBtn").onclick = () => {
    formLibro.style.display = "flex";
    formPrestamo.style.display = "none";
};
document.getElementById("agregarPrestamoBtn").onclick = () => {
    formPrestamo.style.display = "flex";
    formLibro.style.display = "none";
};

function cerrarForm() {
    formLibro.style.display = "none";
    formPrestamo.style.display = "none";
}

// 🔍 Buscador
document.getElementById("buscador").addEventListener("keyup", function() {
    let input = this.value.toLowerCase();
    let filas = document.querySelectorAll("table tbody tr");

    filas.forEach(fila => {
        let texto = fila.textContent.toLowerCase();
        fila.style.display = texto.includes(input) ? "" : "none";
    });
});
</script>
</body>
</html>
