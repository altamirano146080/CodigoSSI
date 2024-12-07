<?php
include ("basededatos.php");

$conn = conexion(); // Conectar con la base de datos


// Consulta para obtener la última fecha de actualización
$sql = "SELECT update_time FROM final_fecha_actualizacion ORDER BY id DESC LIMIT 1";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $last_update_time = $row["update_time"];
} else {
    $last_update_time = "No disponible";
}

// Cerrar la conexión
mysqli_close($conn);

// Devolver la última fecha de actualización
echo $last_update_time;
?>
