<?php
    require_once("../loginConMod/comprobarusuario.php");
    echo "entra";

    if(!comprobarusuario()) {
        echo "comprueba";
        //Un header a iniciar sesion
        header("Location: ../ulio-html/iniciarSesion.html");
		exit;
	}
    echo "sale";
    header("Location: menu.html");
    exit;

?>
