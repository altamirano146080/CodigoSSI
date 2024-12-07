<?php
include ("basededatos.php"); // Asegúrate de incluir tu archivo de conexión a la base de datos


// Función para cambiar la contraseña
function cambiarContraseña($usuario_id, $newPassword)
{
    $con = conexion(); // Conectar con la base de datos

    // Escapar las variables para evitar inyección SQL
    $usuario_id = mysqli_real_escape_string($con, $usuario_id);
    $newPassword = mysqli_real_escape_string($con, $newPassword);

    // Consulta para actualizar la contraseña del usuario
    $query = "UPDATE final_usuarios SET contrasena = ? WHERE id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "si", $newPassword, $usuario_id);

    // Ejecutar la consulta
    if (mysqli_stmt_execute($stmt)) {
        // Contraseña actualizada correctamente
        return true;
    } else {
        // Error al actualizar la contraseña
        return false;
    }

    // Cerrar la conexión
    //mysqli_close($con);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se han enviado todos los campos necesarios
    if (isset($_POST['usuarioId']) && isset($_POST['newPassword'])) {
        // Recuperar los datos del formulario
        $usuario_id = $_POST['usuarioId']; // Cambio aquí
        $newPassword = md5($_POST['newPassword']);

        // Llamar a la función para cambiar la contrasena
        if (cambiarContraseña($usuario_id, $newPassword)) {
            echo "Contraseña cambiada correctamente. Nueva contraseña: " . $newPassword;
            //echo "Contraseña cambiada correctamente.";
        } else {
            echo "Error al cambiar la contraseña.";
        }

    } else {
        echo "Todos los campos son obligatorios.";
    }
}

?>