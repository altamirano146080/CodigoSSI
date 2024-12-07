<?php 
	include "basededatos.php";

	$con = conexion();

	$consulta = "select * from final_usuarios";
	$resultado = $con->query($consulta);

	$cadena = file_get_contents("plantillausuario.html");
	$trozos = explode("##fila##", $cadena);

	$cuerpo = "";
	while ($datos = $resultado->fetch_assoc()) {
		$aux = $trozos[1];
		$aux = str_replace("##idusuario##", $datos["id"], $aux);
		$aux = str_replace("##gmail##", $datos["gmail"], $aux);
		$cuerpo .= $aux;
	}

	echo $trozos[0] . $cuerpo . $trozos[2];
?>