<?php
include("basededatos.php");// Crear conexión
$conn = conexion();

// Clave de API proporcionada por la NASA
$api_key = "1xOH51vQqMa0GYtUbRXPCCG5StHvKmU99ZRrOGFI";

// URL para solicitar datos de la API de la NASA
$request_url = "https://eonet.gsfc.nasa.gov/api/v2.1/events?";
// Establecer conexión a la base de datos


// Verificar la conexión
if (!$conn) {
    die("La conexión a la base de datos falló: " . mysqli_connect_error());
}

// Realizar la solicitud GET
$response = file_get_contents($request_url);

// Decodificar la respuesta JSON
$data = json_decode($response, true);

// Verificar si se recibió la respuesta correctamente
if ($data && isset($data['events'])) {

    // Iterar sobre cada evento
    foreach ($data['events'] as $evento) {
        $apod_id_evento = $evento['id'];
        $apod_title = $evento['title'];
        $apod_description = $evento['description'];
        $apod_link = $evento['link'];
        // Acceder a la categoría del evento
        $apod_categories = $evento['categories'][0]['id']; // Suponiendo que solo haya una categoría por evento
        // Acceder a la fuente del evento

        // Aquí se realizaría la inserción en la base de datos para cada evento...

        // Preparar la consulta SQL para verificar la existencia de las entradas en final_source_links
        $check_source_query = "SELECT * FROM final_source_links WHERE id_event = '$apod_id_evento'";
        $source_result = mysqli_query($conn, $check_source_query);

        if (mysqli_num_rows($source_result) == 0) {
            // No existen entradas en final_source_links para este evento, entonces podemos proceder con la inserción

            // Preparar la consulta SQL para insertar los datos en la tabla final_events
            $sql_event = "INSERT INTO final_events (id_evento, title, description, link, categories) VALUES ('$apod_id_evento', '$apod_title', '$apod_description', '$apod_link', '$apod_categories')";

            // Ejecutar la consulta SQL para insertar en final_events
            if (mysqli_query($conn, $sql_event)) {
                // Obtener las fuentes del evento
                $sources = $evento['sources'];
                foreach ($sources as $source) {
                    $apod_id_source = $source['id'];
                    $apod_source_link = $source['url'];
                    // Preparar la consulta SQL para insertar los datos en la tabla final_source_links
                    $sql_source = "INSERT INTO final_source_links (link,id_source,id_event) VALUES ('$apod_source_link', '$apod_id_source', '$apod_id_evento')";
                    // Ejecutar la consulta SQL para insertar en final_source_links
                    if (mysqli_query($conn, $sql_source)) {
                        echo "source_link bien metido";
                    } else {
                        echo "Error al insertar source_link: " . mysqli_error($conn);
                    }
                }
                // Insertar geometrías si las hay
                if (isset($evento['geometries'])) {
                    $geometries = $evento['geometries'];
                    foreach ($geometries as $geometry) {
                        $geo = json_encode($geometry); 
                        echo $geo;
                        // Preparar la consulta SQL para insertar los datos en la tabla final_geometries
                        $sql_geometry = "INSERT INTO final_geometries (event_id, geometry) VALUES ('$apod_id_evento', '$geo')";
                        // Ejecutar la consulta SQL para insertar en final_geometries
                        if(mysqli_query($conn, $sql_geometry)) {
                            echo "geometria bien metido";
                        } else {
                            echo "Error al insertar geometria: " . mysqli_error($conn);
                        }
                    }
                }
                echo "Los datos se han insertado correctamente en la tabla final_events.";
            } else {
                echo "Error al insertar los datos en final_events: " . mysqli_error($conn);
            }
        } else {
            echo "Las entradas para el evento con id $apod_id_evento ya existen en final_source_links.";
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

    // Cerrar la conexión a la base de datos
    mysqli_close($conn);
} else {
    // Si no se pudo obtener la respuesta, mostrar un mensaje de error
    echo "No se pudo obtener los datos de la API de la NASA.";
}
?>
