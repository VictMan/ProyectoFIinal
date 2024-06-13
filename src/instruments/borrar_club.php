<?php
session_start();
include_once('../../Database/conexion.php');

if (!isset($_SESSION['username']) || $_SESSION['type'] !== 'superadmin') {
    header('Location: ../../login.php');
    exit();
}

$conexion = conectarBD();
$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['clubName'])) {
        $clubName = $_POST['clubName'];

        $conexion->begin_transaction();
        try {
            $sql = "DELETE FROM Socio WHERE Club = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("s", $clubName);
            $stmt->execute();

            $sql = "DELETE FROM Club WHERE Nombre = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("s", $clubName);
            $stmt->execute();

            $conexion->commit();
            $response['success'] = true;
        } catch (Exception $e) {
            $conexion->rollback();
            $response['message'] = "Error al eliminar el club: " . $e->getMessage();
        }
    } else {
        $response['message'] = "Parámetros inválidos.";
    }
}

desconectarBD($conexion);
echo json_encode($response);
?>
