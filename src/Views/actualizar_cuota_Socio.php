<!-- <?php
include_once ('../../Database/conexion.php');

// Obtener los datos enviados en el cuerpo de la solicitud
$data = json_decode(file_get_contents('php://input'), true);
$socioUsuario = $data['socioUsuario'];
$cuotaPagada = $data['cuotaPagada'];
$fechaUltimoPago = $data['fechaUltimoPago'];
$fechaProximoPago = $data['fechaProximoPago'];

// Conectar a la base de datos
$conexion = conectarBD();

// Preparar y ejecutar la consulta de actualización
$sql = "UPDATE Socio 
        SET `Cuota pagada` = ?, `Último pago` = ?, `Próximo pago` = ? 
        WHERE Usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("issi", $cuotaPagada, $fechaUltimoPago, $fechaProximoPago, $socioUsername);

// Crear una respuesta para indicar éxito o fracaso
$response = array();
if ($stmt->execute()) {
    $response['success'] = true;
} else {
    $response['success'] = false;
}

// Cerrar la conexión y la declaración
$stmt->close();
desconectarBD($conexion);

// Enviar la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);
?> -->

<?php
include_once ('../../Database/conexion.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $socioUsuario = $_POST['socioUsuario'];
    $cuotaPagada = isset($_POST['cuotaPagada']) ? 1 : 0;
    $fechaUltimoPago = date('Y-m-d');
    $fechaProximoPago = date('Y-m-d', strtotime('+1 month'));

    // Conectar a la base de datos
    $conexion = conectarBD();

    // Preparar y ejecutar la consulta de actualización
    $sql = "UPDATE Socio 
            SET `Cuota pagada` = ?, `Último pago` = ?, `Próximo pago` = ? 
            WHERE Usuario = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("isss", $cuotaPagada, $fechaUltimoPago, $fechaProximoPago, $socioUsuario);

    // Ejecutar la consulta y verificar el resultado
    if ($stmt->execute()) {
        header('Location: admin_view.php');
        exit();
    } else {
        echo "Error al actualizar la cuota: " . $stmt->error;
    }

    // Cerrar la conexión y la declaración
    $stmt->close();
    desconectarBD($conexion);
}
?>

