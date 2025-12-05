<?php
include 'db.php'; // Incluye el archivo de conexión

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $codigo = $_POST['codigo'];
    $nombre_completo = $_POST['nombre_completo'];
    $ci = $_POST['ci'];
    $carrera = $_POST['carrera'];
    $fecha_solicitud = $_POST['fecha_solicitud'];
    $ano = $_POST['ano'];
    $celular = $_POST['celular'];
    $correo = $_POST['correo'];

    // Preparar la consulta SQL para insertar datos
    $sql = "INSERT INTO estudiantes (codigo, nombre_completo, ci, carrera, fecha_solicitud, ano, celular, correo) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conexion, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssssss", $codigo, $nombre_completo, $ci, $carrera, $fecha_solicitud, $ano, $celular, $correo);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>
                    alert('Registro exitoso.');
                    window.location.href = 'estudiantes.php';
                </script>";
            exit();
        } else {
            echo "Error al registrar: " . mysqli_error($conexion);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error en la preparación de la consulta: " . mysqli_error($conexion);
    }

    mysqli_close($conexion);
} else {
    echo "Método de solicitud no válido.";
}
