<?php

$tiempo_vida = 300; // 5 minutos en segundos
session_set_cookie_params($tiempo_vida);

// Incluir el archivo de conexión a la base de datos
include("basededatos.php");

// Obtener datos del formulario
$email = $_POST['email'];
$contraseña = $_POST['contrasena'];
$es_administrador = isset($_POST['es_administrador']) && $_POST['es_administrador'] == 'on';

// Comprobar si las variables están vacías y evitar inyecciones
if (empty($email) || empty($contraseña)) {
    echo "El email o la contraseña están vacíos.";
    exit;
}

// Establecer conexión a la base de datos
$con = conexion();

$email = mysqli_real_escape_string($con, $email);
$contraseña = mysqli_real_escape_string($con, $contraseña);



$sql = "SELECT * FROM final_usuarios WHERE gmail='$email'";
$resultado = mysqli_query($con, $sql);

if ($resultado && mysqli_num_rows($resultado) > 0) {
    $fila = mysqli_fetch_assoc($resultado);
    $sql = "SELECT * FROM final_administradores WHERE usuario_id=" . $fila['id'];
    $resultado2 = mysqli_query($con, $sql);
    $fila2 = mysqli_fetch_assoc($resultado2);

    // Verificar contraseña
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
            header("Location: ../ulio-html/index.html");
            exit;
        }
    } else {
        // Contraseña incorrecta
        header("Location: ../ulo-html/iniciarSesion.html");
        exit;
    }
} else {
    // Usuario no encontrado
    header("Location: ../ulo-html/iniciarSesion.html");
    exit;
}
?>
