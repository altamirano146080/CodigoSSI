<?php
include ("basededatos.php");
session_start();


function obtener($usuario_id)
{
    $con = conexion(); // Conectar con la base de datos

    // Escapar las variables para evitar inyección SQL
    $usuario_id = mysqli_real_escape_string($con, $usuario_id);
    $query = "SELECT nombre,gmail,foto FROM final_usuarios WHERE id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "s", $usuario_id);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    // Ejecutar la consulta
    if ($resultado && mysqli_num_rows($resultado) > 0) {

        $usuario = array(
            "nombre" => $fila['nombre'],
            "correo" => $fila['gmail'],
            "foto" => $fila['foto']
            // Otras propiedades del usuario, como la ruta de la imagen de perfil, etc.
        );
        return $usuario;
        
    }
    return false;


    // Cerrar la conexión
    //mysqli_close($con);
}
if (isset($_SESSION['id'])) {
    // Verificar si se ha enviado el formulario

    // Recuperar los datos del formulario
    $usuario_id = $_SESSION['id']; // Por ejemplo, puedes obtener el ID del usuario de la sesión actual
    $usuario = obtener($usuario_id);
    // Aquí obtienes los datos del usuario desde tu base de datos o de donde sea necesario


    // Devuelve los datos del usuario como JSON
    echo json_encode($usuario);

}


?>