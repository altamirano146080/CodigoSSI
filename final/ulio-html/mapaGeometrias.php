<?php
include ("basededatos.php");
$conn = conexion();

$id = $_GET['EONET_id'];
$titulo = $_GET['titulo'];
$sql = "SELECT geometry FROM final_geometries WHERE event_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 's', $id);

if ($stmt) {
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    // Inicializar un array para almacenar las geometrías
    $geometriasArray = [];

    // Iterar sobre los resultados y almacenar las geometrías en el array
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $geometria = json_decode($fila["geometry"]); // Decodificar la geometría
        if ($geometria !== null) {
            $geometriasArray[] = $geometria; // Añadir la geometría al array
        } else {
            echo "Error al decodificar una de las geometrías JSON.";
        }
    }

    // Cerrar la consulta
    mysqli_stmt_close($stmt);

    // Si hay geometrías, generar el código del mapa
    if (!empty($geometriasArray)) {
        // Cargar el contenido del archivo HTML
        $cadena = file_get_contents("mapaGeometrias.html");
        $trozos = explode("##fila##", $cadena);

        $cuerpo = "";
        $aux = $trozos[1];

        // Reemplazar los marcadores en el HTML
        $aux = str_replace("##titulo##", $titulo, $aux);

        // Generar el código JavaScript para añadir las geometrías al mapa
        $mapaScript = "<script>
            document.addEventListener('DOMContentLoaded', function() {
                const map = L.map('map').setView([0, 0], 2);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors'
                }).addTo(map);

                // Función para añadir un punto al mapa
                function addPointToMap(coordinates) {
                    L.marker(coordinates).addTo(map)
                        .bindPopup('Coordenadas: ' + coordinates)
                        .openPopup();
                }

                // Añadir las geometrías al mapa
                const geometrias = " . json_encode($geometriasArray) . ";
                geometrias.forEach(geo => {
                    if (geo.type === 'Point') {
                        const coordinates = [geo.coordinates[1], geo.coordinates[0]]; // Leaflet espera [lat, lng]
                        addPointToMap(coordinates);
                    }
                });
            });
        </script>";

        $aux = str_replace("##mapa##", $mapaScript, $aux);
        $cuerpo .= $aux;

        echo $trozos[0] . $cuerpo . $trozos[2];
    } else {
        echo "No se encontraron geometrías para el ID proporcionado.";
    }
} else {
    echo "Error preparando la consulta: " . mysqli_error($conn);
}

// Cerrar la conexión
mysqli_close($conn);
?>
