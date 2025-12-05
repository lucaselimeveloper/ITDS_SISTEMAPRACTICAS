<?php
// Incluir archivo de conexión
include 'db.php';

// Obtener datos del formulario
$razon_social = $_POST['razon_social'];
$representante_legal = $_POST['representante_legal'];
$direccion = $_POST['direccion'];

// Insertar el convenio en la base de datos
$sql = "INSERT INTO convenios (razon_social, representante_legal, direccion) VALUES (?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("sss", $razon_social, $representante_legal, $direccion);

if ($stmt->execute()) {
    // Obtener el ID del convenio recién insertado
    $convenio_id = $stmt->insert_id;

    // Verificar si se enviaron datos de tutores
    if (!empty($_POST['nombre_tutor'])) {
        foreach ($_POST['nombre_tutor'] as $index => $nombre_tutor) {
            if (!empty($nombre_tutor)) { // Solo insertar si el campo no está vacío
                $cargo = $_POST['cargo'][$index] ?? null;
                $correo = $_POST['correo'][$index] ?? null;
                $telefono = $_POST['telefono'][$index] ?? null;

                // Insertar cada tutor relacionado con el convenio
                $sql_tutor = "INSERT INTO tutores (convenio_id, nombre_tutor, cargo, correo, telefono) VALUES (?, ?, ?, ?, ?)";
                $stmt_tutor = $conexion->prepare($sql_tutor);
                $stmt_tutor->bind_param("issss", $convenio_id, $nombre_tutor, $cargo, $correo, $telefono);
                $stmt_tutor->execute();
                $stmt_tutor->close();
            }
        }
    }

    // Redirigir a la página de lista de convenios con un mensaje de éxito
    echo "<script>alert('Convenio registrado exitosamente'); window.location.href='convenios.php';</script>";
} else {
    echo "<script>alert('Error al registrar el convenio: " . $conexion->error . "'); window.history.back();</script>";
}

// Cerrar conexiones
$stmt->close();
$conexion->close();
?>
