<?php
include("basededatos.php");
$conn = conexion();

$categorieId = $_GET['categorieId'];
$pagina = $_GET['pagina'];
$numerolineas = $_GET['numerolineas'];

$offset = ($pagina - 1) * $numerolineas; // Calcular el offset para la paginación

// Consulta SQL para obtener los eventos con el JOIN para obtener el título de la categoría
$sql = "
    SELECT fe.id_evento, fe.title AS evento_title, fe.description, fe.link, fc.title AS category_title
    FROM final_events fe
    JOIN final_categories fc ON fe.categories = fc.id_categories
    WHERE fe.categories = ?
    LIMIT ? OFFSET ?
";
$stmt = mysqli_prepare($conn, $sql);

// Pasar los parámetros por referencia utilizando el operador &
mysqli_stmt_bind_param($stmt, 'sii', $categorieId, $numerolineas, $offset);

if ($stmt) {
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    $eventos = array(); // Inicializar un array para almacenar los datos de los eventos

    while ($fila = mysqli_fetch_assoc($resultado)) {
        // Verificar si la descripción está vacía y asignar "Sin descripción" si es el caso
        $descripcion = !empty($fila["description"]) ? $fila["description"] : "Sin descripción";
        
        // Agregar los datos de la fila actual al array
        $eventos[] = array(
            "eventoId"  => $fila["id_evento"],
            "title"  => $fila["evento_title"],
            "description"  => $descripcion,
            "link"  => $fila["link"],
            "categories"  => $fila["category_title"]
        );
    }

    // Obtener el número total de eventos para la paginación
    $sqlTotal = "
        SELECT COUNT(*) AS total 
        FROM final_events fe
        JOIN final_categories fc ON fe.categories = fc.id_categories
        WHERE fe.categories = ?
    ";
    $stmtTotal = mysqli_prepare($conn, $sqlTotal);
    mysqli_stmt_bind_param($stmtTotal, 's', $categorieId);
    mysqli_stmt_execute($stmtTotal);
    $resultadoTotal = mysqli_stmt_get_result($stmtTotal);
    $filaTotal = mysqli_fetch_assoc($resultadoTotal);
    $totalEventos = $filaTotal['total'];

    $totalPaginas = ceil($totalEventos / $numerolineas);

    mysqli_stmt_close($stmt);
    mysqli_stmt_close($stmtTotal);
} else {
    echo "Error preparando la consulta: " . mysqli_error($conn);
}

mysqli_close($conn);

// Convertir el array de datos de los eventos a JSON y enviarlo como respuesta
echo json_encode(array("data" => $eventos, "totalPages" => $totalPaginas));
?>
