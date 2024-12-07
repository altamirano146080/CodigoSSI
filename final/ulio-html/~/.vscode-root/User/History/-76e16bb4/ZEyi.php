<?php
include("basededatos.php");
$conn = conexion();

$roverId = $_GET['rover_id'];
$pagina = $_GET['pagina'];
$numerolineas = $_GET['numerolineas'];
$offset = ($pagina - 1) * $numerolineas;
// SQL para obtener listado de fotos del día
$sql = "SELECT photo_id, rover_id, photo_date, photo_url, sol FROM final_Foto  WHERE rover_id = ? LIMIT ? OFFSET ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'sii', $roverId, $numerolineas, $offset);

// Comprobar si la preparación de la sentencia fue exitosa
if ($stmt) {
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    $imageData = array(); // Inicializar un array para almacenar los datos de las imágenes

    while ($fila = mysqli_fetch_assoc($resultado)) {
        // Agregar los datos de la fila actual al array
        $imageData[] = array(
            "fotoId"  => $fila["photo_id"],
            "roverId"  => $fila["rover_id"],
            "title"  => $fila["photo_date"],
            "fotoURL"  => $fila["photo_url"],
            "sol"  => $fila["sol"]
        );
    }
    $sqlTotal = "SELECT COUNT(*) AS total from final_Foto WHERE rover_id = ?";
    $stmtTotal = mysqli_prepare($conn, $sqlTotal);
    mysqli_stmt_bind_param($stmtTotal, 's', $roverId);
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


echo json_encode(array("data" => $imageData, "totalPages" => $totalPaginas));
?>