<?php
include("basededatos.php");
$conn = conexion();

$valid_tables = ['final_apod_images', 'final_categories', 'final_events', 'final_Foto', 'final_source'];  

// Obtener y sanitizar el nombre de la tabla
$table = $_POST['table'];

if (!in_array($table, $valid_tables)) {
    echo "Nombre de tabla no válido.";
    exit;
}

$sql = "DELETE FROM $table";

// Ejecutar la consulta para vaciar la tabla principal
if ($conn->query($sql) === TRUE) {
    echo "Tabla '$table' vaciada correctamente.";
    // Si la tabla es 'final_events', vaciar también 'final_geometries'
    if ($table === 'final_events') {
        $sql2 = "DELETE FROM final_geometries";
        $sql3 = "DELETE FROM final_source_links";

        if ($conn->query($sql2) === TRUE) {
            echo "Tabla 'final_geometries' vaciada correctamente.";
        } else {
            echo "Error vaciando la tabla 'final_geometries': " . $conn->error;
        }

        if ($conn->query($sql3) === TRUE) {
            echo "Tabla 'final_source_links' vaciada correctamente.";
        } else {
            echo "Error vaciando la tabla 'final_source_links': " . $conn->error ;
        }
    }
    else if($table === 'final_Foto'){
        $sql4 = "DELETE FROM final_Rover";

        if ($conn->query($sql4) === TRUE) {
            echo "Tabla 'final_Rover' vaciada correctamente.";
        } else {
            echo "Error vaciando la tabla 'final_Rover': " . $conn->error;
        }

    }
    $sqlupdate = "INSERT INTO final_fecha_actualizacion(id, update_time) VALUES (1, NOW())
        ON DUPLICATE KEY UPDATE update_time = NOW()";

    // Ejecutar la consulta SQL
    if (mysqli_query($conn, $sqlupdate)) {
        echo "La fecha de actualización se ha registrado correctamente.";
    } else {
        echo "Error al registrar la fecha de actualización: " . mysqli_error($conn);
    }
} else {
    echo "Error vaciando la tabla '$table': " . $conn->error;
}

mysqli_close($conn);
?>