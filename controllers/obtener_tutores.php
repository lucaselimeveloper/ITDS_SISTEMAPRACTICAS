<?php
include '../ProyectoPracticas/db.php'; // Conexión a la base de datos

if (isset($_GET['convenio_id'])) {
    $convenio_id = $_GET['convenio_id'];

    // Consulta para obtener los tutores asociados al convenio
    $sql = "SELECT * FROM tutores WHERE convenio_id = '$convenio_id'";
    $result = mysqli_query($conexion, $sql);

    $tutores = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $tutores[] = $row;
    }

    // Devolver los tutores en formato JSON
    echo json_encode($tutores);

    mysqli_close($conexion);
}
?>