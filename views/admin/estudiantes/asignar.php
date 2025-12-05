<?php
include '../ProyectoPracticas/db.php'; // Conexión a la base de datos

// Verificar si se ha enviado el ID del estudiante
if (!isset($_GET['id_estudiante'])) {
    die("Error: ID del estudiante no proporcionado.");
}

$id_estudiante = $_GET['id_estudiante'];

// Obtener los datos del estudiante
$sql_estudiante = "SELECT * FROM estudiantes WHERE id_estudiante = '$id_estudiante'";
$result_estudiante = mysqli_query($conexion, $sql_estudiante);

if (!$result_estudiante || mysqli_num_rows($result_estudiante) === 0) {
    die("Error: Estudiante no encontrado.");
}

$estudiante = mysqli_fetch_assoc($result_estudiante);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Práctica Profesional</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <!-- Navbar -->
    <div class="fixed top-0 left-0 w-full z-50">
        <?php include '../../layouts/navbar.php'; ?>
    </div>

    <section class="flex justify-center items-center min-h-screen pt-24 px-6">
        <div class="max-w-3xl w-full bg-white p-6 rounded-lg shadow-lg border-t-4 border-yellow-400">
            <h2 class="text-2xl font-bold text-center text-black mb-6">Asignar Práctica Profesional</h2>

            <!-- Datos del estudiante -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-black">Datos del Estudiante</h3>
                <p><strong>Código:</strong> <?= $estudiante['codigo'] ?></p>
                <p><strong>Nombre:</strong> <?= $estudiante['nombre_completo'] ?></p>
                <p><strong>C.I.:</strong> <?= $estudiante['ci'] ?></p>
                <p><strong>Carrera:</strong> <?= $estudiante['carrera'] ?></p>
                <p><strong>Correo:</strong> <?= $estudiante['correo'] ?></p>
                <p><strong>Teléfono:</strong> <?= $estudiante['celular'] ?></p>
                <p><strong>Fecha de Solicitud:</strong> <?= $estudiante['fecha_solicitud'] ?></p>
            </div>

            <!-- Formulario de asignación -->
            <form action="procesar_asignacion.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" name="estudiante_id" value="<?= $estudiante['id_estudiante'] ?>">

                <!-- Convenio -->
                <div class="md:col-span-2">
                    <label for="convenio_id" class="block text-black font-semibold">Convenio (Empresa)</label>
                    <select id="convenio_id" name="convenio_id" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500" required>
                        <option value="">Seleccione un convenio</option>
                        <?php
                        // Obtener la lista de convenios
                        $sql_convenios = "SELECT * FROM convenios";
                        $result_convenios = mysqli_query($conexion, $sql_convenios);

                        if ($result_convenios) {
                            while ($convenio = mysqli_fetch_assoc($result_convenios)) {
                                echo "<option value='{$convenio['id']}'>{$convenio['razon_social']}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <!-- Tutor (se llena dinámicamente con JavaScript) -->
                <div class="md:col-span-2">
                    <label for="tutor_id" class="block text-black font-semibold">Tutor</label>
                    <select id="tutor_id" name="tutor_id" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500" required>
                        <option value="">Seleccione un tutor</option>
                    </select>
                </div>

                <!-- Área -->
                <div class="md:col-span-2">
                    <label for="area" class="block text-black font-semibold">Área de Trabajo</label>
                    <input type="text" id="area" name="area" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500" required>
                </div>

                <!-- Fecha de Inicio -->
                <div>
                    <label for="fecha_inicio" class="block text-black font-semibold">Fecha de Inicio</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500" required>
                </div>

                <!-- Total de Horas -->
                <div>
                    <label for="total_horas" class="block text-black font-semibold">Total de Horas</label>
                    <input type="number" id="total_horas" name="total_horas" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500" required>
                </div>

                <!-- Botones -->
                <div class="md:col-span-2 flex justify-between">
                    <button type="submit" class="px-4 py-2 bg-black text-white font-bold rounded-md hover:bg-gray-900 transition duration-300">Asignar</button>
                    <button type="button" onclick="window.history.back()" class="px-4 py-2 bg-gray-300 text-black font-bold rounded-md hover:bg-gray-400 transition duration-300">Cancelar</button>
                </div>
            </form>
        </div>
    </section>

    <?php 
        include '../../layouts/footer.php'; 
    ?>

    <!-- JavaScript para cargar tutores dinámicamente -->
    <script>
        document.getElementById('convenio_id').addEventListener('change', function () {
            const convenioId = this.value;
            const tutorSelect = document.getElementById('tutor_id');

            // Limpiar opciones anteriores
            tutorSelect.innerHTML = '<option value="">Seleccione un tutor</option>';

            if (convenioId) {
                // Obtener los tutores del convenio seleccionado
                fetch(`obtener_tutores.php?convenio_id=${convenioId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(tutor => {
                            tutorSelect.innerHTML += `<option value="${tutor.id}">${tutor.nombre_tutor}</option>`;
                        });
                    })
                    .catch(error => console.error('Error:', error));
            }
        });
    </script>
</body>

</html>

<?php mysqli_close($conexion); ?>