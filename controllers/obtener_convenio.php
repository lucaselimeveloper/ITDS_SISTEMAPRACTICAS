<?php
include '../ProyectoPracticas/db.php';

if (isset($_GET['id'])) {
    $id_convenio = $_GET['id'];

    // Obtener datos del convenio
    $sql_convenio = "SELECT * FROM convenios WHERE id = '$id_convenio'";
    $result_convenio = mysqli_query($conexion, $sql_convenio);
    $convenio = mysqli_fetch_assoc($result_convenio);

    // Obtener datos de los tutores
    $sql_tutores = "SELECT * FROM tutores WHERE convenio_id = '$id_convenio'";
    $result_tutores = mysqli_query($conexion, $sql_tutores);
    $tutores = [];
    while ($row = mysqli_fetch_assoc($result_tutores)) {
        $tutores[] = $row;
    }

    // Devolver los datos en formato JSON
    echo json_encode([
        'convenio' => $convenio,
        'tutores' => $tutores
    ]);

    mysqli_close($conexion);
}
?>