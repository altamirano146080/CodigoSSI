<?php
$servidor = "dbserver";
$bd = "db_grupo04";
$user = "grupo04";
$password = "IePhi3ooho";

$conn = mysqli_connect($servidor, $user, $password, $bd);

// Verificar conexión
if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// SQL para obtener la foto más reciente
$sql = "SELECT hd_url, explanation FROM final_apod_images ORDER BY date DESC LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);

// Comprobar si la preparación de la sentencia fue exitosa
if ($stmt) {
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if ($fila = mysqli_fetch_assoc($resultado)) {
        // Asumiendo que 'explanation' es la descripción correcta en tu base de datos
        $imageData = array("url" => $fila["hd_url"], "descripcion" => $fila["explanation"]);
    } else {
        $imageData = array("url" => "ruta_a_una_imagen_predeterminada.jpg", "descripcion" => "No hay descripción disponible");
    }

    mysqli_stmt_close($stmt);
} else {
    echo "Error preparando la consulta: " . mysqli_error($conn);
}

mysqli_close($conn);

echo json_encode($imageData);
?>
