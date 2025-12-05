<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Registro de Estudiante</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <!-- Navbar -->
    <div class="fixed top-0 left-0 w-full z-50">
        <?php include '../../layouts/navbar.php'; ?>
    </div>

    <section class="flex justify-center items-center min-h-screen pt-24 px-6">
        <div class="max-w-3xl w-full bg-white p-6 rounded-lg shadow-lg border-t-4 border-yellow-400">
            <h2 class="text-2xl font-bold text-center text-black mb-6">Registro de Estudiante</h2>

            <form action="procesar_registro.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Código -->
                <div class="md:col-span-2">
                    <label for="codigo" class="block text-black font-semibold">Código</label>
                    <input type="text" id="codigo" name="codigo" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500" required>
                </div>

                <!-- Nombre Completo -->
                <div class="md:col-span-2">
                    <label for="nombre_completo" class="block text-black font-semibold">Estudiante (Nombre Completo)</label>
                    <input type="text" id="nombre_completo" name="nombre_completo" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500" required>
                </div>

                <!-- CI Nº -->
                <div>
                    <label for="ci" class="block text-black font-semibold">C.I. Nº</label>
                    <input type="text" id="ci" name="ci" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500" required>
                </div>

                <!-- Carrera (Combo Box) -->
                <div>
                    <label for="carrera" class="block text-black font-semibold">Carrera</label>
                    <select id="carrera" name="carrera" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500" required>
                        <option value="">Selecciona una carrera</option>
                        <option value="Administración de Empresas TS">Administración De Empresas TS</option>
                        <option value="Comercio Internacional y Gestión Aduanera TS">Comercio Internacional y Gestión Aduanera TS</option>
                        <option value="Contaduría General">Contaduría General TS</option>
                        <option value="Mercadotecnia">Mercadotecnia TS</option>
                        <option value="Gestor de Tramites Aduaneros">Gestor De Tramites Aduaneros TM</option>
                        <option value="Contaduría General TM">Contaduría General TM</option>
                        <option value="Administración Técnica">Administración Técnica TM</option>
                        <option value="Marketing y Ventas">Marketing y Ventas TM</option>
                        <option value="Perito en Banca">Perito en Ventas TM</option>
                        <option value="Secretariado Ejecutivo">Secretariado Ejecutivo TS</option>
                        <option value="Turismo">Turismo TS</option>
                        <option value="Secretariado Administrativo">Secretariado Administrativo TM</option>
                        <option value="Guía Turistica">Guía Turistica TM</option>
                        <option value="Topografía y Geodesia">Topografía y Geodesia TS</option>
                        <option value="Diseño de Interiores">Diseño de Interiores TS</option>
                        <option value="Construcción Civil">Construcción Civil TS</option>
                        <option value="Decoración de Interiores">Decoración de Interiores TM</option>
                        <option value="Construcción">Construcción TM</option>                                                                                             
                    </select>
                </div>

                <!-- Fecha de Solicitud -->
                <div>
                    <label for="fecha_solicitud" class="block text-black font-semibold">Fecha de Solicitud</label>
                    <input type="date" id="fecha_solicitud" name="fecha_solicitud" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500" required>
                </div>

                <!-- Año (Combo Box) -->
                <div class="md:col-span-2">
                    <label for="ano" class="block text-black font-semibold">Año</label>
                    <select id="ano" name="ano" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500" required>
                        <option value="">Selecciona el año</option>
                        <option value="2">2do Año</option>
                        <option value="3">3er Año</option>
                    </select>
                </div>

                <!-- Celular -->
                <div>
                    <label for="celular" class="block text-black font-semibold">Celular</label>
                    <input type="tel" id="celular" name="celular" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500" required>
                </div>

                <!-- Correo -->
                <div>
                    <label for="correo" class="block text-black font-semibold">Correo Electrónico</label>
                    <input type="email" id="correo" name="correo" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-yellow-500" required>
                </div>

                <!-- Botones -->
                <div class="md:col-span-2 flex justify-between">
                    <button type="submit" class="px-4 py-2 bg-black text-white font-bold rounded-md hover:bg-gray-900 transition duration-300">Guardar</button>
                    <button type="button" onclick="window.history.back()" class="px-4 py-2 bg-gray-300 text-black font-bold rounded-md hover:bg-gray-400 transition duration-300">Cancelar</button>
                </div>
            </form>
        </div>
    </section>

    <?php 
        include '../../layouts/footer.php'; 
    ?>
</body>

</html>
