<?php
include ("basededatos.php");
$conn = conexion();

$idFoto = $_GET['fotoId'];

$sql = "SELECT * FROM final_apod_images WHERE id =".$idFoto;
//$stmt = mysqli_prepare($conn, $sql);
//mysqli_stmt_bind_param($stmt, 's', $idFoto);
//if($resultado=mysqli_query($conn,$sql)){

//}
if($resultado=mysqli_query($conn,$sql)){
    //mysqli_stmt_execute($stmt);
    //$resultado = mysqli_stmt_get_result($stmt);

    if ($fila = mysqli_fetch_assoc($resultado)) {
        if ($fila["media_type"] == "video") {
            $url = $fila["url"];
            $descripcion = $fila["explanation"];
            $date = $fila["date"];
            $title = $fila["title"];


            $cadena = file_get_contents("masInformacionFotoDia.html");
            $trozos = explode("##fila##", $cadena);

            $cuerpo = "";
            $aux = $trozos[1];
            $cuy = '<iframe id="video" width="560" height="315" src="'.$url.'"
            title="YouTube video player" frameborder="0" 
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
            referrerpolicy="strict-origin-when-cross-origin" 
            allowfullscreen
            ></iframe>';
    

            $aux = str_replace("##imagen##", $cuy, $aux);
            $aux = str_replace("##descripcion##", $descripcion, $aux);
            $aux = str_replace("##fecha##", $date, $aux);
            $aux = str_replace("##titulo##", $title, $aux);

            $cuerpo .= $aux;

            echo $trozos[0] . $cuerpo . $trozos[2];
        } else {
            $url = $fila["hd_url"];
            $descripcion = $fila["explanation"];
            $date = $fila["date"];
            $title = $fila["title"];


            $cadena = file_get_contents("masInformacionFotoDia.html");
            $trozos = explode("##fila##", $cadena);

            $cuerpo = "";
            $aux = $trozos[1];
            $cuy = '<img src="'.$url.'" alt="Imagen Descriptiva" id="imagen" >';

            $aux = str_replace("##imagen##", $cuy, $aux);

            $aux = str_replace("##descripcion##", $descripcion, $aux);
            $aux = str_replace("##fecha##", $date, $aux);
            $aux = str_replace("##titulo##", $title, $aux);
            $cuerpo .= $aux;

            echo $trozos[0] . $cuerpo . $trozos[2];
        }

    } else {
        echo "No hay resultados para el ID proporcionado.";
    }

    mysqli_stmt_close($stmt);
} else {
    echo "Error preparando la consulta: " . mysqli_error($conn);
}

mysqli_close($conn);

?>