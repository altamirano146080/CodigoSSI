<?php

	include "basededatos.php";
	$con = conexion();

	$stmt = $con->prepare("update personas set nombre = ?, apellido1 = ?, apellido2 = ? where idpersona = ?");
	$stmt->bind_param("ssss", $nom, $ape1, $ape2, $id);

	$nom = $_POST['nombre'];
	$ape1 = $_POST['apellido1'];
	$ape2 = $_POST['apellido2'];
	$id = $_POST['idpersona'];

	$stmt->execute();

	if ($stmt->affected_rows) {
		echo "Se ha modificado con éxito";
	} else {
		echo "No se ha modificado";
	}



?>