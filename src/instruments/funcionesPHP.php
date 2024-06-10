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
function dibuja_select($nomSel, $tabla, $campo)
    {
        $html = "<select name='$nomSel'>\n";
        $html .= "<option value='Todos'>Todos</option>\n";
        $conexion = conectarBD();
        $sql = "SELECT DISTINCT $campo FROM $tabla ORDER BY $campo";
        $res = $conexion->query($sql);

        while ($fila = $res->fetch_array()) {
            $valor = $fila[$campo];
            $selected = isset($_POST[$nomSel]) && $_POST[$nomSel] == $valor ? "selected" : "";
            $html .= "<option value='$valor' $selected>$valor</option>\n";
        }

        $res->free();
        desconectarBD($conexion);
        $html .= "</select>\n";

        return $html;
    }
?>