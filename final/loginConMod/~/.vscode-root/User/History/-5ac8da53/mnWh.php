<?php

$tiempo_vida = 300; // 5 minutos en segundos
session_set_cookie_params($tiempo_vida);

// Incluir el archivo de conexión a la base de datos
include("basededatos.php");

// Obtener datos del formulario
$email = $_POST['email'];  // No se hace sanitización
$contraseña = md5($_POST['contrasena']);  // La contraseña se sigue convirtiendo a hash, pero no es relevante para la inyección SQL
$es_administrador = isset($_POST['es_administrador']) && $_POST['es_administrador'] == 'on';

// Establecer conexión a la base de datos
$con = conexion();

// Consulta vulnerable sin sanitización
$sql = "SELECT * FROM final_usuarios WHERE gmail='$email'";  // No se usan sentencias preparadas ni escape de caracteres
$resultado = mysqli_query($con, $sql);

// Verificamos si la consulta devolvió algún resultado
if ($resultado && mysqli_num_rows($resultado) > 0) {
    $fila = mysqli_fetch_assoc($resultado);

    // Ahora se busca en la tabla final_administradores
    $sql = "SELECT * FROM final_administradores WHERE usuario_id=" . $fila['id'];  // Vulnerable a inyección aquí también
    $resultado2 = mysqli_query($con, $sql);
    $fila2 = mysqli_fetch_assoc($resultado2);

    // Verificamos la contraseña
    if ($contraseña == $fila['contrasena']) {
        // Iniciar sesión
        session_start();
        $_SESSION['user'] = $fila['gmail'];
        $_SESSION['passwd'] = $fila['contrasena'];
        $_SESSION['tiempo'] = time();
        $_SESSION['id'] = $fila['id'];

        // Redirigir según el tipo de usuario
        if ($es_administrador && ($fila2['usuario_id'] && $fila['id']) == 1) {
            $_SESSION['tipo'] = 'administrador';
            header("Location: ../dashmin/menu.html");
            exit;
        } else {
            $_SESSION['tipo'] = 'usuario';
            echo '<script>
            document.getElementById("error_message").innerHTML = "El usuario o la contraseña son incorrectos";
            document.getElementById("error_message").style.display = "block";
          </script>';
            header("Location: ../ulio-html/index.html");
            exit;
        }
    } else {
        echo '<script>
        document.getElementById("error_message").innerHTML = "El usuario o la contraseña son incorrectos";
        document.getElementById("error_message").style.display = "block";
      </script>';
        header("Location: ../ulio-html/iniciarSesion.html");
        exit;
    }
} else {
    // No hay resultados para el correo
    echo '<script>
        document.getElementById("error_message").innerHTML = "El cuy usuario o la contraseña son incorrectos";
        document.getElementById("error_message").style.display = "block";
    </script>';
    header("Location: ../ulio-html/iniciarSesion.html");
    exit;
}
?>
