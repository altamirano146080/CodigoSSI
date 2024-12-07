<?php

	include "basededatos.php";
	$con = conexion();

	$stmt = $con->prepare("select * from personas where idpersona = ?");
	$stmt->bind_param("s", $idpersona);

	$idpersona = $_GET['idpersona'];

	$stmt->execute();

	$resultado = $stmt->get_result();
	$datos = $resultado->fetch_assoc();

	$cadena = file_get_contents("modificar.html");
	$cadena = str_replace("##idpersona##", $datos["idpersona"], $cadena);
	$cadena = str_replace("##nombre##", $datos["nombre"], $cadena);
	$cadena = str_replace("##apellido1##", $datos["apellido1"], $cadena);
	$cadena = str_replace("##apellido2##", $datos["apellido2"], $cadena);

	echo $cadena;

?>





