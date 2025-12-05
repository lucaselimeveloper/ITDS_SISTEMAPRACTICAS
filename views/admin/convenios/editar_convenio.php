<?php
include '../ProyectoPracticas/db.php';

// Obtener ID del convenio a editar
$id_convenio = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id_convenio) {
    header('Location: convenios.php');
    exit();
}

// Obtener datos del convenio
$sql_convenio = "SELECT * FROM convenios WHERE id = '$id_convenio'";
$result_convenio = mysqli_query($conexion, $sql_convenio);
$convenio = mysqli_fetch_assoc($result_convenio);

if (!$convenio) {
    header('Location: convenios.php');
    exit();
}

// Obtener tutores del convenio
$sql_tutores = "SELECT * FROM tutores WHERE convenio_id = '$id_convenio'";
$result_tutores = mysqli_query($conexion, $sql_tutores);
$tutores = [];
while ($row = mysqli_fetch_assoc($result_tutores)) {
    $tutores[] = $row;
}

// Procesar el formulario de actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $razon_social = $_POST['razon_social'];
    $representante_legal = $_POST['representante_legal'];
    $direccion = $_POST['direccion'];
    
    // Iniciar transacción
    mysqli_begin_transaction($conexion);
    
    try {
        // Actualizar datos del convenio
        $sql_update = "UPDATE convenios SET 
                    razon_social = '$razon_social',
                    representante_legal = '$representante_legal',
                    direccion = '$direccion'
                    WHERE id = '$id_convenio'";
        
        if (!mysqli_query($conexion, $sql_update)) {
            throw new Exception("Error al actualizar convenio: " . mysqli_error($conexion));
        }
        
        // Procesar tutores
        $tutores_post = $_POST['tutores'] ?? [];
        $tutores_actuales_ids = array_column($tutores, 'id');
        $tutores_procesados = [];
        
        foreach ($tutores_post as $tutor) {
            $tutor_id = mysqli_real_escape_string($conexion, $tutor['id'] ?? '');
            $nombre = mysqli_real_escape_string($conexion, $tutor['nombre']);
            $cargo = mysqli_real_escape_string($conexion, $tutor['cargo']);
            $correo = mysqli_real_escape_string($conexion, $tutor['correo']);
            $telefono = mysqli_real_escape_string($conexion, $tutor['telefono']);
            
            if (in_array($tutor_id, $tutores_actuales_ids)) {
                // Actualizar tutor existente
                $sql_tutor = "UPDATE tutores SET 
                            nombre_tutor = '$nombre',
                            cargo = '$cargo',
                            correo = '$correo',
                            telefono = '$telefono'
                            WHERE id = '$tutor_id' AND convenio_id = '$id_convenio'";
                $tutores_procesados[] = $tutor_id;
            } else {
                // Insertar nuevo tutor
                $sql_tutor = "INSERT INTO tutores (convenio_id, nombre_tutor, cargo, correo, telefono)
                            VALUES ('$id_convenio', '$nombre', '$cargo', '$correo', '$telefono')";
            }
            
            if (!mysqli_query($conexion, $sql_tutor)) {
                throw new Exception("Error al procesar tutor: " . mysqli_error($conexion));
            }
        }
        
        // Eliminar tutores que no están en el formulario
        $tutores_a_eliminar = array_diff($tutores_actuales_ids, $tutores_procesados);
        foreach ($tutores_a_eliminar as $id) {
            $sql_delete = "DELETE FROM tutores WHERE id = '$id' AND convenio_id = '$id_convenio'";
            if (!mysqli_query($conexion, $sql_delete)) {
                throw new Exception("Error al eliminar tutor: " . mysqli_error($conexion));
            }
        }
        
        // Confirmar transacción
        mysqli_commit($conexion);
        $mensaje_exito = "Convenio actualizado correctamente";
    } catch (Exception $e) {
        mysqli_rollback($conexion);
        $mensaje_error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Convenio</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <div class="fixed top-0 left-0 w-full z-50">
        <?php include '../../layouts/navbar.php'; ?>
    </div>

    <section class="flex justify-center items-center min-h-screen pt-24 px-6">
        <div class="max-w-3xl w-full bg-white p-6 rounded-lg shadow-lg border-t-4 border-yellow-400">
            <h2 class="text-2xl font-bold text-center text-black mb-6">Editar Convenio</h2>
            
            <?php if (isset($mensaje_exito)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?php echo $mensaje_exito; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($mensaje_error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo $mensaje_error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Nombre o Razón Social -->
                <div class="md:col-span-2">
                    <label for="razon_social" class="block text-black font-semibold">Nombre o Razón Social</label>
                    <input type="text" id="razon_social" name="razon_social" 
                        value="<?php echo htmlspecialchars($convenio['razon_social']); ?>" 
                        class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500" required>
                </div>

                <!-- Representante Legal -->
                <div class="md:col-span-2">
                    <label for="representante_legal" class="block text-black font-semibold">Representante Legal</label>
                    <input type="text" id="representante_legal" name="representante_legal" 
                        value="<?php echo htmlspecialchars($convenio['representante_legal']); ?>" 
                        class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500" required>
                </div>

                <!-- Dirección -->
                <div class="md:col-span-2">
                    <label for="direccion" class="block text-black font-semibold">Dirección</label>
                    <input type="text" id="direccion" name="direccion" 
                        value="<?php echo htmlspecialchars($convenio['direccion']); ?>" 
                        class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500" required>
                </div>

                <h3 class="text-xl font-semibold md:col-span-2 mt-6">Tutores</h3>

                <!-- Contenedor de Tutores -->
                <div class="md:col-span-2" id="tutorContainer">
                    <?php foreach ($tutores as $index => $tutor): ?>
                        <div class="tutor-group border border-gray-300 p-4 rounded-lg mb-4">
                            <input type="hidden" name="tutores[<?php echo $index; ?>][id]" value="<?php echo $tutor['id']; ?>">
                            
                            <label class="block text-black font-semibold">Nombre del Tutor</label>
                            <input type="text" name="tutores[<?php echo $index; ?>][nombre]" 
                                value="<?php echo htmlspecialchars($tutor['nombre_tutor']); ?>" 
                                class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500 mb-2" required>

                            <label class="block text-black font-semibold">Cargo</label>
                            <input type="text" name="tutores[<?php echo $index; ?>][cargo]" 
                                value="<?php echo htmlspecialchars($tutor['cargo']); ?>" 
                                class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500 mb-2">

                            <label class="block text-black font-semibold">Correo Electrónico</label>
                            <input type="email" name="tutores[<?php echo $index; ?>][correo]" 
                                value="<?php echo htmlspecialchars($tutor['correo']); ?>" 
                                class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500 mb-2">

                            <label class="block text-black font-semibold">Teléfono</label>
                            <input type="text" name="tutores[<?php echo $index; ?>][telefono]" 
                                value="<?php echo htmlspecialchars($tutor['telefono']); ?>" 
                                class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500 mb-2">
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Botón para agregar más tutores -->
                <div class="md:col-span-2">
                    <button type="button" onclick="addTutor()" class="bg-blue-500 text-white px-4 py-2 rounded-md font-bold hover:bg-blue-700 transition duration-300">
                        Agregar Otro Tutor
                    </button>
                </div>

                <!-- Botones -->
                <div class="md:col-span-2 flex justify-between mt-4">
                    <button type="submit" class="px-4 py-2 bg-yellow-500 text-black font-bold rounded-md hover:bg-yellow-600 transition duration-300">Guardar Cambios</button>
                    <a href="convenios.php" class="px-4 py-2 bg-gray-300 text-black font-bold rounded-md hover:bg-gray-400 transition duration-300">Cancelar</a>
                </div>
            </form>
        </div>
    </section>

    <script>
        // Contador para nuevos tutores
        let tutorCounter = <?php echo count($tutores); ?>;
        
        function addTutor() {
            const tutorContainer = document.getElementById("tutorContainer");
            const newTutor = document.createElement("div");
            newTutor.classList.add("tutor-group", "border", "border-gray-300", "p-4", "rounded-lg", "mb-4");

            newTutor.innerHTML = `
                <hr class="my-4">
                <input type="hidden" name="tutores[${tutorCounter}][id]" value="new">
                
                <label class="block text-black font-semibold">Nombre del Tutor</label>
                <input type="text" name="tutores[${tutorCounter}][nombre]" 
                    class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500 mb-2" required>

                <label class="block text-black font-semibold">Cargo</label>
                <input type="text" name="tutores[${tutorCounter}][cargo]" 
                    class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500 mb-2">

                <label class="block text-black font-semibold">Correo Electrónico</label>
                <input type="email" name="tutores[${tutorCounter}][correo]" 
                    class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500 mb-2">

                <label class="block text-black font-semibold">Teléfono</label>
                <input type="text" name="tutores[${tutorCounter}][telefono]" 
                    class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500 mb-2">
            `;

            tutorContainer.appendChild(newTutor);
            tutorCounter++;
        }
    </script>

    <?php 
        include '../../layouts/footer.php'; 
    ?>
</body>
</html>