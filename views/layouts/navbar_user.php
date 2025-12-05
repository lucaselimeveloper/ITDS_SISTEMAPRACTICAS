<!-- Navbar Personalizado para usuarios no administradores -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <nav class="bg-black text-white shadow-lg rounded-b-lg">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <!-- Logo -->
            <div>
                <a href="practicas_users.php" class="text-2xl font-extrabold tracking-wide text-white">PRACTICAS ITDS</a>
            </div>

            <!-- Menú hamburguesa para móviles -->
            <button id="menu-toggle" class="lg:hidden focus:outline-none">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                </svg>
            </button>

            <!-- Opciones del menú -->
            <ul id="menu" class="hidden lg:flex space-x-6">
                <li><a href="estudiantes_users.php" class="hover:bg-white hover:text-black px-3 py-2 rounded-lg transition duration-300 font-bold">Estudiantes</a></li>
                <li><a href="convenios_users.php" class="hover:bg-white hover:text-black px-3 py-2 rounded-lg transition duration-300 font-bold">Convenios</a></li>
                <li><a href="practicas_users.php" class="hover:bg-white hover:text-black px-3 py-2 rounded-lg transition duration-300 font-bold">Prácticas</a></li>
                <li><a href="logout.php" class="bg-yellow-500 px-4 py-2 rounded-lg text-black font-bold hover:bg-yellow-600 transition duration-300">Salir</a></li>
            </ul>
        </div>

        <!-- Menú desplegable en móviles -->
        <ul id="mobile-menu" class="hidden bg-black text-white flex-col space-y-4 p-4 lg:hidden font-bold">
            <li><a href="estudiantes_users.php" class="hover:bg-white hover:text-black px-3 py-2 rounded-lg transition duration-300 font-bold">Estudiantes</a></li>
            <li><a href="convenios_users.php" class="hover:bg-white hover:text-black px-3 py-2 rounded-lg transition duration-300 font-bold">Convenios</a></li>
            <li><a href="practicas_users.php" class="hover:bg-white hover:text-black px-3 py-2 rounded-lg transition duration-300 font-bold">Prácticas</a></li>
            <li><a href="logout.php" class="bg-yellow-500 px-4 py-2 rounded-lg text-black font-bold hover:bg-yellow-600 transition duration-300">Salir</a></li>
        </ul>
    </nav>

    <script>
        document.getElementById('menu-toggle').addEventListener('click', function () {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>

</body>

</html>