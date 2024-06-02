<?php
require('./fpdf/fpdf.php');
include_once('../../Database/conexion.php');

session_start();

if (!isset($_SESSION['username'])) {
    die('Acceso denegado');
}

$socioUsuario = $_SESSION['username'];
$conexion = conectarBD();

// Obtener información del socio
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

// Obtener información del club
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

// Ruta del logo por defecto si no se encuentra el logo del club
$defaultLogo = '../../Database/imagesPerfil/default_logo.jpg';
$logoPath = (!empty($logoClub) && file_exists($logoClub)) ? $logoClub : $defaultLogo;

// Crear el PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Añadir el logo del club
$pdf->Image($logoPath, 10, 10, 30); // Ajusta las coordenadas y el tamaño según sea necesario

// Añadir el título
$pdf->Cell(190, 40, iconv('UTF-8', 'ISO-8859-1', 'Justificante de pago'), 0, 1, 'C');
$pdf->Cell(190, 40, iconv('UTF-8', 'ISO-8859-1', $nombreClub), 0, 1, 'C');

// Añadir la información del socio
$pdf->SetFont('Arial', '', 12);
$pdf->Ln(10); // Salto de línea
$pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', "Nombre del socio: $nombreSocio"), 0, 1);
$pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', "Fecha del último pago: " . date('d-m-Y', strtotime($ultimoPago))), 0, 1);
$pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', "Fecha del próximo pago: " . date('d-m-Y', strtotime($proximoPago))), 0, 1);

// Output del PDF
$pdf->Output("I", "Factura_$socioUsuario.pdf");

desconectarBD($conexion);
?>
