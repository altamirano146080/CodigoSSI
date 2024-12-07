<?php
include ("basededatos.php");
$conn = conexion();
session_start();
if (isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];
    // SQL para obtener listado de fotos del día
    $sql = "SELECT id, nombre, gmail, contrasena FROM final_usuarios ORDER BY id LIMIT 30";

    $stmt = mysqli_prepare($conn, $sql);

    // Comprobar si la preparación de la sentencia fue exitosa
    if ($stmt) {
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);

        $usuarioData = array(); // Inicializar un array para almacenar los datos de las imágenes

        while ($fila = mysqli_fetch_assoc($resultado)) {
        //$input = $_POST['comando'];
            // Comprobar si se envió un comando

            if ($fila["id"] != $user_id) {
                // Agregar los datos de la fila actual al array
                $usuarioData[] = array(
                    "usuario_id" => $fila["id"],
                    "usuario_nombre" => $fila["nombre"],
                    "usuario_gmail" => $fila["gmail"],
                    "usuario_contrasena" => $fila["contrasena"]
                );
            }

        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparando la consulta: " . mysqli_error($conn);
    }

    mysqli_close($conn);

    // Convertir el array de datos de las imágenes a JSON y enviarlo como respuesta
    echo json_encode($usuarioData);
}
else{
    echo("no ha iniciado sesion");
}

?>