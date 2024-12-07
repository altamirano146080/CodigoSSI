<?php

session_start();
require_once("basededatos.php");

// Función para comprobar el usuario y gestionar el tiempo de sesión
function comprobarusuario() {
    $tiempo_vida = 300; // 5 minutos en segundos

    // Verificar si todas las variables de sesión necesarias están establecidas
    if(isset($_SESSION['user'], $_SESSION['passwd'], $_SESSION['tiempo'])) {//, $_SESSION['tipo']
        $user = $_SESSION['user'];
        $passwd = $_SESSION['passwd'];
        $tiempo1 = $_SESSION['tiempo'];
        //$tipo = $_SESSION['tipo'];

        // Verificar el tiempo transcurrido desde la última actividad
        $tiempo2 = time();
        $diferencia = $tiempo2 - $tiempo1;

        if ($diferencia > $tiempo_vida) {
            // Se ha acabado el tiempo, destruir la sesión
            unset($_SESSION['tiempo']);
            unset($_SESSION['user']);
            unset($_SESSION['passwd']);
            session_destroy();
            
            return false;
        } else {
            // Actualizar el tiempo de sesión
            $_SESSION['tiempo'] = $tiempo2;

            // Consultar la base de datos para verificar la autenticación del usuario
            $con = conexion();
            $consulta = "SELECT * FROM final_usuarios WHERE gmail=?";
            $stmt = mysqli_prepare($con, $consulta);
            mysqli_stmt_bind_param($stmt, "s", $user);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);
            $datos = mysqli_fetch_assoc($resultado);

            if ($datos['contrasena'] === $passwd) {//
            
                return true;
            } else {
                // Las credenciales son inválidas, destruir la sesión
                unset($_SESSION['tiempo']);
                unset($_SESSION['user']);
                unset($_SESSION['passwd']);
                session_destroy();
                
                return false;
            }
        }
    } else {
        // Faltan variables de sesión, destruir la sesión
        unset($_SESSION['tiempo']);
        unset($_SESSION['user']);
        unset($_SESSION['passwd']);
        session_destroy();
       
        return false;
    }
}

?>
