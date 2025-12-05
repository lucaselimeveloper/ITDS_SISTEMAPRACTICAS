<?php
session_start();
include('../config/db.php');

// Verificar si se enviaron credenciales
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener y limpiar datos del formulario
    $usuario = mysqli_real_escape_string($conexion, $_POST['usuario']);
    $contrasenia = mysqli_real_escape_string($conexion, $_POST['contrasenia']);

    // Consulta preparada para mayor seguridad
    $consulta = "SELECT * FROM usuarios WHERE usuario = ? AND contrasenia = ?";
    $stmt = mysqli_prepare($conexion, $consulta);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $usuario, $contrasenia);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($resultado) > 0) {
            $usuario_data = mysqli_fetch_assoc($resultado);
            
            // Configurar datos de sesión
            $_SESSION['usuario'] = $usuario_data['usuario'];
            $_SESSION['es_admin'] = $usuario_data['es_admin'];
            $_SESSION['id_usuario'] = $usuario_data['id'];
            
            // Redirección según tipo de usuario
            if ($usuario_data['es_admin'] == 1) {
                header('Location: ../home.php');
            } else {
                header('Location: ../views/user/practicas/practicas_users.php');
            }
            exit();
        } else {
            $_SESSION['error_login'] = "Usuario o contraseña incorrectos";
            header('Location: ../index.php');
            exit();
        }
        
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['error_login'] = "Error en el sistema. Intente nuevamente.";
        header('Location: ../index.php');
        exit();
    }
} else {
    header('Location: ../index.php');
    exit();
}

mysqli_close($conexion);
?>