<?php
include ("basededatos.php"); // Asegúrate de incluir tu archivo de conexión a la base de datos

// Función para eliminar un usuario
function eliminarUsuario($usuario_id)
{
    $con = conexion(); // Conectar con la base de datos

    // Escapar la variable para evitar inyección SQL
    $usuario_id = mysqli_real_escape_string($con, $usuario_id);

    // Consulta para eliminar el usuario
    $query = "DELETE FROM final_usuarios WHERE id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $usuario_id);

    // Ejecutar la consulta
    if (mysqli_stmt_execute($stmt)) {
        // Usuario eliminado correctamente
        return true;
    } else {
        // Error al eliminar el usuario
        return false;
    }

    // Cerrar la conexión
    //mysqli_close($con);
}

// Verificar si se ha enviado el ID del usuario a eliminar
if (isset($_POST['usuarioId'])) {
    // Recuperar el ID del usuario desde la solicitud
    $usuario_id = $_POST['usuarioId'];

    // Llamar a la función para eliminar el usuario
    if (eliminarUsuario($usuario_id)) {
        echo "Usuario eliminado correctamente.";
    } else {
        echo "Error al eliminar el usuario.";
    }
} else {
    echo "ID de usuario no proporcionado.";
}
?>
