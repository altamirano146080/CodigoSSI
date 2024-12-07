<?php
include ("basededatos.php");
session_start();
function cambiarContraseña($usuario_id, $contraseña_nueva, $contraseña_actual)
{
    $con = conexion(); // Conectar con la base de datos

    // Escapar las variables para evitar inyección SQL
    $usuario_id = mysqli_real_escape_string($con, $usuario_id);
    $contraseña_nueva = mysqli_real_escape_string($con, $contraseña_nueva);
    $query = "SELECT contraseña FROM final_usuarios WHERE id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "s", $usuario_id);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    // Ejecutar la consulta
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        if ($fila['contraseña'] == $contraseña_actual) {
            // Consulta para actualizar la contraseña del usuario
            $query = "UPDATE final_usuarios SET contraseña = ? WHERE id = ?";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "si", $contraseña_nueva ,$usuario_id);
            

            // Ejecutar la consulta
            if (mysqli_stmt_execute($stmt)) {
                // Contraseña actualizada correctamente
                return true;
            } else {
                // Error al actualizar la contraseña
                return false;
            }
        } else {
            // Error al actualizar la contraseña
            return false;
        }
    }
    return false;


    // Cerrar la conexión
    //mysqli_close($con);
}
if (isset($_SESSION['id'])) {
    // Verificar si se ha enviado el formulario
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Verificar si se han enviado todos los campos necesarios
        if (isset($_POST['currentPassword']) && isset($_POST['newPassword']) && isset($_POST['confirmNewPassword'])) {
            // Recuperar los datos del formulario
            $usuario_id = $_SESSION['id']; // Por ejemplo, puedes obtener el ID del usuario de la sesión actual
            $contraseña_actual = md5($_POST['currentPassword']);
            $contraseña_nueva = md5($_POST['newPassword']);
            $confirmar_contraseña_nueva = md5($_POST['confirmNewPassword']);

            // Verificar si la nueva contraseña y la confirmación coinciden
            if ($contraseña_nueva === $confirmar_contraseña_nueva) {
                // Verificar si la contraseña actual es correcta (puedes implementar tu propia lógica aquí)
                // En este ejemplo, la contraseña actual siempre se considera correcta
                if (cambiarContraseña($usuario_id, $contraseña_nueva, $contraseña_actual)) {
                    echo "Contraseña cambiada correctamente.";
                } else {
                    echo "Error al cambiar la contraseña.";
                }
            } else {
                echo "La nueva contraseña y la confirmación no coinciden.";
            }
        } else {
            echo "Todos los campos son obligatorios.";
        }
    }
}

?>