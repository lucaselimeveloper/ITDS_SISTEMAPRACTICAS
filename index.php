<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
    <!--CDN de tailwind-->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<!--Cuerpo de LOGIN-->

<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white shadow-md rounded-lg p-8 w-full max-w-sm">
        <h1 class="text-2xl font-bold text-center mb-6 text-gray-800">Iniciar Sesión</h1>
        <img src="resources/img/logo_ITDS.png" alt="">
        <!-- Formulario -->
        <form action="auth/validar.php" method="POST" class="space-y-4">
            <!-- Usuario -->
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Usuario</label>
                <input type="text" id="usuario" name="usuario"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    placeholder="Escribe tu usuario" required>
            </div>
            <!-- contraseña -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                <input type="password" id="contrasenia" name="contrasenia"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    placeholder="Escribe tu contraseña" required>
            </div>
            <!-- boton enviar -->
            <div>
                <button type="submit"
                    class="w-full bg-black hover:bg-slate-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-75">
                    Iniciar Sesión
                </button>
            </div>
        </form>
    </div>

</body>

</html>