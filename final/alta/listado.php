<?php

	include "basededatos.php";
	$con = conexion();

	$stmt = $con->prepare("select * from personas");
	$stmt->execute();
	$resultado = $stmt->get_result();

	$cadena = file_get_contents("listado.html");
	$trozos = explode("##fila##", $cadena);

	$cuerpo = "";
	while ($datos = $resultado->fetch_assoc()) {
		$aux = $trozos[1];
		$aux = str_replace("##codigo##", $datos["idpersona"], $aux);
		$aux = str_replace("##nombre##", $datos["nombre"], $aux);
		$aux = str_replace("##apellido1##", $datos["apellido1"], $aux);
		$aux = str_replace("##apellido2##", $datos["apellido2"], $aux);
		$cuerpo .= $aux;
	}

	echo $trozos[0] . $cuerpo . $trozos[2];





?>



