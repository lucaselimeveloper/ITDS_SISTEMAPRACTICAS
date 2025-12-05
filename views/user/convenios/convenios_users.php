<?php
    include '../../../config/db.php'; // Asegúrate de que la ruta sea correcta

// Manejar la solicitud de generación de reporte
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generar_reporte'])) {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="reporte_convenios.xls"');
    
    echo "<table border='1'>";
    echo "<tr>
            <th>ID</th>
            <th>Razón Social</th>
            <th>Representante Legal</th>
            <th>Dirección</th>
            <th>Fecha de Registro</th>
            <th>Tutores</th>
          </tr>";
    
    $sql = "SELECT c.id, c.razon_social, c.representante_legal, c.direccion, c.fecha_registro, 
                   GROUP_CONCAT(t.nombre_tutor SEPARATOR ', ') AS tutores
            FROM convenios c
            LEFT JOIN tutores t ON c.id = t.convenio_id";
    
    if (isset($_POST['search_value']) && !empty($_POST['search_value'])) {
        $search = mysqli_real_escape_string($conexion, $_POST['search_value']);
        $sql .= " WHERE c.razon_social LIKE '%$search%' OR c.representante_legal LIKE '%$search%' 
                  OR c.direccion LIKE '%$search%' OR t.nombre_tutor LIKE '%$search%'";
    }
    
    $sql .= " GROUP BY c.id";
    $result = mysqli_query($conexion, $sql);
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>".$row['id']."</td>";
        echo "<td>".$row['razon_social']."</td>";
        echo "<td>".$row['representante_legal']."</td>";
        echo "<td>".$row['direccion']."</td>";
        echo "<td>".$row['fecha_registro']."</td>";
        echo "<td>".$row['tutores']."</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    exit();
}

// Verificar si se ha solicitado eliminar un convenio
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id_convenio = $_POST['id'];
    $delete_sql = "DELETE FROM convenios WHERE id = '$id_convenio'";

    if (mysqli_query($conexion, $delete_sql)) {
        echo "Convenio eliminado correctamente";
    } else {
        echo "Error al eliminar el convenio: " . mysqli_error($conexion);
    }

    mysqli_close($conexion);
    exit();
}

