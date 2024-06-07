<?php
require('./fpdf/fpdf.php');
include_once('../../Database/conexion.php');

session_start();

if (!isset($_SESSION['username'])) {
    die('Acceso denegado');
}

$socioUsuario = $_SESSION['username'];
$conexion = conectarBD();

$querySocio = "SELECT Nombre, `Último pago`, `Próximo pago`, Club FROM Socio WHERE Usuario = ?";
$stmtSocio = $conexion->prepare($querySocio);
$stmtSocio->bind_param('s', $socioUsuario);
$stmtSocio->execute();
$resultSocio = $stmtSocio->get_result();

if ($resultSocio->num_rows === 0) {
    die('Socio no encontrado');
}

$socio = $resultSocio->fetch_assoc();
$nombreSocio = $socio['Nombre'];
$ultimoPago = $socio['Último pago'];
$proximoPago = $socio['Próximo pago'];
$club = $socio['Club'];

$queryClub = "SELECT Nombre, Logo FROM Club WHERE Nombre = ?";
$stmtClub = $conexion->prepare($queryClub);
$stmtClub->bind_param('s', $club);
$stmtClub->execute();
$resultClub = $stmtClub->get_result();

if ($resultClub->num_rows === 0) {
    die('Club no encontrado');
}

$clubInfo = $resultClub->fetch_assoc();
$nombreClub = $clubInfo['Nombre'];
$logoClub = $clubInfo['Logo'];

$defaultLogo = '../../Database/imagesPerfil/default_logo.jpg';
$logoPath = (!empty($logoClub) && file_exists($logoClub)) ? $logoClub : $defaultLogo;

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

$pdf->Image($logoPath, 10, 10, 30); 

$pdf->Cell(190, 40, iconv('UTF-8', 'ISO-8859-1', $nombreClub), 0, 1, 'C');

$pdf->SetFont('Arial', '', 12);
$pdf->Ln(20);

$ultimoPagoFormatted = date('d-m-Y', strtotime($ultimoPago));
$proximoPagoFormatted = date('d-m-Y', strtotime($proximoPago));

$pdf->MultiCell(0, 10, iconv('UTF-8', 'ISO-8859-1', "El socio $nombreSocio ha pagado la cuota el día $ultimoPagoFormatted.\n\nEste documento queda como justificante del acto. Deberá volver a ingresar la cuota el $proximoPagoFormatted."), 0, 1);

$pdf->Output("I", "Justificante_$socioUsuario.pdf");

desconectarBD($conexion);
?>
