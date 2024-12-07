<?php
include("basededatos.php");
$conn = conexion();

// Consulta SQL para obtener los títulos de las categorías junto con sus IDs
$sql = "
    SELECT DISTINCT fc.id_categories, fc.title
    FROM final_categories fc
    JOIN final_events fe ON fc.id_categories = fe.categories
";

$stmt = mysqli_prepare($conn, $sql);

// Comprobar si la preparación de la sentencia fue exitosa
if ($stmt) {
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    $categories = array(); // Inicializar un array para almacenar los datos de las categorías

    while ($fila = mysqli_fetch_assoc($resultado)) {
        // Agregar los datos de la categoría al array
        $categories[] = array(
            "id_categories" => $fila["id_categories"],
            "title" => $fila["title"]
        );
    }

    mysqli_stmt_close($stmt);
} else {
    echo "Error preparando la consulta: " . mysqli_error($conn);
}

mysqli_close($conn);

// Convertir el array de datos de las categorías a JSON y enviarlo como respuesta
echo json_encode($categories);
?>
