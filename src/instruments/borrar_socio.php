<?php
include_once ('../../Database/conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $socioUsuario = $_POST['socioUsuario'];

    $conexion = conectarBD();
    $sql = "DELETE FROM Socio WHERE Usuario = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $socioUsuario);
    
    $response = ['success' => false];
    
    if ($stmt->execute()) {
        $response['success'] = true;
    }

    $stmt->close();
    desconectarBD($conexion);

    echo json_encode($response);
}
?>
