<?php
include_once ('../../Database/conexion.php');

$socioUsuario = $_POST['socioUsuario'];
$cuotaPagada = $_POST['cuotaPagada'];
$fechaUltimoPago = isset($_POST['fechaUltimoPago']) ? $_POST['fechaUltimoPago'] : null;
$fechaProximoPago = isset($_POST['fechaProximoPago']) ? $_POST['fechaProximoPago'] : null;

$conexion = conectarBD();

if ($cuotaPagada == 1) {
    $sql = "UPDATE Socio SET `Cuota pagada` = ?, `Último pago` = ?, `Próximo pago` = ? WHERE Usuario = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('isss', $cuotaPagada, $fechaUltimoPago, $fechaProximoPago, $socioUsuario);
} else {
    $sql = "UPDATE Socio SET `Cuota pagada` = ? WHERE Usuario = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('is', $cuotaPagada, $socioUsuario);
}

$response = array();

if ($stmt->execute()) {
    $response['success'] = true;
} else {
    $response['success'] = false;
}

$stmt->close();
desconectarBD($conexion);

echo json_encode($response);
?>