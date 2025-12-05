<?php
include '../ProyectoPracticas/db.php'; // Conexión a la base de datos

// Verificar si se ha pasado el ID de la práctica en la URL
if (!isset($_GET['id_practica'])) {
    die("Error: No se ha especificado una práctica.");
}

$id_practica = $_GET['id_practica'];

// Obtener la información de la práctica
$sql = "SELECT 
            pp.id AS id_practica,
            e.codigo AS codigo_estudiante,
            e.ci AS ci_estudiante,
            e.nombre_completo AS nombre_estudiante,
            e.carrera AS carrera_estudiante,
            c.razon_social AS empresa,
            t.nombre_tutor AS tutor,
            pp.fecha_inicio AS fecha_inicio,
            pp.fecha_fin AS fecha_fin,
            pp.estado AS estado
        FROM practica_profesional pp
        INNER JOIN estudiantes e ON pp.estudiante_id = e.id_estudiante
        INNER JOIN convenios c ON pp.convenio_id = c.id
        INNER JOIN tutores t ON pp.tutor_id = t.id
        WHERE pp.id = '$id_practica'";

$result = mysqli_query($conexion, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    die("Error: No se encontró la práctica especificada.");
}

$practica = mysqli_fetch_assoc($result);

// Procesar el formulario de congelación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['fecha_fin']) && isset($_POST['motivo'])) {
        $fecha_fin = $_POST['fecha_fin'];
        $motivo = $_POST['motivo'];

        // Actualizar la práctica en la base de datos (marcar como congelada)
            $update_sql = "UPDATE practica_profesional 
                        SET fecha_fin = '$fecha_fin', 
                            estado = 'congelada',
                            motivo_congelacion = '$motivo'
                        WHERE id = '$id_practica'";

        if (mysqli_query($conexion, $update_sql)) {
            // Redirigir a la página de prácticas
            header("Location: practicas.php");
            exit();
        } else {
            echo "Error al congelar la práctica: " . mysqli_error($conexion);
        }
    } else {
        echo "Error: Faltan datos requeridos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Congelar Práctica</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <!-- Navbar -->
    <?php include '../../layouts/navbar.php'; ?>

    <section class="container mx-auto px-4 py-8">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold text-black mb-6">Congelar Práctica</h2>

            <!-- Información de la práctica -->
            <div class="mb-6">
                <h3 class="text-xl font-semibold mb-2">Información de la Práctica</h3>
                <div class="space-y-2">
                    <p><strong>Código Estudiante:</strong> <?= $practica['codigo_estudiante'] ?></p>
                    <p><strong>Nombre:</strong> <?= $practica['nombre_estudiante'] ?></p>
                    <p><strong>Carrera:</strong> <?= $practica['carrera_estudiante'] ?></p>
                    <p><strong>Empresa:</strong> <?= $practica['empresa'] ?></p>
                    <p><strong>Tutor:</strong> <?= $practica['tutor'] ?></p>
                    <p><strong>Fecha de Inicio:</strong> <?= $practica['fecha_inicio'] ?></p>
                    <p><strong>Estado Actual:</strong> 
                        <?= ucfirst($practica['estado']) ?>
                    </p>
                </div>
            </div>

            <!-- Formulario de congelación -->
            <form method="POST">
                <div class="mb-4">
                    <label for="fecha_fin" class="block text-sm font-medium text-gray-700">Fecha de Congelación:</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" class="mt-1 p-2 border rounded-lg w-full" required>
                </div>

                <div class="mb-4">
                    <label for="motivo" class="block text-sm font-medium text-gray-700">Motivo de Congelación:</label>
                    <textarea id="motivo" name="motivo" rows="4" class="mt-1 p-2 border rounded-lg w-full" required></textarea>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="practicas.php" class="bg-gray-500 text-white px-4 py-2 rounded-lg font-bold hover:bg-gray-700 transition duration-300">Cancelar</a>
                    <button type="submit" class="bg-purple-500 text-white px-4 py-2 rounded-lg font-bold hover:bg-purple-700 transition duration-300">Congelar Práctica</button>
                </div>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <?php 
        include '../../layouts/footer.php'; 
    ?>
</body>
</html>

<?php mysqli_close($conexion); ?>