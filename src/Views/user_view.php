<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Vista de Usuario</title>
    <style>
        .status-icon {
            font-size: 24px;
        }
        .status-paid {
            color: green;
        }
        .status-unpaid {
            color: red;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table th, .info-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .info-table th {
            background-color: #f2f2f2;
            text-align: left;
        }
    </style>
</head>
<body>
    <?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header('Location: login.php');
        exit();
    }

    include_once ('../../Database/conexion.php');
    $conexion = conectarBD();

    $userUsername = $_SESSION['username'];

    // Obtener el nombre del club
    $clubQuery = "SELECT Club.Nombre 
                  FROM Club 
                  JOIN Socio ON Club.Nombre = Socio.Club 
                  WHERE Socio.Usuario = '$userUsername'";
    $clubResult = $conexion->query($clubQuery);
    $nombreClub = '';
    if ($clubResult->num_rows > 0) {
        $clubRow = $clubResult->fetch_assoc();
        $nombreClub = $clubRow['Nombre'];
    }

    // Obtener la información de pagos del usuario
    $sql = "SELECT Nombre, `Último pago`, `Próximo pago`, `Cuota pagada`
            FROM Socio
            WHERE Usuario = '$userUsername'";
    $selectMiTabla = $conexion->prepare($sql);
    $selectMiTabla->execute();
    $result = $selectMiTabla->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nombre = $row['Nombre'];
        $fecha_ultimo_pago = $row['Último pago'];
        $fecha_proximo_pago = $row['Próximo pago'];
        $cuota_pagada = $row['Cuota pagada'];

        $status_icon = $cuota_pagada ? "<i class='fas fa-check-circle status-icon status-paid'></i>" : "<i class='fas fa-times-circle status-icon status-unpaid'></i>";
    } else {
        $nombre = $fecha_ultimo_pago = $fecha_proximo_pago = $status_icon = '';
    }

    desconectarBD($conexion);
    ?>

    <h2>Información de Pagos</h2>
    <h3>Club: <?php echo $nombreClub; ?></h3>
    <table class="info-table">
        <tr>
            <th>Nombre</th>
            <th>Fecha del último pago</th>
            <th>Fecha del próximo pago</th>
            <th>Estado de pago</th>
            <th>Factura</th>
        </tr>
        <tr>
            <td><?php echo $nombre; ?></td>
            <td><?php echo $fecha_ultimo_pago; ?></td>
            <td><?php echo $fecha_proximo_pago; ?></td>
            <td><?php echo $status_icon; ?></td>
            <td>
                <a href="../instruments/generar_factura.php?usuario=<?php echo $userUsername; ?>" target="_blank">
                    <i class="fas fa-file-pdf status-icon"></i>
                </a>
            </td>
        </tr>
        <?php if (empty($nombre)) { ?>
            <tr><td colspan="5">No se encontró información de pagos</td></tr>
        <?php } ?>
    </table>
</body>
</html>
