<?php
require('fpdf/fpdf.php');
include_once ('../../Database/conexion.php');

if (isset($_GET['usuario'])) {
    $userUsername = $_GET['usuario'];

    $conexion = conectarBD();

    // Obtener la información del socio
    $sql = "SELECT Nombre, `Último pago`, `Próximo pago`
            FROM Socio
            WHERE Usuario = '$userUsername'";
    $result = $conexion->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nombre = $row['Nombre'];
        $fecha_ultimo_pago = $row['Último pago'];
        $fecha_proximo_pago = $row['Próximo pago'];

        // Crear el PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Factura de Pago', 0, 1, 'C');
        $pdf->Ln(10);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, utf8_decode('Nombre del socio: ') . $nombre, 0, 1);
        $pdf->Cell(0, 10, utf8_decode('Fecha del último pago: ') . $fecha_ultimo_pago, 0, 1);
        $pdf->Cell(0, 10, utf8_decode('Fecha del próximo pago: ') . $fecha_proximo_pago, 0, 1);
        $pdf->Output('D', 'factura.pdf');
    }

    desconectarBD($conexion);
}
?>
