<?php
session_start();
include_once('../../Database/conexion.php');

if (!isset($_SESSION['username']) || $_SESSION['type'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$conexion = conectarBD();
$usuario = $_SESSION['username'];
$tipo = $_SESSION['type'];

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_club'])) {
        $conexion->begin_transaction();
        try {
            $sql = "DELETE FROM Socio WHERE Club = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("s", $usuario);
            $stmt->execute();

            $sql = "DELETE FROM Club WHERE Usuario = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("s", $usuario);
            $stmt->execute();

            $conexion->commit();
            session_destroy();
            header('Location: login.php');
            exit();
        } catch (Exception $e) {
            $conexion->rollback();
            $error_message = "Error al eliminar el club: " . $e->getMessage();
        }
    }
}
desconectarBD($conexion);
?>
