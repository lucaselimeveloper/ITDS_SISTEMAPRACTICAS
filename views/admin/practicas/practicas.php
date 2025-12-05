<?php
    include '../../../config/db.php';

// Verificar si se ha solicitado eliminar una práctica
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_practica'])) {
        $id_practica = $_POST['id_practica'];
        $delete_sql = "DELETE FROM practica_profesional WHERE id = '$id_practica'";

        if (mysqli_query($conexion, $delete_sql)) {
            echo "Práctica eliminada correctamente";
        } else {
            echo "Error al eliminar la práctica: " . mysqli_error($conexion);
        }

        mysqli_close($conexion);
        exit();
    }

    // Manejar la solicitud de generación de reporte
    if (isset($_POST['generar_reporte'])) {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="reporte_practicas.xls"');

        echo "<table border='1'>";
        echo "<tr>
                <th>Código Estudiante</th>
                <th>C.I.</th>
                <th>Nombre</th>
                <th>Carrera</th>
                <th>Empresa</th>
                <th>Tutor</th>
                <th>Área</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Estado</th>
                <th>Motivo Congelación</th>
              </tr>";

        // Obtener el filtro de estado si existe
        $filtro_estado = isset($_POST['filtro_estado']) ? $_POST['filtro_estado'] : '';
        $search_value = isset($_POST['search_value']) ? $_POST['search_value'] : '';

        $sql = "SELECT 
                    e.codigo AS codigo_estudiante,
                    e.ci AS ci_estudiante,
                    e.nombre_completo AS nombre_estudiante,
                    e.carrera AS carrera_estudiante,
                    c.razon_social AS empresa,
                    t.nombre_tutor AS tutor,
                    pp.area AS area_practica,
                    pp.fecha_inicio AS fecha_inicio,
                    pp.fecha_fin AS fecha_fin,
                    pp.estado AS estado,
                    pp.motivo_congelacion AS motivo_congelacion
                FROM practica_profesional pp
                INNER JOIN estudiantes e ON pp.estudiante_id = e.id_estudiante
                INNER JOIN convenios c ON pp.convenio_id = c.id
                INNER JOIN tutores t ON pp.tutor_id = t.id";

        // Aplicar filtros
        $where = [];
        if ($filtro_estado !== '') {
            if ($filtro_estado === 'en_progreso') {
                $where[] = "pp.estado = 'activa' AND pp.fecha_fin IS NULL";
            } elseif ($filtro_estado === 'finalizada') {
                $where[] = "pp.estado = 'finalizada'";
            } elseif ($filtro_estado === 'congelada') {
                $where[] = "pp.estado = 'congelada'";
            }
        }

        if (!empty($search_value)) {
            $search = mysqli_real_escape_string($conexion, $search_value);
            $where[] = "(e.codigo LIKE '%$search%' OR e.ci LIKE '%$search%' OR e.nombre_completo LIKE '%$search%' 
                        OR e.carrera LIKE '%$search%' OR c.razon_social LIKE '%$search%' OR t.nombre_tutor LIKE '%$search%')";
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $result = mysqli_query($conexion, $sql);

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['codigo_estudiante'] . "</td>";
            echo "<td>" . $row['ci_estudiante'] . "</td>";
            echo "<td>" . $row['nombre_estudiante'] . "</td>";
            echo "<td>" . $row['carrera_estudiante'] . "</td>";
            echo "<td>" . $row['empresa'] . "</td>";
            echo "<td>" . $row['tutor'] . "</td>";
            echo "<td>" . $row['area_practica'] . "</td>";
            echo "<td>" . $row['fecha_inicio'] . "</td>";
            echo "<td>" . ($row['fecha_fin'] ?? '-') . "</td>";
            echo "<td>" . ucfirst($row['estado']) . "</td>";
            echo "<td>" . ($row['motivo_congelacion'] ?? '-') . "</td>";
            echo "</tr>";
        }

        echo "</table>";
        exit();
    }
}

// Obtener el filtro de estado si existe
$filtro_estado = isset($_GET['estado']) ? $_GET['estado'] : '';

