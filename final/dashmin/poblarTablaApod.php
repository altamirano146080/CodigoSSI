<?php
/*
include("basededatos.php");
// Crear conexión
$conn = conexion();

// Clave de API proporcionada por la NASA
$api_key = "1xOH51vQqMa0GYtUbRXPCCG5StHvKmU99ZRrOGFI";

// Fechas de inicio y fin del rango (puedes editarlas)
$start_date = "2023-01-01";
$end_date = date("Y-m-d"); // Fecha actual

// URL para solicitar datos de la API de la NASA con el rango de fechas especificado
$request_url = "https://api.nasa.gov/planetary/apod?api_key=$api_key&start_date=$start_date&end_date=$end_date";

// Realizar la solicitud GET
$response = file_get_contents($request_url);

// Decodificar la respuesta JSON
$data = json_decode($response, true);

// Verificar si se recibió la respuesta correctamente
if ($data && is_array($data)) {
    // Array para almacenar los datos de las imágenes de APOD
    $apod_images_data = [];

    // Iterar sobre cada imagen recibida
    foreach ($data as $image) {
        // Agregar los datos de la imagen a $apod_images_data
        $apod_images_data[] = [
            'date' => $image['date'],
            'title' => $image['title'],
            'explanation' => $image['explanation'],
            'url' => $image['url'],
            'hd_url' => isset($image['hdurl']) ? $image['hdurl'] : '', // Algunas imágenes pueden no tener URL de alta definición
            'media_type' => $image['media_type']
        ];
    }


    // Verificar la conexión
    if (!$conn) {
        die("La conexión a la base de datos falló: " . mysqli_connect_error());
    }

    foreach ($apod_images_data as $apod_image_data) {
        $date = mysqli_real_escape_string($conn, $apod_image_data['date']);
        $title = mysqli_real_escape_string($conn, $apod_image_data['title']);
        $explanation = mysqli_real_escape_string($conn, $apod_image_data['explanation']);
        $url = mysqli_real_escape_string($conn, $apod_image_data['url']);
        $hd_url = mysqli_real_escape_string($conn, $apod_image_data['hd_url']);
        $media_type = mysqli_real_escape_string($conn, $apod_image_data['media_type']);
    
        // Preparar la consulta SQL para insertar los datos en la tabla apod_images
        $sql = "INSERT INTO final_apod_images (date, title, explanation, url, hd_url, media_type) VALUES ('$date', '$title', '$explanation', '$url', '$hd_url', '$media_type')";
    
        // Ejecutar la consulta SQL
        if (mysqli_query($conn, $sql)) {
            echo "Los datos de la imagen de APOD para la fecha $date se han insertado correctamente en la tabla apod_images.<br>";
        } else {
            echo "Error al insertar los datos para la fecha $date: " . mysqli_error($conn) . "<br>";
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
    echo "Error al obtener datos de la API de la NASA.";
}
*/

include("basededatos.php");
// Crear conexión
$conn = conexion();

// Clave de API proporcionada por la NASA
$api_key = "1xOH51vQqMa0GYtUbRXPCCG5StHvKmU99ZRrOGFI";

// Fechas de inicio y fin del rango (puedes editarlas)
$start_date = "2023-01-01";
$end_date = date("Y-m-d"); // Fecha actual

// URL para solicitar datos de la API de la NASA con el rango de fechas especificado
$request_url = "https://api.nasa.gov/planetary/apod?api_key=$api_key&start_date=$start_date&end_date=$end_date";

// Realizar la solicitud GET
$response = file_get_contents($request_url);

// Decodificar la respuesta JSON
$data = json_decode($response, true);

// Verificar si se recibió la respuesta correctamente
if ($data && is_array($data)) {
    // Verificar la conexión
    if (!$conn) {
        die("La conexión a la base de datos falló: " . mysqli_connect_error());
    }

    // Iterar sobre cada imagen recibida y procesarla
    foreach ($data as $image) {
        $date = mysqli_real_escape_string($conn, $image['date']);
        $title = mysqli_real_escape_string($conn, $image['title']);
        $explanation = mysqli_real_escape_string($conn, $image['explanation']);
        $url = mysqli_real_escape_string($conn, $image['url']);
        $hd_url = isset($image['hdurl']) ? mysqli_real_escape_string($conn, $image['hdurl']) : '';
        $media_type = mysqli_real_escape_string($conn, $image['media_type']);

        // Preparar la consulta SQL para insertar los datos en la tabla apod_images
        $sql = "INSERT INTO final_apod_images (date, title, explanation, url, hd_url, media_type) VALUES ('$date', '$title', '$explanation', '$url', '$hd_url', '$media_type')";

        // Ejecutar la consulta SQL
        if (mysqli_query($conn, $sql)) {
            echo "Los datos de la imagen de APOD para la fecha $date se han insertado correctamente en la tabla apod_images.<br>";
        } else {
            echo "Error al insertar los datos para la fecha $date: " . mysqli_error($conn) . "<br>";
        }
    }

    // Actualizar la fecha de actualización
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
    echo "Error al obtener datos de la API de la NASA.";
}


?>
