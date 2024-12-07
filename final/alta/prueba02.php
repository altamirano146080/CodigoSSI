<?php

	include "basededatos.php";
	$con = conexion();

	$stmt = $con->prepare("insert into personas (nombre, apellido1, apellido2) values (?, ?, ?)");
	$stmt->bind_param("sss", $nom, $ape1, $ape2);

	$nom = "Nora";
	$ape1 = "Irerutagoiena";
	$ape2 = "Beguiristain";

	$stmt->execute();

	if ($stmt->affected_rows) {
		echo "Todo correcto. <br>";
	} else {
		echo "No se ha podido insertar<br>";
	}

	//Coger datos de la base de datos
	$stmt = $con->prepare("select * from personas");
	$stmt->execute();
	$resultado = $stmt->get_result();

	while ($datos = $resultado->fetch_assoc()) {
		echo $datos["nombre"] . " " . $datos["apellido1"] . " " . $datos["apellido2"] . "<br>";
	}



?>




