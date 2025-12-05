<?php
include '../ProyectoPracticas/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id_estudiante'];
    $nombre = $_POST['nombre_completo'];
    $carrera = $_POST['carrera'];
    $correo = $_POST['correo'];
    $celular = $_POST['celular'];

    $sql = "UPDATE estudiantes SET nombre_completo='$nombre', carrera='$carrera', correo='$correo', celular='$celular' WHERE id_estudiante=$id";

    if (mysqli_query($conexion, $sql)) {
        echo "Estudiante actualizado correctamente.";
    } else {
        echo "Error al actualizar: " . mysqli_error($conexion);
    }

    mysqli_close($conexion);
}
?>
