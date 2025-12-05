<?php
include '../../../config/db.php';
// Verificar si se ha solicitado eliminar un estudiante
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_estudiante'])) {
        $id_estudiante = $_POST['id_estudiante'];
        $delete_sql = "DELETE FROM estudiantes WHERE id_estudiante = '$id_estudiante'";

        if (mysqli_query($conexion, $delete_sql)) {
            echo "Estudiante eliminado correctamente";
        } else {
            echo "Error al eliminar el estudiante: " . mysqli_error($conexion);
        }

        mysqli_close($conexion);
        exit();
    }
    
    // Manejar la solicitud de generación de reporte
    if (isset($_POST['generar_reporte'])) {
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
}

// Consulta inicial para mostrar la tabla
$sql = "SELECT id_estudiante, codigo, nombre_completo, carrera, correo, celular, fecha_solicitud FROM estudiantes";
$result = mysqli_query($conexion, $sql);
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

    <!-- Navbar -->
    <?php
        include '../../layouts/navbar.php';
    ?>

    <section class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-black">Buscar estudiante</h2>
            <div class="flex space-x-4">
                <a href="registrar_estudiante.php" class="bg-yellow-500 text-black font-bold px-4 py-2 rounded-lg hover:bg-yellow-600 transition duration-300">
                    Registrar Nuevo Estudiante
                </a>
                <button id="editButton" class="bg-yellow-500 text-black px-4 py-2 rounded-lg font-bold hover:bg-yellow-600 transition duration-300" disabled>Editar</button>
                <button id="deleteButton" class="bg-red-500 text-white px-4 py-2 rounded-lg font-bold hover:bg-red-700 transition duration-300" disabled>Eliminar</button>
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

    <!-- Modal de edición -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-xl font-bold mb-4">Editar Estudiante</h2>
            <form id="editForm">
                <input type="hidden" id="editId" name="id_estudiante">

                <label class="block mb-2">Nombre:</label>
                <input type="text" id="editNombre" name="nombre_completo" class="w-full p-2 border rounded mb-2" required>

                <label class="block mb-2">Carrera:</label>
                <input type="text" id="editCarrera" name="carrera" class="w-full p-2 border rounded mb-2" required>

                <label class="block mb-2">Correo:</label>
                <input type="email" id="editCorreo" name="correo" class="w-full p-2 border rounded mb-2" required>

                <label class="block mb-2">Teléfono:</label>
                <input type="text" id="editCelular" name="celular" class="w-full p-2 border rounded mb-2" required>

                <div class="flex justify-end space-x-2 mt-4">
                    <button type="button" onclick="closeModal()" class="bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
                    <button type="submit" class="bg-yellow-500 text-black px-4 py-2 rounded">Guardar</button>
                </div>
            </form>
        </div>
    </div>

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

            document.getElementById('editButton').disabled = false;
            document.getElementById('deleteButton').disabled = false;
            document.getElementById('assignButton').disabled = false;
        }

        document.getElementById('editButton').addEventListener('click', function () {
            if (!selectedRow) return;

            const cells = selectedRow.getElementsByTagName('td');
            document.getElementById('editId').value = selectedRow.getAttribute('data-id');
            document.getElementById('editNombre').value = cells[1].textContent.trim();
            document.getElementById('editCarrera').value = cells[2].textContent.trim();
            document.getElementById('editCorreo').value = cells[3].textContent.trim();
            document.getElementById('editCelular').value = cells[4].textContent.trim();

            document.getElementById('editModal').classList.remove('hidden');
        });

        function closeModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        document.getElementById('editForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('editar_estudiante.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                location.reload();
            })
            .catch(error => console.error('Error:', error));
        });

        document.getElementById('deleteButton').addEventListener('click', function () {
            if (!selectedRow) return;
            const studentId = selectedRow.getAttribute('data-id');

            if (confirm('¿Estás seguro de que deseas eliminar este estudiante?')) {
                fetch('', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id_estudiante=${studentId}`
                })
                .then(response => response.text())
                .then(data => {
                    alert(data);
                    location.reload();
                })
                .catch(error => console.error('Error:', error));
            }
        });

        document.getElementById('assignButton').addEventListener('click', function () {
            if (!selectedRow) return;
            const studentId = selectedRow.getAttribute('data-id');
            window.location.href = `asignar.php?id_estudiante=${studentId}`;
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

    <?php 
        include '../../layouts/footer.php'; 
    ?>

</body>
</html>

<?php mysqli_close($conexion); ?>