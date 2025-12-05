<?php
    include '../../../config/db.php'; // Asegúrate de que la ruta sea correcta

// Manejar la solicitud de generación de reporte
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generar_reporte'])) {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="reporte_estudiantes.xls"');
    
    echo "<table border='1'>";
    echo "<tr>
            <th>Código</th>
            <th>Nombre</th>
            <th>Carrera</th>
            <th>Correo</th>
            <th>Teléfono</th>
            <th>Fecha de Registro</th>
          </tr>";
    
    $sql = "SELECT codigo, nombre_completo, carrera, correo, celular, fecha_solicitud FROM estudiantes";
    if (isset($_POST['search_value']) && !empty($_POST['search_value'])) {
        $search = mysqli_real_escape_string($conexion, $_POST['search_value']);
        $sql .= " WHERE codigo LIKE '%$search%' OR nombre_completo LIKE '%$search%' OR carrera LIKE '%$search%' OR correo LIKE '%$search%' OR celular LIKE '%$search%'";
    }
    
    $result = mysqli_query($conexion, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>".$row['codigo']."</td>";
        echo "<td>".$row['nombre_completo']."</td>";
        echo "<td>".$row['carrera']."</td>";
        echo "<td>".$row['correo']."</td>";
        echo "<td>".$row['celular']."</td>";
        echo "<td>".$row['fecha_solicitud']."</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    exit();
}

// Realizar la consulta a la base de datos (solo lectura)
$sql = "SELECT id_estudiante, codigo, nombre_completo, carrera, correo, celular, fecha_solicitud FROM estudiantes";
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
    <title>Gestionar Estudiantes</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <!-- Navbar para usuarios -->
    <?php include '../ProyectoPracticas/resources/navbar_user.php'; ?>

    <section class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-black">Buscar estudiante</h2>
            <div class="flex space-x-4">
                <button id="assignButton" class="bg-blue-500 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700 transition duration-300" disabled>Asignar</button>
                <button id="reportButton" class="bg-green-500 text-white px-4 py-2 rounded-lg font-bold hover:bg-green-700 transition duration-300">Reporte</button>
            </div>
        </div>

        <!-- Buscador -->
        <div class="flex justify-between items-center mb-6">
            <input type="text" id="search" class="border border-gray-300 rounded-lg p-2 w-full max-w-md focus:ring-2 focus:ring-yellow-500 focus:outline-none" placeholder="Buscar estudiante..." oninput="filterTable()">
        </div>

        <!-- Tabla de Estudiantes -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <table class="table-auto w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-black text-white">
                        <th class="px-4 py-3 border">Código</th>
                        <th class="px-4 py-3 border">Nombre</th>
                        <th class="px-4 py-3 border">Carrera</th>
                        <th class="px-4 py-3 border">Correo</th>
                        <th class="px-4 py-3 border">Teléfono</th>
                        <th class="px-4 py-3 border">Fecha de Registro</th>
                    </tr>
                </thead>
                <tbody id="studentTable" class="text-black">
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr class="hover:bg-yellow-100 transition duration-300 cursor-pointer" onclick="selectRow(this)" data-id="<?= $row['id_estudiante'] ?>">
                            <td class="px-4 py-3 border text-center"><?= $row['codigo'] ?></td>
                            <td class="px-4 py-3 border"><?= $row['nombre_completo'] ?></td>
                            <td class="px-4 py-3 border"><?= $row['carrera'] ?></td>
                            <td class="px-4 py-3 border"><?= $row['correo'] ?></td>
                            <td class="px-4 py-3 border"><?= $row['celular'] ?></td>
                            <td class="px-4 py-3 border"><?= $row['fecha_solicitud'] ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </section>

    <script>
        let selectedRow = null;
        let currentSearchValue = '';

        function filterTable() {
            currentSearchValue = document.getElementById('search').value.toLowerCase();
            const rows = document.querySelectorAll('#studentTable tr');

            rows.forEach(row => {
                let match = [...row.getElementsByTagName('td')].some(td => td.textContent.toLowerCase().includes(currentSearchValue));
                row.style.display = match ? '' : 'none';
            });
        }

        function selectRow(row) {
            if (selectedRow) selectedRow.classList.remove('bg-yellow-200');
            selectedRow = row;
            selectedRow.classList.add('bg-yellow-200');

            document.getElementById('assignButton').disabled = false;
        }

        // Función para redirigir a asignar_user.php
        document.getElementById('assignButton').addEventListener('click', function () {
            if (!selectedRow) return;

            // Obtener el ID del estudiante de la fila seleccionada
            const studentId = selectedRow.getAttribute('data-id');

            // Redirigir a asignar_user.php con el ID del estudiante como parámetro
            window.location.href = `asignar_users.php?id_estudiante=${studentId}`;
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
            
            document.body.appendChild(form);
            form.submit();
        });
    </script>

    <?php include '../ProyectoPracticas/resources/footer.php'; ?>

</body>
</html>

<?php mysqli_close($conexion); ?>