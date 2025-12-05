<?php
include '../ProyectoPracticas/db.php';

// Verificar si se recibieron datos por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $id_practica = $_POST['id_practica'];
    $area = $_POST['area'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $tutor_id = $_POST['tutor_id'];

    // Verificar que la práctica existe y está en estado activa
    $check_sql = "SELECT estado FROM practica_profesional WHERE id = '$id_practica'";
    $check_result = mysqli_query($conexion, $check_sql);
    
    if (mysqli_num_rows($check_result) === 0) {
        die("La práctica no existe");
    }
    
    $practica = mysqli_fetch_assoc($check_result);
    if ($practica['estado'] !== 'activa') {
        die("Solo se pueden editar prácticas en estado 'En progreso'");
    }

    // Actualizar los datos de la práctica
    $update_sql = "UPDATE practica_profesional SET 
                    area = '$area',
                    fecha_inicio = '$fecha_inicio',
                    fecha_fin = " . ($fecha_fin ? "'$fecha_fin'" : "NULL") . ",
                    tutor_id = '$tutor_id'
                    WHERE id = '$id_practica'";

    if (mysqli_query($conexion, $update_sql)) {
        echo "Práctica actualizada correctamente";
        header("Refresh: 2; URL=practicas.php");
    } else {
        echo "Error al actualizar la práctica: " . mysqli_error($conexion);
    }

    mysqli_close($conexion);
    exit();
}
// Si no es POST, obtener datos para mostrar el formulario
if (isset($_GET['id'])) {
    $id_practica = $_GET['id'];
    
    // Obtener datos de la práctica
    $sql = "SELECT 
                pp.id AS id_practica,
                pp.area AS area_practica,
                pp.fecha_inicio AS fecha_inicio,
                pp.fecha_fin AS fecha_fin,
                pp.estado AS estado,
                pp.tutor_id AS tutor_id,
                e.nombre_completo AS nombre_estudiante,
                c.razon_social AS empresa,
                t.nombre_tutor AS tutor,
                t.cargo AS cargo_tutor
            FROM practica_profesional pp
            INNER JOIN estudiantes e ON pp.estudiante_id = e.id_estudiante
            INNER JOIN convenios c ON pp.convenio_id = c.id
            INNER JOIN tutores t ON pp.tutor_id = t.id
            WHERE pp.id = '$id_practica'";
    
    $result = mysqli_query($conexion, $sql);
    
    if (!$result || mysqli_num_rows($result) === 0) {
        die("Práctica no encontrada");
    }
    
    $practica = mysqli_fetch_assoc($result);
    
    // Verificar que la práctica está en estado activa
    if ($practica['estado'] !== 'activa') {
        die("Solo se pueden editar prácticas en estado 'En progreso'");
    }
    
    // Obtener lista de tutores para el convenio
    $convenio_id_sql = "SELECT convenio_id FROM practica_profesional WHERE id = '$id_practica'";
    $convenio_id_result = mysqli_query($conexion, $convenio_id_sql);
    $convenio_id_row = mysqli_fetch_assoc($convenio_id_result);
    $convenio_id = $convenio_id_row['convenio_id'];
    
    $tutores_sql = "SELECT id, nombre_tutor, cargo FROM tutores WHERE convenio_id = '$convenio_id'";
    $tutores_result = mysqli_query($conexion, $tutores_sql);
} else {
    die("ID de práctica no especificado");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Práctica</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <!-- Navbar -->
    <?php include '../../layouts/navbar.php'; ?>

    <section class="container mx-auto px-4 py-8">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold text-black mb-6">Editar Práctica Profesional</h2>
            
            <!-- Información básica -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-semibold mb-2">Información de la Práctica</h3>
                <p><strong>Estudiante:</strong> <?= $practica['nombre_estudiante'] ?></p>
                <p><strong>Empresa:</strong> <?= $practica['empresa'] ?></p>
                <p><strong>Estado actual:</strong> En progreso</p>
            </div>
            
            <!-- Formulario de edición -->
            <form method="POST" action="editar_practica.php">
                <input type="hidden" name="id_practica" value="<?= $practica['id_practica'] ?>">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="area" class="block text-sm font-medium text-gray-700 mb-1">Área de la Práctica:</label>
                        <input type="text" id="area" name="area" value="<?= $practica['area_practica'] ?>" 
                        class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:outline-none" required>
                    </div>
                    
                    <div>
                        <label for="tutor_id" class="block text-sm font-medium text-gray-700 mb-1">Tutor:</label>
                        <select id="tutor_id" name="tutor_id" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:outline-none" required>
                            <?php while ($tutor = mysqli_fetch_assoc($tutores_result)): ?>
                                <option value="<?= $tutor['id'] ?>" <?= $tutor['id'] == $practica['tutor_id'] ? 'selected' : '' ?>>
                                    <?= $tutor['nombre_tutor'] ?> (<?= $tutor['cargo'] ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Inicio:</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?= $practica['fecha_inicio'] ?>" 
                        class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:outline-none" required>
                    </div>
                    
                    <div>
                        <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Finalización (opcional):</label>
                        <input type="date" id="fecha_fin" name="fecha_fin" value="<?= $practica['fecha_fin'] ?>" 
                        class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:outline-none">
                    </div>
                </div>
                
                <div class="flex justify-end space-x-4 mt-6">
                    <a href="practicas.php" class="bg-gray-500 text-white px-4 py-2 rounded-lg font-bold hover:bg-gray-700 transition duration-300">Cancelar</a>
                    <button type="submit" class="bg-yellow-500 text-black px-4 py-2 rounded-lg font-bold hover:bg-yellow-600 transition duration-300">Guardar Cambios</button>
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