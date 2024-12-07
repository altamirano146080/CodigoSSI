<?php

    // Datos de conexión
    $server = "dbserver";
    $username = "grupo04"; // Reemplaza XX con el número de tu grupo
    $password = "IePhi3ooho"; // Aquí deberías obtener la contraseña de dbinfo.txt
    $database = "db_grupo04"; // Reemplaza XX con el número de tu grupo

    // Clave de API proporcionada por la NASA
    $api_key = "1xOH51vQqMa0GYtUbRXPCCG5StHvKmU99ZRrOGFI";
    $rovers = array('spirit','curiosity','opportunity');


    // URL para solicitar datos de la API de la NASA

    // Establecer conexión a la base de datos
    $conn = mysqli_connect($server, $username, $password, $database);

    // Verificar la conexión
    if (!$conn) {
        die("La conexión a la base de datos falló: " . mysqli_connect_error());
    }
    foreach($rovers as $rover){
        $request_url = "https://api.nasa.gov/mars-photos/api/v1/manifests/$rover?api_key=$api_key";
        // Realizar la solicitud GET
        $response = file_get_contents($request_url);

        // Decodificar la respuesta JSON
        $data = json_decode($response, true);

        // Verificar si se recibió la respuesta correctamente
        if ($data && isset($data['photo_manifest'])) {
            // Iterar sobre cada evento

            $name = $data['photo_manifest']['name'];
            $landing = $data['photo_manifest']['landing_date'];
            $launch = $data['photo_manifest']['launch_date'];
            $status = $data['photo_manifest']['status'];
            // Acceder a la categoría del evento
            $max_sol = $data['photo_manifest']['max_sol']; // Suponiendo que solo haya una categoría por evento
            $max_date = $data['photo_manifest']['max_date']; 
            $total_photos = $data['photo_manifest']['total_photos']; 
            // Acceder a la fuente del evento
            $sql = "INSERT INTO final_Rover (name,status, launch_date,landing_date, max_sol, max_date, total_photos) VALUES (?, ?, ?, ?, ?, ?, ?)";
            // Preparar la consulta
            $stmt = $conn->prepare($sql);


            // Vincular parámetros
            $stmt->bind_param("ssssisi", $name, $status, $launch, $landing, $max_sol, $max_date, $total_photos);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                $photos = $data['photo_manifest']['photos'];
                $photo_count = 0;

                foreach($photos as $photo){

                    $sol = $photo['sol'];
                    $photo_date = $photo['earth_date'];
                    $request_url2 = "https://api.nasa.gov/mars-photos/api/v1/rovers/$rover/photos?sol=$sol&api_key=$api_key";
                    // Realizar la solicitud GET
                    $response2 = file_get_contents($request_url2);

                    // Decodificar la respuesta JSON
                    $data2 = json_decode($response2, true);
                    if ($data2 && isset($data2['photos'])) {

                        foreach ($data2['photos'] as $foto) {
                            if ($photo_count >= 5000) {
                                break 2; // Salir de ambos bucles si se alcanzan las 10 fotos
                            }

                            $foto_id = $foto['id'];
                            $foto_url = $foto['img_src'];


                            // Acceder a la fuente del evento
                            $sql2 = "INSERT INTO final_Foto (photo_id, rover_id, photo_date, photo_url,sol) VALUES (?,?, ?, ?, ?)";
                            // Preparar la consulta
                            $stmt2 = $conn->prepare($sql2);
                            // Vincular parámetros
                            $stmt2->bind_param("isssi", $foto_id,$name,$photo_date,$foto_url,$sol);

                            if($stmt2->execute()){
                                $photo_count++;
                            }
                            else{
                                echo "mala insercion de fotos". $conn->error;
                            }
                            // Cerrar la declaración
                            $stmt2->close();
                        }
                    }
                }
            } else {
                echo "Error al insertar datos del rover '$name': " . $conn->error;
            }
            
            // Cerrar la declaración
            $stmt->close();


        } else {
            // Si no se pudo obtener la respuesta, mostrar un mensaje de error
            echo "No se pudo obtener los datos de la API de la NASA.";
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
    
?>