// Construir la consulta SQL base
$sql = "SELECT 
            pp.id AS id_practica,
            e.codigo AS codigo_estudiante,
            e.ci AS ci_estudiante,
            e.nombre_completo AS nombre_estudiante,
            e.carrera AS carrera_estudiante,
            c.razon_social AS empresa,
            t.nombre_tutor AS tutor,
            t.cargo AS cargo_tutor,
            pp.area AS area_practica,
            pp.fecha_inicio AS fecha_inicio,
            pp.fecha_fin AS fecha_fin,
            pp.estado AS estado,
            pp.motivo_congelacion AS motivo_congelacion
        FROM practica_profesional pp
        INNER JOIN estudiantes e ON pp.estudiante_id = e.id_estudiante
        INNER JOIN convenios c ON pp.convenio_id = c.id
        INNER JOIN tutores t ON pp.tutor_id = t.id";

// Aplicar filtro de estado si existe
if ($filtro_estado !== '') {
    if ($filtro_estado === 'en_progreso') {
        $sql .= " WHERE pp.estado = 'activa' AND pp.fecha_fin IS NULL";
    } elseif ($filtro_estado === 'finalizada') {
        $sql .= " WHERE pp.estado = 'finalizada'";
    } elseif ($filtro_estado === 'congelada') {
        $sql .= " WHERE pp.estado = 'congelada'";
    }
}

$result = mysqli_query($conexion, $sql);