// Realizar la consulta a la base de datos
$sql = "SELECT c.id, c.razon_social, c.representante_legal, c.direccion, c.fecha_registro, 
               GROUP_CONCAT(t.nombre_tutor SEPARATOR ', ') AS tutores
        FROM convenios c
        LEFT JOIN tutores t ON c.id = t.convenio_id
        GROUP BY c.id";
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
    <title>Gestionar Convenios</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <!-- Navbar -->
    <?php include '../ProyectoPracticas/resources/navbar_user.php'; ?>

    <section class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-black">Lista de Convenios</h2>
            <div class="flex space-x-4">
                <button id="reportButton" class="bg-green-500 text-white px-4 py-2 rounded-lg font-bold hover:bg-green-700 transition duration-300">Reporte</button>
            </div>
        </div>

        <!-- Buscador -->
        <div class="flex justify-between items-center mb-6">
            <input type="text" id="search" class="border border-gray-300 rounded-lg p-2 w-full max-w-md focus:ring-2 focus:ring-yellow-500 focus:outline-none" placeholder="Buscar convenio..." oninput="filterTable()">
        </div>

        <!-- Tabla de Convenios -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <table class="table-auto w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-black text-white">
                        <th class="px-4 py-3 border">ID</th>
                        <th class="px-4 py-3 border">Razón Social</th>
                        <th class="px-4 py-3 border">Representante Legal</th>
                        <th class="px-4 py-3 border">Dirección</th>
                        <th class="px-4 py-3 border">Fecha de Registro</th>
                        <th class="px-4 py-3 border">Tutores</th>
                    </tr>
                </thead>
                <tbody id="convenioTable" class="text-black">
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr class="hover:bg-yellow-100 transition duration-300 cursor-pointer" onclick="selectRow(this)">
                            <td class="px-4 py-3 border text-center"><?php echo $row['id']; ?></td>
                            <td class="px-4 py-3 border"><?php echo $row['razon_social']; ?></td>
                            <td class="px-4 py-3 border"><?php echo $row['representante_legal']; ?></td>
                            <td class="px-4 py-3 border"><?php echo $row['direccion']; ?></td>
                            <td class="px-4 py-3 border"><?php echo $row['fecha_registro']; ?></td>
                            <td class="px-4 py-3 border"><?php echo $row['tutores']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Modal de edición -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96 max-h-[90vh] overflow-y-auto">
            <h2 class="text-xl font-bold mb-4">Editar Convenio</h2>
            <form id="editForm">
                <input type="hidden" id="editId" name="id_convenio">

                <label class="block mb-2">Razón Social:</label>
                <input type="text" id="editRazon" name="razon_social" class="w-full p-2 border rounded mb-2" required>

                <label class="block mb-2">Representante Legal:</label>
                <input type="text" id="editRepresentante" name="representante_legal" class="w-full p-2 border rounded mb-2" required>

                <label class="block mb-2">Dirección:</label>
                <input type="text" id="editDireccion" name="direccion" class="w-full p-2 border rounded mb-2" required>

                <!-- Campos para editar tutores -->
                <div id="tutoresContainer" class="mb-4">
                    <label class="block mb-2">Tutores:</label>
                    <!-- Los campos de tutores se generarán dinámicamente aquí -->
                </div>

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
            const rows = document.querySelectorAll('#convenioTable tr');

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
        }

        document.getElementById('editButton').addEventListener('click', function () {
            if (!selectedRow) return;

            const convenioId = selectedRow.getElementsByTagName('td')[0].textContent.trim();

            // Obtener los datos del convenio y sus tutores mediante AJAX
            fetch(`obtener_convenio.php?id=${convenioId}`)
                .then(response => response.json())
                .then(data => {
                    // Llenar los campos del convenio
                    document.getElementById('editId').value = data.convenio.id;
                    document.getElementById('editRazon').value = data.convenio.razon_social;
                    document.getElementById('editRepresentante').value = data.convenio.representante_legal;
                    document.getElementById('editDireccion').value = data.convenio.direccion;

                    // Llenar los campos de los tutores
                    const tutoresContainer = document.getElementById('tutoresContainer');
                    tutoresContainer.innerHTML = '<label class="block mb-2">Tutores:</label>';

                    data.tutores.forEach((tutor, index) => {
                        tutoresContainer.innerHTML += `
                            <div class="mb-2">
                                <input type="hidden" name="tutores[${index}][id]" value="${tutor.id}">
                                <input type="text" name="tutores[${index}][nombre]" value="${tutor.nombre_tutor}" class="w-full p-2 border rounded mb-1" placeholder="Nombre del tutor" required>
                                <input type="text" name="tutores[${index}][cargo]" value="${tutor.cargo}" class="w-full p-2 border rounded mb-1" placeholder="Cargo">
                                <input type="email" name="tutores[${index}][correo]" value="${tutor.correo}" class="w-full p-2 border rounded mb-1" placeholder="Correo">
                                <input type="text" name="tutores[${index}][telefono]" value="${tutor.telefono}" class="w-full p-2 border rounded mb-1" placeholder="Teléfono">
                            </div>
                        `;
                    });

                    // Mostrar el modal
                    document.getElementById('editModal').classList.remove('hidden');
                })
                .catch(error => console.error('Error:', error));
        });

        function closeModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        document.getElementById('editForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('editar_convenio.php', {
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
            const convenioId = selectedRow.getElementsByTagName('td')[0].textContent.trim();

            if (confirm('¿Estás seguro de que deseas eliminar este convenio?')) {
                fetch('', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id=' + convenioId
                })
                .then(response => response.text())
                .then(data => {
                    alert(data);
                    location.reload();
                })
                .catch(error => console.error('Error:', error));
            }
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