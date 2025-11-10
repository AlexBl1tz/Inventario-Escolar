<?php  
include("conexion.php");
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$USUARIO = $_SESSION['usuario'];

$libros = $conn->query("
    SELECT 
        l.*, 
        (SELECT p.persona 
         FROM prestamo p 
         WHERE p.idLibro = l.idLibro 
         ORDER BY p.idPrestamo DESC 
         LIMIT 1) AS prestado_a
    FROM libro l
    ORDER BY l.idLibro ASC
");

$prestamos = $conn->query("SELECT * FROM prestamo ORDER BY idPrestamo DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/style-inv.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <title>Inventario Biblioteca</title>
    <style>
        body { font-family: 'Poppins', sans-serif; background:#fafafa; }
        .btns-container { text-align: center; margin: 20px 0; }
        .btns-container button {
            background-color: #5563DE;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 10px;
            cursor: pointer;
            margin: 5px;
            font-weight: bold;
            transition: 0.3s;
        }
        .btns-container button:hover { background-color: #3846B2; }
        .seccion { display: none; animation: fade 0.3s ease-in-out; }
        .seccion.active { display: block; }
        @keyframes fade { from {opacity: 0;} to {opacity: 1;} }

        table { width: 95%; margin: 0 auto; border-collapse: collapse; background:white; border-radius:10px; overflow:hidden; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #5563DE; color: white; }
        td a { text-decoration: none; font-size: 18px; }
        td a:hover { opacity: 0.7; }

        a.pdf-link { color: #2ecc71; text-decoration: none; font-weight: bold; }
        a.pdf-link:hover { text-decoration: underline; }

        /* Filtros */
        .filtro-container {
            text-align: center;
            margin: 20px auto;
            background: #f6f7ff;
            padding: 15px;
            border-radius: 15px;
            width: 90%;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }
        .filtro-container input, 
        .filtro-container select {
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-family: 'Poppins', sans-serif;
            margin: 5px;
        }
        .btn-limpiar {
            background-color: #3846B2;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
            margin-left: 10px;
            transition: 0.3s;
        }
        .btn-limpiar:hover {
            background-color: #2a3499;
        }
        .header {
            text-align:center;
            background:#5563DE;
            color:white;
            padding:10px;
            border-radius:0 0 15px 15px;
        }
        .logout-btn {
            background:#ff4d4d;
            color:white;
            padding:8px 12px;
            border-radius:8px;
            text-decoration:none;
            font-weight:bold;
            float:right;
            margin-top:-40px;
            margin-right:20px;
        }
        .logout-btn:hover { background:#cc0000; }
    </style>
</head>
<body>
    <div class="header">
        <h1>📚 SISTEMA DE INVENTARIO</h1>
        <p class="mover2">USUARIO: <strong><?php echo $USUARIO; ?></strong></p>
        <a href="logout.php" class="logout-btn">CERRAR SESIÓN</a>
    </div>

    <div class="btns-container buton39">
        <button onclick="mostrarSeccion('libros')">📘 Agregar Libro</button>
        <button onclick="mostrarSeccion('prestamos')">📖 Registrar Préstamo</button>
    </div>

    <!-- SECCIÓN LIBROS -->
    <div id="libros" class="seccion active">
        <div class="formulario">
            <h2>➕ AGREGAR LIBRO</h2>
            <form action="guardar_libro.php" method="POST" enctype="multipart/form-data">
                <input type="text" name="TITULO" placeholder="TÍTULO" required>
                <input type="text" name="AUTOR" placeholder="AUTOR" required>
                <input type="text" name="EDITORIAL" placeholder="EDITORIAL">
                <select name="GENERO" required>
                    <option value="">-- SELECCIONAR GÉNERO --</option>
                    <option value="Terror">Terror</option>
                    <option value="Comedia">Comedia</option>
                    <option value="Acción">Acción</option>
                    <option value="Magia">Magia</option>
                    <option value="Romance">Romance</option>
                    <option value="Aventura">Aventura</option>
                </select>
                <select name="SALON" required>
                    <option value="">-- SELECCIONAR SALÓN --</option>
                    <option value="1°">1°</option>
                    <option value="2°">2°</option>
                    <option value="3°">3°</option>
                    <option value="4°">4°</option>
                    <option value="5°">5°</option>
                    <option value="6°">6°</option>
                    <option value="Dirección">Dirección</option>
                </select>
                <select name="ANIO" id="selectAnio" required></select>
                <input type="number" name="CANTIDAD" placeholder="CANTIDAD" required>
                <select name="ESTADO" required>
                    <option value="">-- SELECCIONAR ESTADO --</option>
                    <option value="DISPONIBLE">DISPONIBLE</option>
                    <option value="PRESTADO">PRESTADO</option>
                    <option value="DAÑADO">DAÑADO</option>
                    <option value="GUARDADO">GUARDADO</option>
                </select>
                <input type="file" name="PDF">
                <button type="submit" class="e232">GUARDAR LIBRO</button>
            </form>
        </div>

        <h2>📋 LISTA DE LIBROS</h2>

        <div class="filtro-container">
            <input type="text" id="filtroTitulo" placeholder="🔍 Buscar por título..." style="width:25%;">
            <input type="text" id="filtroAutor" placeholder="✍️ Autor" style="width:20%;">
            <input type="text" id="filtroEditorial" placeholder="🏢 Editorial" style="width:20%;">
            <select id="filtroGenero">
                <option value="">🎭 Género</option>
                <option value="Terror">Terror</option>
                <option value="Comedia">Comedia</option>
                <option value="Acción">Acción</option>
                <option value="Magia">Magia</option>
                <option value="Romance">Romance</option>
                <option value="Aventura">Aventura</option>
            </select>
            <select id="filtroEstado">
                <option value="">📦 Estado</option>
                <option value="DISPONIBLE">DISPONIBLE</option>
                <option value="PRESTADO">PRESTADO</option>
                <option value="DAÑADO">DAÑADO</option>
                <option value="GUARDADO">GUARDADO</option>
            </select>
            <button type="button" class="btn-limpiar" onclick="limpiarFiltros()">🧹 Limpiar filtros</button>
        </div>

        <table id="tablaLibros">
            <tr>
                <th>ID</th><th>TÍTULO</th><th>AUTOR</th><th>EDITORIAL</th>
                <th>GÉNERO</th><th>SALÓN</th><th>AÑO</th><th>CANTIDAD</th>
                <th>ESTADO</th><th>PRESTADO A</th><th>PDF</th><th>CREADOR</th><th>ACCIONES</th>
            </tr>
            <?php while ($row = $libros->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['idLibro'] ?></td>
                <td><?= htmlspecialchars($row['TITULO']) ?></td>
                <td><?= htmlspecialchars($row['AUTOR']) ?></td>
                <td><?= htmlspecialchars($row['EDITORIAL']) ?></td>
                <td><?= htmlspecialchars($row['GENERO']) ?></td>
                <td><?= htmlspecialchars($row['SALON']) ?></td>
                <td><?= htmlspecialchars($row['ANIO']) ?></td>
                <td><?= htmlspecialchars($row['CANTIDAD']) ?></td>
                <td><?= htmlspecialchars($row['ESTADO']) ?></td>
                <td><?= $row['ESTADO'] == 'PRESTADO' ? htmlspecialchars($row['prestado_a'] ?: '—') : '—' ?></td>
                <td>
                    <?php if (!empty($row['PDF'])) { ?>
                        <a class="pdf-link" href="uploads/<?= $row['PDF'] ?>" target="_blank">📄 Ver</a>
                    <?php } else { echo "—"; } ?>
                </td>
                <td><?= htmlspecialchars($row['CREADOR'] ?? '—') ?></td>
                <td>
                    <a href="editar_libro.php?id=<?= $row['idLibro'] ?>">✏️</a> |
                    <a href="eliminar_libro.php?id=<?= $row['idLibro'] ?>" onclick="return confirm('¿Eliminar este libro?')">🗑️</a>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>

    <!-- SECCIÓN PRÉSTAMOS -->
    <div id="prestamos" class="seccion">
        <div class="formulario">
            <h2>📖 REGISTRAR PRÉSTAMO</h2>
            <form action="guardar_prestamo.php" method="POST">
                <input type="number" name="idLibro" placeholder="📘 ID del Libro" required>
                <input type="text" name="persona" placeholder="👤 Persona que recibe el libro" required>
                <input type="date" name="fecPrestamo" required>
                <input type="date" name="fecDevolucion" required>
                <button type="submit" style="background:#5563DE;color:#fff;border:none;padding:10px 15px;border-radius:10px;font-weight:bold;cursor:pointer;">💾 Guardar Préstamo</button>
            </form>
        </div>

        <h2>📋 LISTA DE PRÉSTAMOS</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>ID LIBRO</th>
                <th>PERSONA</th>
                <th>USUARIO</th>
                <th>FECHA PRÉSTAMO</th>
                <th>FECHA DEVOLUCIÓN</th>
                <th>ACCIONES</th>
            </tr>
            <?php while ($p = $prestamos->fetch_assoc()) { ?>
            <tr>
                <td><?= $p['idPrestamo'] ?></td>
                <td><?= $p['idLibro'] ?></td>
                <td><?= htmlspecialchars($p['persona']) ?></td>
                <td><?= htmlspecialchars($p['usuario']) ?></td>
                <td><?= htmlspecialchars($p['fecPrestamo']) ?></td>
                <td><?= htmlspecialchars($p['fecDevolucion']) ?></td>
                <td>
                    <a href="editar_prestamo.php?id=<?= $p['idPrestamo'] ?>">✏️</a> |
                    <a href="eliminar_prestamo.php?id=<?= $p['idPrestamo'] ?>" onclick="return confirm('¿Eliminar este préstamo?')">🗑️</a>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
 <br>
 <br>
 <br>
    <script>
        function mostrarSeccion(id) {
            document.querySelectorAll('.seccion').forEach(s => s.classList.remove('active'));
            document.getElementById(id).classList.add('active');
        }

        const currentYear = new Date().getFullYear();
        const selectAnio = document.getElementById("selectAnio");
        for (let y = currentYear; y >= 2010; y--) {
            const opt = document.createElement("option");
            opt.value = y; opt.textContent = y;
            selectAnio.appendChild(opt);
        }

        const inputsFiltro = {
            titulo: document.getElementById("filtroTitulo"),
            autor: document.getElementById("filtroAutor"),
            editorial: document.getElementById("filtroEditorial"),
            genero: document.getElementById("filtroGenero"),
            estado: document.getElementById("filtroEstado"),
        };
        const tablaLibros = document.getElementById("tablaLibros");

        Object.values(inputsFiltro).forEach(input => input.addEventListener("input", aplicarFiltros));

        function aplicarFiltros() {
            const filas = tablaLibros.querySelectorAll("tr:not(:first-child)");
            const filtros = {
                titulo: inputsFiltro.titulo.value.toLowerCase(),
                autor: inputsFiltro.autor.value.toLowerCase(),
                editorial: inputsFiltro.editorial.value.toLowerCase(),
                genero: inputsFiltro.genero.value.toLowerCase(),
                estado: inputsFiltro.estado.value.toLowerCase(),
            };
            filas.forEach(fila => {
                const celdas = fila.getElementsByTagName("td");
                const datos = {
                    titulo: celdas[1].textContent.toLowerCase(),
                    autor: celdas[2].textContent.toLowerCase(),
                    editorial: celdas[3].textContent.toLowerCase(),
                    genero: celdas[4].textContent.toLowerCase(),
                    estado: celdas[8].textContent.toLowerCase(),
                };
                const coincide = Object.keys(filtros).every(
                    key => !filtros[key] || datos[key].includes(filtros[key])
                );
                fila.style.display = coincide ? "" : "none";
            });
        }

        function limpiarFiltros() {
            Object.values(inputsFiltro).forEach(input => input.value = "");
            aplicarFiltros();
        }
    </script>
</body>
</html>
