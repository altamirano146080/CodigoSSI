<?php
include ("basededatos.php");
$conn = conexion();
session_start();
if (isset($_SESSION['id'])) {
    $usuario_id = $_SESSION['id'];   
    $busquedagmail = $_GET['busqueda_gmail'];
    $busquedagmail = '%' . $busquedagmail . '%';

    // SQL para obtener listado de fotos del día
    $sql = "SELECT id, nombre, gmail, contraseña FROM final_usuarios WHERE gmail LIKE ? ORDER BY id";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $busquedagmail);


    // Comprobar si la preparación de la sentencia fue exitosa
    if ($stmt) {
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);

        $usuarioData = array(); // Inicializar un array para almacenar los datos de las imágenes

        while ($fila = mysqli_fetch_assoc($resultado)) {
            // Agregar los datos de la fila actual al array
            if($usuario_id != $fila["id"]){
                $usuarioData[] = array(
                    "usuario_id" => $fila["id"],
                    "usuario_nombre" => $fila["nombre"],
                    "usuario_gmail" => $fila["gmail"],
                    "usuario_contrasena" => $fila["contraseña"]
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
    echo ("no ha iniciado sesion");
}

?>