if (!$result) {
    die("Error en la consulta: " . mysqli_error($conexion));
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Prácticas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <!-- Navbar -->
    <?php
        include '../../layouts/navbar.php';
    ?>

    <section class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-black">Lista de Prácticas</h2>
            <div class="flex space-x-4">
                <button id="editButton" class="bg-yellow-500 text-black px-4 py-2 rounded-lg font-bold hover:bg-yellow-600 transition duration-300" disabled>Editar</button>
                <button id="deleteButton" class="bg-red-500 text-white px-4 py-2 rounded-lg font-bold hover:bg-red-700 transition duration-300" disabled>Eliminar</button>
                <button id="finalizarButton" class="bg-blue-500 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700 transition duration-300" disabled>Finalizar</button>
                <button id="congelarButton" class="bg-purple-500 text-white px-4 py-2 rounded-lg font-bold hover:bg-purple-700 transition duration-300" disabled>Congelar</button>
                <button id="moreInfoButton" class="bg-green-200 text-black px-4 py-2 rounded-lg font-bold hover:bg-green-300 transition duration-300" disabled>Más info +</button>
                <button id="reportButton" class="bg-green-500 text-white px-4 py-2 rounded-lg font-bold hover:bg-green-700 transition duration-300">Reporte</button>
            </div>
        </div>

        <!-- Filtros -->
        <div class="flex flex-wrap justify-between items-center mb-6 gap-4">
            <div class="flex items-center space-x-4">
                <div>
                    <label for="filtroEstado" class="block text-sm font-medium text-gray-700 mb-1">Filtrar por estado:</label>
                    <select id="filtroEstado" class="border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-yellow-500 focus:outline-none" onchange="aplicarFiltro()">
                        <option value="">Todos</option>
                        <option value="en_progreso" <?= $filtro_estado === 'en_progreso' ? 'selected' : '' ?>>En progreso</option>
                        <option value="finalizada" <?= $filtro_estado === 'finalizada' ? 'selected' : '' ?>>Finalizada</option>
                        <option value="congelada" <?= $filtro_estado === 'congelada' ? 'selected' : '' ?>>Congelada</option>
                    </select>
                </div>
            </div>

            <div class="flex-1 max-w-md">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Buscar práctica:</label>
                <input type="text" id="search" class="border border-gray-300 rounded-lg p-2 w-full focus:ring-2 focus:ring-yellow-500 focus:outline-none" placeholder="Buscar..." oninput="filterTable()">
            </div>
        </div>

        <!-- Tabla de Prácticas -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <table class="table-auto w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-black text-white">
                        <th class="px-4 py-3 border">Código Estudiante</th>
                        <th class="px-4 py-3 border">C.I.</th>
                        <th class="px-4 py-3 border">Nombre</th>
                        <th class="px-4 py-3 border">Carrera</th>
                        <th class="px-4 py-3 border">Empresa</th>
                        <th class="px-4 py-3 border">Tutor</th>
                        <th class="px-4 py-3 border">Fecha de Inicio</th>
                        <th class="px-4 py-3 border">Fecha de Finalización</th>
                        <th class="px-4 py-3 border">Estado</th>
                    </tr>
                </thead>
                <tbody id="practicaTable" class="text-black">
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr class="hover:bg-yellow-100 transition duration-300 cursor-pointer" onclick="selectRow(this)" data-id="<?= $row['id_practica'] ?>" data-fecha-fin="<?= $row['fecha_fin'] ?>" data-nombre="<?= $row['nombre_estudiante'] ?>" data-carrera="<?= $row['carrera_estudiante'] ?>" data-empresa="<?= $row['empresa'] ?>" data-tutor="<?= $row['tutor'] ?>" data-cargo-tutor="<?= $row['cargo_tutor'] ?>" data-area="<?= $row['area_practica'] ?>" data-fecha-inicio="<?= $row['fecha_inicio'] ?>" data-estado="<?= $row['estado'] ?>" data-motivo-congelacion="<?= $row['motivo_congelacion'] ?>">
                            <td class="px-4 py-3 border text-center"><?= $row['codigo_estudiante'] ?></td>
                            <td class="px-4 py-3 border"><?= $row['ci_estudiante'] ?></td>
                            <td class="px-4 py-3 border"><?= $row['nombre_estudiante'] ?></td>
                            <td class="px-4 py-3 border"><?= $row['carrera_estudiante'] ?></td>
                            <td class="px-4 py-3 border"><?= $row['empresa'] ?></td>
                            <td class="px-4 py-3 border"><?= $row['tutor'] ?></td>
                            <td class="px-4 py-3 border"><?= $row['fecha_inicio'] ?></td>
                            <td class="px-4 py-3 border"><?= $row['fecha_fin'] ?? '-' ?></td>
                            <td class="px-4 py-3 border">
                                <?php
                                if ($row['estado'] === 'congelada') {
                                    echo '<span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs">Congelada</span>';
                                } elseif ($row['estado'] === 'finalizada') {
                                    echo '<span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Finalizada</span>';
                                } else {
                                    echo '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">En progreso</span>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Modal de Más Información -->
    <div id="infoModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-xl font-bold mb-4">Información de la Práctica</h2>
            <div id="modalContent" class="space-y-2">
                <p><strong>Nombre del Estudiante:</strong> <span id="modalNombre"></span></p>
                <p><strong>Carrera:</strong> <span id="modalCarrera"></span></p>
                <p><strong>Organización (Convenio):</strong> <span id="modalEmpresa"></span></p>
                <p><strong>Cargo del Tutor:</strong> <span id="modalCargoTutor"></span></p>
                <p><strong>Área de la Práctica:</strong> <span id="modalArea"></span></p>
                <p><strong>Periodo:</strong> <span id="modalPeriodo"></span></p>
                <p><strong>Estado:</strong> <span id="modalEstado"></span></p>
                <div id="motivoCongelacionContainer" class="hidden">
                    <p><strong>Motivo de congelación:</strong> <span id="modalMotivoCongelacion"></span></p>
                </div>
            </div>
            <div class="flex justify-end space-x-2 mt-4">
                <button onclick="printModal()" class="bg-blue-500 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700 transition duration-300">Imprimir</button>
                <button onclick="closeInfoModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg font-bold hover:bg-gray-700 transition duration-300">Cerrar</button>
            </div>
        </div>
    </div>

    <script>
        let selectedRow = null;
        let currentSearchValue = '';
        let currentEstadoFilter = '<?= $filtro_estado ?>';

        function aplicarFiltro() {
            currentEstadoFilter = document.getElementById('filtroEstado').value;
            window.location.href = `practicas.php?estado=${currentEstadoFilter}`;
        }

        function filterTable() {
            currentSearchValue = document.getElementById('search').value.toLowerCase();
            const rows = document.querySelectorAll('#practicaTable tr');

            rows.forEach(row => {
                let match = [...row.getElementsByTagName('td')].some(td => td.textContent.toLowerCase().includes(currentSearchValue));
                row.style.display = match ? '' : 'none';
            });
        }

        function selectRow(row) {
            if (selectedRow) selectedRow.classList.remove('bg-yellow-200');
            selectedRow = row;
            selectedRow.classList.add('bg-yellow-200');

            // Habilitar botones
            document.getElementById('editButton').disabled = false;
            document.getElementById('deleteButton').disabled = false;
            document.getElementById('finalizarButton').disabled = false;
            document.getElementById('congelarButton').disabled = false;
            document.getElementById('moreInfoButton').disabled = false;

            // Deshabilitar botones según estado
            const estado = selectedRow.getAttribute('data-estado');
            if (estado === 'finalizada' || estado === 'congelada') {
                document.getElementById('finalizarButton').disabled = true;
                document.getElementById('congelarButton').disabled = true;
            }
        }

        // Función para abrir el modal de más información
        document.getElementById('moreInfoButton').addEventListener('click', function() {
            if (!selectedRow) return;

            // Obtener los datos de la fila seleccionada
            const nombre = selectedRow.getAttribute('data-nombre');
            const carrera = selectedRow.getAttribute('data-carrera');
            const empresa = selectedRow.getAttribute('data-empresa');
            const cargoTutor = selectedRow.getAttribute('data-cargo-tutor');
            const area = selectedRow.getAttribute('data-area');
            const fechaInicio = selectedRow.getAttribute('data-fecha-inicio');
            const fechaFin = selectedRow.getAttribute('data-fecha-fin');
            const estado = selectedRow.getAttribute('data-estado');
            const motivoCongelacion = selectedRow.getAttribute('data-motivo-congelacion');

            // Mostrar los datos en el modal
            document.getElementById('modalNombre').textContent = nombre;
            document.getElementById('modalCarrera').textContent = carrera;
            document.getElementById('modalEmpresa').textContent = empresa;
            document.getElementById('modalCargoTutor').textContent = cargoTutor;
            document.getElementById('modalArea').textContent = area;
            document.getElementById('modalPeriodo').textContent = `${fechaInicio} - ${fechaFin || '-'}`;

            // Mostrar estado con formato
            let estadoText = '';
            if (estado === 'congelada') {
                estadoText = 'Congelada';
            } else if (estado === 'finalizada') {
                estadoText = 'Finalizada';
            } else {
                estadoText = 'En progreso';
            }
            document.getElementById('modalEstado').textContent = estadoText;

            // Mostrar motivo de congelación si existe
            const motivoContainer = document.getElementById('motivoCongelacionContainer');
            const motivoSpan = document.getElementById('modalMotivoCongelacion');

            if (estado === 'congelada' && motivoCongelacion) {
                motivoSpan.textContent = motivoCongelacion;
                motivoContainer.classList.remove('hidden');
            } else {
                motivoContainer.classList.add('hidden');
            }

            // Abrir el modal
            document.getElementById('infoModal').classList.remove('hidden');
        });

        // Función para cerrar el modal
        function closeInfoModal() {
            document.getElementById('infoModal').classList.add('hidden');
        }

        // Función para imprimir el contenido del modal
        function printModal() {
            const modalContent = document.getElementById('modalContent').innerHTML;
            const printWindow = window.open('', '', 'height=600,width=800');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Informe de Práctica Profesional</title>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                line-height: 1.5;
                                padding: 1.5cm;
                                font-size: 12pt;
                            }
                            .titulo-informe {
                                text-align: center;
                                font-weight: bold;
                                text-transform: uppercase;
                                margin-bottom: 1rem;
                            }
                            .subtitulo-informe {
                                text-align: center;
                                font-weight: bold;
                                text-transform: uppercase;
                                margin-bottom: 1rem;
                            }
                            .instrucciones {
                                font-style: italic;
                                text-align: center;
                                margin-bottom: 2rem;
                                font-size: 0.9rem;
                            }
                            .seccion-titulo {
                                font-weight: bold;
                                border-bottom: 1px solid #000;
                                margin-top: 1.5rem;
                                margin-bottom: 1rem;
                            }
                            .campo-dato {
                                display: inline-block;
                                min-width: 60%;
                                border-bottom: 1px solid #000;
                                padding-left: 0.5rem;
                            }
                            .campo-vacio-grande {
                                border: 1px solid #000;
                                min-height: 6rem;
                                margin-top: 0.5rem;
                                padding: 0.5rem;
                            }
                            .firma {
                                margin-top: 3rem;
                                width: 40%;
                                float: left;
                                text-align: center;
                            }
                            .linea-firma {
                                border-top: 1px solid #000;
                                width: 80%;
                                margin: 0 auto;
                                padding-top: 0.5rem;
                            }
                            @page {
                                size: A4;
                                margin: 1.5cm;
                            }
                        </style>
                    </head>
                    <body>
                        <h1 class="titulo-informe">INFORME</h1>
                        <h2 class="subtitulo-informe">PRÁCTICA PROFESIONAL</h2>
                        <p class="instrucciones">
                            El informe debe ser llenado por el estudiante y debe estar avalado por el responsable de la organización.<br>
                            A este informe se debe adjuntar el Certificado de Pasantía emitido por la organización.
                        </p>
                        
                        ${modalContent}
                        
                        <!-- Firmas -->
                        <div style="clear: both; padding-top: 3rem;">
                            <div class="firma">
                                <div class="linea-firma"></div>
                                <p>Firma Estudiante</p>
                            </div>
                            <div class="firma">
                                <div class="linea-firma"></div>
                                <p>VºBº Responsable/Organización Firma y Sello</p>
                            </div>
                        </div>
                    </body>
                </html>
            `);

            printWindow.document.close();
            printWindow.print();
        }

        // Función para eliminar práctica
        document.getElementById('deleteButton').addEventListener('click', function() {
            if (!selectedRow) return;
            const practicaId = selectedRow.getAttribute('data-id');

            if (confirm('¿Estás seguro de que deseas eliminar esta práctica?')) {
                fetch('', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `id_practica=${practicaId}`
                    })
                    .then(response => response.text())
                    .then(data => {
                        alert(data);
                        location.reload();
                    })
                    .catch(error => console.error('Error:', error));
            }
        });

        // Función para redirigir a la página de finalización
        document.getElementById('finalizarButton').addEventListener('click', function() {
            if (!selectedRow) return;
            const practicaId = selectedRow.getAttribute('data-id');
            window.location.href = `finalizar_practica.php?id_practica=${practicaId}`;
        });

        // Función para redirigir a la página de congelación
        document.getElementById('congelarButton').addEventListener('click', function() {
            if (!selectedRow) return;
            const practicaId = selectedRow.getAttribute('data-id');
            window.location.href = `congelar_practica.php?id_practica=${practicaId}`;
        });

        // Función para generar reporte
        document.getElementById('reportButton').addEventListener('click', function() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '';

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'generar_reporte';
            input.value = '1';
            form.appendChild(input);

            const searchInput = document.createElement('input');
            searchInput.type = 'hidden';
            searchInput.name = 'search_value';
            searchInput.value = currentSearchValue;
            form.appendChild(searchInput);

            const estadoInput = document.createElement('input');
            estadoInput.type = 'hidden';
            estadoInput.name = 'filtro_estado';
            estadoInput.value = currentEstadoFilter;
            form.appendChild(estadoInput);

            document.body.appendChild(form);
            form.submit();
        });
        // Función para redirigir a la página de edición
        document.getElementById('editButton').addEventListener('click', function() {
            if (!selectedRow) return;

            const practicaId = selectedRow.getAttribute('data-id');
            const estado = selectedRow.getAttribute('data-estado');

            // Verificar que la práctica está en estado activa
            if (estado !== 'activa') {
                alert('Solo se pueden editar prácticas en estado "En progreso"');
                return;
            }

            window.location.href = `editar_practica.php?id=${practicaId}`;
        });
    </script>

    <!-- Footer -->
    <?php 
        include '../../layouts/footer.php'; 
    ?>

</body>

</html>

<?php mysqli_close($conexion); ?>