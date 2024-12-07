<?php
// PHP code to check authentication status
require_once("../loginConMod/comprobarusuario.php");

if(!comprobarusuario()) {
    // Return JSON response indicating not authenticated
    echo json_encode(array('authenticated' => false));
} else {
    // Return JSON response indicating authenticated
    echo json_encode(array('authenticated' => true));
}
?>