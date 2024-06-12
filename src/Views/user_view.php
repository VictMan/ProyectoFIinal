<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['type'] !== 'socio') {
    header('Location: login.php');
    exit();
}

include_once ('../includes/cabecera.php');
include_once ('../../Database/conexion.php');
$conexion = conectarBD();

$userUsername = $_SESSION['username'];

$clubQuery = "SELECT Club.Nombre, Socio.Foto, Club.HorarioImagen, Club.Logo 
              FROM Club 
              JOIN Socio ON Club.Nombre = Socio.Club 
              WHERE Socio.Usuario = '$userUsername'";
$clubResult = $conexion->query($clubQuery);
$nombreClub = '';
$fotoPerfil = '';
$horarioImagen = '';
$logoClub = '';
if ($clubResult->num_rows > 0) {
    $clubRow = $clubResult->fetch_assoc();
    $nombreClub = $clubRow['Nombre'];
    $fotoPerfil = $clubRow['Foto'];
    $horarioImagen = $clubRow['HorarioImagen'];
    $logoClub = $clubRow['Logo'];
}

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

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/styles.css">
    <script src="../instruments/funciones.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Vista de Usuario</title>
    <style>
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
            width: 100%;
            height: auto;
            max-height: 500px;
            /* Ajusta esta altura según sea necesario */
            object-fit: cover;
            border-radius: 10px;
        }

        .club-header {
            text-align: center;
            background-color: q;
            margin-bottom: 20px;
            padding-top: 5em;
            padding-bottom: 2em;
        }

        .club-header img {
            width: 160px;
            height: 160px;
            border-radius: 50%;
        }

        .club-header h2 {
            margin-top: 10px;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .card {
            width: 70%;
            margin: 20px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .profile-info {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .profile-info img {
            width: 64px;
            height: 64px;
            border-radius: 50%;
        }

        .profile-info div {
            display: flex;
            flex-direction: column;
        }

        .profile-info h2 {
            font-size: 1.125rem;
            font-weight: bold;
        }

        .profile-info p {
            color: #6b7280;
        }

        .payment-info {
            margin-top: 1rem;
            margin-bottom: 1rem;
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .payment-info .dates {
            display: flex;
            justify-content: space-between;
        }

        .payment-info p {
            color: #6b7280;
        }

        .status-paid {
            color: green;
        }

        .status-unpaid {
            color: red;
        }

        .download-button {
            width: 100%;
            text-align: center;
            padding: 10px;
            background-color: black;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .card img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .card-content {
            text-align: center;
        }

        @media screen and (min-width: 920px) {
            .main-content {
                display: grid;
                grid-template-columns: repeat(2, auto);
                gap:10px;
            }
            .card{
                width: 80%;
            }

            .card:nth-child(1){
                height:50%;
            }
        }
    </style>
    <script>
        $(document).ready(function () {
            let headerColor = getCookie("headerColor");
            if (headerColor) {
                $(".card").css("box-shadow", "0 10px 15px " + headerColor);
            }
        });

        function showPopupMessage(message) {
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
            popup.text(message);
            overlay.fadeIn(300);
            popup.css({ left: '-50%' }).show().animate({ left: '50%' }, 500);
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
    <main class="main-container">
        <div class="club-header">
            <img src="<?php echo $logoClub; ?>" alt="Logo del club">
            <h2><?php echo $nombreClub; ?></h2>
        </div>
        <div class="main-content">
            <div class="card">
                <div class="profile-info">
                    <img src="<?php echo $fotoPerfil; ?>" alt="Foto de perfil">
                    <div>
                        <h2><?php echo $nombre; ?></h2>
                        <p><?php echo $userUsername; ?></p>
                    </div>
                </div>
                <div class="payment-info">
                    <div>
                        <p>Estado de pago</p>
                        <p><?php echo $status_icon; ?></p>
                    </div>
                    <div class="dates">
                        <div>
                            <p>Último pago</p>
                            <p><?php echo $fecha_ultimo_pago; ?></p>
                        </div>
                        <div>
                            <p>Próximo pago</p>
                            <p><?php echo $fecha_proximo_pago; ?></p>
                        </div>
                    </div>
                </div>
                <a class="download-button <?php echo $cuota_pagada ? '' : 'unpaid'; ?>"
                    href="<?php echo $cuota_pagada ? "../instruments/generar_factura.php?usuario=$userUsername" : 'javascript:void(0)'; ?>"
                    <?php echo $cuota_pagada ? 'target="_blank"' : 'onclick="showPopupMessage(\'Tu cuota no está pagada, no puedes descargar este justificante\')"'; ?>>
                    Descargar justificante de pago
                </a>
            </div>
            <?php if (!empty($horarioImagen)): ?>
                <div class="card">
                    <h3>Horario del Club</h3>
                    <div class="card-content">
                        <img src="<?php echo $horarioImagen; ?>" alt="Horario del club" class="schedule-image">
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="popup-overlay"></div>
        <div class="popup-message"></div>
    </main>
    <?php include_once ('../includes/pie.html'); ?>
</body>

</html>