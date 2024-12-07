<?php
include("basededatos.php");
$conn = conexion();


// SQL para obtener listado de fotos del día
$sql = "SELECT * FROM final_categories" ;

$stmt = mysqli_prepare($conn, $sql);


// Comprobar si la preparación de la sentencia fue exitosa
if ($stmt) {
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    $usuarioData = array(); // Inicializar un array para almacenar los datos de las imágenes

    while ($fila = mysqli_fetch_assoc($resultado)) {
        // Agregar los datos de la fila actual al array
        $usuarioData[] = array(
            "id_categories"  => $fila["id_categories"],
            "title"  => $fila["title"],
            "link"  => $fila["link"],
            "description"  => $fila["description"],
            "layers"  => $fila["layers"]
        );
    }

    mysqli_stmt_close($stmt);
} else {
    echo "Error preparando la consulta: " . mysqli_error($conn);
}

mysqli_close($conn);

// Convertir el array de datos de las imágenes a JSON y enviarlo como respuesta
echo json_encode($usuarioData);