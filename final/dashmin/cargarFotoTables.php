<?php
include("basededatos.php");
$conn = conexion();


// SQL para obtener listado de fotos del día
$sql = "SELECT * FROM final_Foto LIMIT 5" ;

$stmt = mysqli_prepare($conn, $sql);


// Comprobar si la preparación de la sentencia fue exitosa
if ($stmt) {
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    $usuarioData = array(); // Inicializar un array para almacenar los datos de las imágenes

    while ($fila = mysqli_fetch_assoc($resultado)) {
        // Agregar los datos de la fila actual al array
        $usuarioData[] = array(
            "photo_id"  => $fila["photo_id"],
            "rover_id"  => $fila["rover_id"],
            "photo_date"  => $fila["photo_date"],
            "photo_url"  => $fila["photo_url"],
            "sol"  => $fila["sol"]
        );
    }

    mysqli_stmt_close($stmt);
} else {
    echo "Error preparando la consulta: " . mysqli_error($conn);
}

mysqli_close($conn);

// Convertir el array de datos de las imágenes a JSON y enviarlo como respuesta
echo json_encode($usuarioData);