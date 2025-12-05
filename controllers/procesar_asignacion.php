<?php
include '../ProyectoPracticas/db.php'; // Conexión a la base de datos

// Verificar si la solicitud es de tipo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $estudiante_id = $_POST['estudiante_id'];
    $convenio_id = $_POST['convenio_id'];
    $tutor_id = $_POST['tutor_id'];
    $area = $_POST['area'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $total_horas = $_POST['total_horas'];

    // Validar que todos los campos estén presentes
    if (empty($estudiante_id) || empty($convenio_id) || empty($tutor_id) || empty($area) || empty($fecha_inicio) || empty($total_horas)) {
        // Redirigir con un mensaje de error si falta algún campo
        header("Location: asignar.php?id_estudiante=$estudiante_id&error=Todos los campos son obligatorios.");
        exit();
    }

    // Escapar los datos para evitar inyecciones SQL
    $estudiante_id = mysqli_real_escape_string($conexion, $estudiante_id);
    $convenio_id = mysqli_real_escape_string($conexion, $convenio_id);
    $tutor_id = mysqli_real_escape_string($conexion, $tutor_id);
    $area = mysqli_real_escape_string($conexion, $area);
    $fecha_inicio = mysqli_real_escape_string($conexion, $fecha_inicio);
    $total_horas = mysqli_real_escape_string($conexion, $total_horas);

    // Insertar la práctica profesional en la base de datos
    $sql = "INSERT INTO practica_profesional (estudiante_id, convenio_id, tutor_id, area, fecha_inicio, total_horas)
            VALUES ('$estudiante_id', '$convenio_id', '$tutor_id', '$area', '$fecha_inicio', '$total_horas')";

    if (mysqli_query($conexion, $sql)) {
        // Redirigir con un mensaje de éxito
        header("Location: estudiantes.php?success=Práctica asignada correctamente.");
        exit();
    } else {
        // Redirigir con un mensaje de error si falla la inserción
        header("Location: asignar.php?id_estudiante=$estudiante_id&error=Error al asignar la práctica: " . mysqli_error($conexion));
        exit();
    }
} else {
    // Redirigir si el método de solicitud no es POST
    header("Location: index.php?error=Método de solicitud no válido.");
    exit();
}

mysqli_close($conexion);
?>