<?php

	include "basededatos.php";

	$con = conexion();

	$consulta = "insert into personas (nombre, apellido1, apellido2) values ('Maite', 'Ugarteburu', 'Uberuaga')";

	$resultado = $con->query($consulta);

	if ($resultado) {
		echo "Se ha insertado correctamente";
	} else {
		echo "No se ha insertado los datos";
	}

	$consulta = "select * from personas";
	$resultado = $con->query($consulta);

	while ($datos = $resultado->fetch_assoc()) {
		echo $datos["nombre"] . " " . $datos["apellido1"] . " " . $datos["apellido2"] . "<br>";
	}

?>





