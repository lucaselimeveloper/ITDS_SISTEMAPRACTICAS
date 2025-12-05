<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Convenios</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <!-- Navbar -->
    <div class="fixed top-0 left-0 w-full z-50">
        <?php include '../ProyectoPracticas/resources/navbar_user.php'; ?>
    </div>

    <section class="flex justify-center items-center min-h-screen pt-24 px-6">
        <div class="max-w-3xl w-full bg-white p-6 rounded-lg shadow-lg border-t-4 border-yellow-400">
            <h2 class="text-2xl font-bold text-center text-black mb-6">Registro de Convenios</h2>

            <form action="procesar_convenio.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Nombre o Razón Social -->
                <div class="md:col-span-2">
                    <label for="razon_social" class="block text-black font-semibold">Nombre o Razón Social</label>
                    <input type="text" id="razon_social" name="razon_social" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500" required>
                </div>

                <!-- Representante Legal -->
                <div class="md:col-span-2">
                    <label for="representante_legal" class="block text-black font-semibold">Representante Legal</label>
                    <input type="text" id="representante_legal" name="representante_legal" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500" required>
                </div>

                <!-- Dirección -->
                <div class="md:col-span-2">
                    <label for="direccion" class="block text-black font-semibold">Dirección</label>
                    <input type="text" id="direccion" name="direccion" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500" required>
                </div>

                <h3 class="text-xl font-semibold md:col-span-2 mt-6">Datos del Tutor</h3>

                <!-- Contenedor de Tutores -->
                <div class="md:col-span-2" id="tutorContainer">
                    <div class="tutor-group border border-gray-300 p-4 rounded-lg mb-4">
                        <label class="block text-black font-semibold">Nombre del Tutor</label>
                        <input type="text" name="nombre_tutor[]" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500 mb-2">

                        <label class="block text-black font-semibold">Cargo</label>
                        <input type="text" name="cargo[]" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500 mb-2">

                        <label class="block text-black font-semibold">Correo Electrónico</label>
                        <input type="email" name="correo[]" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500 mb-2">

                        <label class="block text-black font-semibold">Teléfono</label>
                        <input type="text" name="telefono[]" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500 mb-2">
                    </div>
                </div>

                <!-- Botón para agregar más tutores -->
                <div class="md:col-span-2">
                    <button type="button" onclick="addTutor()" class="bg-blue-500 text-white px-4 py-2 rounded-md font-bold hover:bg-blue-700 transition duration-300">
                        Agregar Otro Tutor
                    </button>
                </div>

                <!-- Botones -->
                <div class="md:col-span-2 flex justify-between mt-4">
                    <button type="submit" class="px-4 py-2 bg-black text-white font-bold rounded-md hover:bg-gray-900 transition duration-300">Guardar</button>
                    <button type="reset" onclick="window.history.back()" class="px-4 py-2 bg-gray-300 text-black font-bold rounded-md hover:bg-gray-400 transition duration-300">Cancelar</button>
                </div>
            </form>
        </div>
    </section>

    <script>
        function addTutor() {
            const tutorContainer = document.getElementById("tutorContainer");
            const newTutor = document.createElement("div");
            newTutor.classList.add("tutor-group", "border", "border-gray-300", "p-4", "rounded-lg", "mb-4");

            newTutor.innerHTML = `
                <hr class="my-4">
                <label class="block text-black font-semibold">Nombre del Tutor</label>
                <input type="text" name="nombre_tutor[]" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500 mb-2">

                <label class="block text-black font-semibold">Cargo</label>
                <input type="text" name="cargo[]" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500 mb-2">

                <label class="block text-black font-semibold">Correo Electrónico</label>
                <input type="email" name="correo[]" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500 mb-2">

                <label class="block text-black font-semibold">Teléfono</label>
                <input type="text" name="telefono[]" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500 mb-2">
            `;

            tutorContainer.appendChild(newTutor);
        }
    </script>

    <?php include '../ProyectoPracticas/resources/footer.php'; ?>
</body>

</html>
