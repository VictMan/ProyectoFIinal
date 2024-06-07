<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Vista de Usuario</title>
    <?php
    $headerColor = '';
    if (isset($_COOKIE['headerColor'])) {
        $headerColor = $_COOKIE['headerColor'];
    }
    ?>
    <style>
        <?php if ($headerColor): ?>
            th {
                background-color: <?php echo $headerColor; ?>;
            }
        <?php endif; ?>
        .popup-message {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            background-color: #f44336;
            color: white;
            font-size: 18px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            z-index: 1001;
        }
        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }
        .schedule-image {
            margin-top: 20px;
            max-width: 100%;
            height: auto;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        function showPopupMessage(message) {
            // Create or select existing overlay and popup
            let overlay = $('.popup-overlay');
            if (overlay.length === 0) {
                overlay = $('<div class="popup-overlay"></div>');
                $('body').append(overlay);
            }
            let popup = $('.popup-message');
            if (popup.length === 0) {
                popup = $('<div class="popup-message"></div>');
                $('body').append(popup);
            }

            // Set the message
            popup.text(message);

            // Show overlay and popup
            overlay.fadeIn(300);
            popup.css({ left: '-50%' }).show().animate({ left: '50%' }, 500);

            // Close on click
            overlay.on('click', () => {
                popup.animate({ left: '-50%' }, 500, function () {
                    $(this).hide();
                });
                overlay.fadeOut(300);
            });
        }
    </script>
</head>
<body>
<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['type'] !== 'socio') {
    header('Location: login.php');
    exit();
}
include_once('../includes/cabecera.php');
include_once('../../Database/conexion.php');
$conexion = conectarBD();

$userUsername = $_SESSION['username'];

// Obtener el nombre del club, la foto de perfil y la imagen del horario
$clubQuery = "SELECT Club.Nombre, Socio.Foto, Club.HorarioImagen 
              FROM Club 
              JOIN Socio ON Club.Nombre = Socio.Club 
              WHERE Socio.Usuario = '$userUsername'";
$clubResult = $conexion->query($clubQuery);
$nombreClub = '';
$fotoPerfil = '';
$horarioImagen = '';
if ($clubResult->num_rows > 0) {
    $clubRow = $clubResult->fetch_assoc();
    $nombreClub = $clubRow['Nombre'];
    $fotoPerfil = $clubRow['Foto'];
    $horarioImagen = $clubRow['HorarioImagen'];
}

// Usar foto genérica si no hay foto de perfil
if (empty($fotoPerfil)) {
    $fotoPerfil = '../../Database/imagesPerfil/perfilGeneric.jpg';
}

$sql = "SELECT Nombre, `Último pago`, `Próximo pago`, `Cuota pagada`
        FROM Socio
        WHERE Usuario = '$userUsername'";
$selectMiTabla = $conexion->prepare($sql);
$selectMiTabla->execute();
$result = $selectMiTabla->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nombre = $row['Nombre'];
    $fecha_ultimo_pago = date("d-m-Y", strtotime($row['Último pago']));
    $fecha_proximo_pago = date("d-m-Y", strtotime($row['Próximo pago']));
    $cuota_pagada = $row['Cuota pagada'];

    $status_icon = $cuota_pagada ? "<i class='fas fa-check-circle status-icon status-paid'></i>" : "<i class='fas fa-times-circle status-icon status-unpaid'></i>";
} else {
    $nombre = $fecha_ultimo_pago = $fecha_proximo_pago = $status_icon = '';
}

desconectarBD($conexion);
?>

<h2>Información de Pagos</h2>
<h3>Club: <?php echo $nombreClub; ?></h3>
<img src="<?php echo $fotoPerfil; ?>" alt="Foto de perfil" class="profile-photo">
<table class="info-table">
    <tr>
        <th>Nombre</th>
        <th>Fecha del último pago</th>
        <th>Fecha del próximo pago</th>
        <th>Estado de pago</th>
        <th>Último justificante</th>
    </tr>
    <tr>
        <td><?php echo $nombre; ?></td>
        <td><?php echo $fecha_ultimo_pago; ?></td>
        <td><?php echo $fecha_proximo_pago; ?></td>
        <td><?php echo $status_icon; ?></td>
        <td>
            <?php if ($cuota_pagada) { ?>
                <a href="../instruments/generar_factura.php?usuario=<?php echo $userUsername; ?>" target="_blank">
                    <i class="fas fa-file-pdf status-icon"></i>
                </a>
            <?php } else { ?>
                <a href="javascript:void(0)" onclick="showPopupMessage('Tu cuota no está pagada, no puedes descargar este justificante')">
                    <i class="fas fa-file-pdf status-icon"></i>
                </a>
            <?php } ?>
        </td>
    </tr>
    <?php if (empty($nombre)) { ?>
        <tr><td colspan="5">No se encontró información de pagos</td></tr>
    <?php } ?>
</table>

<?php if (!empty($horarioImagen)): ?>
    <h3>Horario del Club</h3>
    <img src="<?php echo $horarioImagen; ?>" alt="Horario del club" class="schedule-image">
<?php endif; ?>
</body>
</html>
