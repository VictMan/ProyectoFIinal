<?php
include_once ('../../Database/conexion.php');

function comprobarUserName($userName)
{
    $error_message = '';
    $conexion = conectarBD();
    $sql_match = "SELECT Usuario FROM Club WHERE Usuario = '$userName'";
    $userNameMatch = $conexion->query($sql_match);

    if ($userNameMatch->num_rows > 0) {
        $error_message = "Este nombre de usuario ya se está usando";
    } else {
        $sql_match = "SELECT Usuario FROM Socio WHERE Usuario = '$userName'";
        $userNameMatch = $conexion->query($sql_match);

        if ($userNameMatch->num_rows > 0) {
            $error_message = "Este nombre de usuario ya se está usando";
        } 
        desconectarBD($conexion);
    }
    return $error_message;
}
?>