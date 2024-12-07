<?php

$tiempo_vida = 300; // 5 minutos en segundos
session_set_cookie_params($tiempo_vida);


// Incluir el archivo de conexión a la base de datos
include("basededatos.php");

// Obtener datos del formulario
$email = $_POST['email'];
$contraseña = $_POST['contrasena'];
$es_administrador = isset($_POST['es_administrador']) && $_POST['es_administrador'] == 'on';
// Establecer conexión a la base de datos
$con = conexion();

// Consultar la base de datos de forma segura usando bindparams
//$sql = "SELECT * FROM final_usuarios WHERE gmail=?";
//$stmt = mysqli_prepare($con, $sql);
//mysqli_stmt_bind_param($stmt, "s", $email);
//mysqli_stmt_execute($stmt);
//$resultado = mysqli_stmt_get_result($stmt);
$sql = "SELECT * FROM final_usuarios WHERE gmail=".$email;
$resultado = mysqli_query($con, $sql);

//$sql = "SELECT * FROM final_administradores WHERE usuario_id=?";
//$stmt = mysqli_prepare($con, $sql);


if ($resultado && mysqli_num_rows($resultado) > 0) {
    //$sql = "SELECT * FROM final_administradores WHERE usuario_id=?";
    $fila = mysqli_fetch_assoc($resultado);
    print_r($fila)
    //mysqli_stmt_bind_param($stmt, "i", $fila['id']);
    //mysqli_stmt_execute($stmt);
    //$resultado2 = mysqli_stmt_get_result($stmt);

    $sql = "SELECT * FROM final_administradores WHERE usuario_id=" . $fila['id'];
    $resultado2 = mysqli_query($con, $sql);
    $fila2 = mysqli_fetch_assoc($resultado2);
    // Verificar contraseña
    if ($contraseña == $fila['contrasena']) {
        // Iniciar sesión
        session_start();
        // Iniciar sesión
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

            //$aux = file_get_contents("../plantilla/principal.html");
            //$aux = str_replace("##titulo##", "Login", $aux);
            //$aux = str_replace("##mensaje##", "Sesion iniciada correctamente", $aux);
            //echo $aux;
        }
    } else {
        echo '<script>
        document.getElementById("error_message").innerHTML = "El usuario o la contraseña son incorrectos";
        document.getElementById("error_message").style.display = "block";
      </script>';
        header("Location: ../ulio-html/iniciarSesion.html");
        exit;

        //echo "El usuario o contraseña no son correctos";
        
    }
} else {
    
}
?>

