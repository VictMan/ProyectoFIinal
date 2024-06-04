<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="../instruments/jquery-3.7.1.min.js"></script>
    <title>Vista de Administrador</title>
</head>

<body>
    <?php
    session_start();
    if (!isset($_SESSION['username']) || $_SESSION['type'] !== 'admin') {
        header('Location: login.php');
        exit();
    }
    include_once('../includes/cabecera.php');
    include_once ('../../Database/conexion.php');
    $conexion = conectarBD();

    $adminUsername = $_SESSION['username'];
    $querySelect = "SELECT Nombre, Logo FROM Club WHERE Usuario = '$adminUsername'";
    $result = $conexion->query($querySelect);
    $nombreClub = $result->fetch_assoc();
    $miClub = $nombreClub['Nombre'];
    $logoClub = $nombreClub['Logo'];

    // Ruta del logo por defecto
    $defaultLogo = '../../Database/imagesPerfil/default_logo.jpg';

    // Verificar si existe un logo personalizado
    if (empty($logoClub) || !file_exists($logoClub)) {
        $logoClub = $defaultLogo;
    }

    echo '<h2>' . $miClub . '</h2>';
    echo '<img src="' . $logoClub . '" alt="Logo del Club" class="club-logo">';

    $sql = "SELECT Nombre, Usuario, `Cuota pagada`, `Último pago`, `Próximo pago`
            FROM Socio
            WHERE Club = '$miClub'";
    $selectMiTabla = $conexion->prepare($sql);
    $selectMiTabla->execute();
    $result = $selectMiTabla->get_result();

    ?>

    <table border="1">
        <tr>
            <th>Nombre</th>
            <th>Usuario</th>
            <th>Cuota</th>
            <th>Fecha del último pago</th>
            <th>Fecha del próximo pago</th>
            <th>Borrar Socio</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $nombre = $row['Nombre'];
                $usuario = $row['Usuario'];
                $cuota_pagada = $row['Cuota pagada'];
                $fecha_ultimo_pago = date("d-m-Y", strtotime($row['Último pago']));
                $fecha_proximo_pago = date("d-m-Y", strtotime($row['Próximo pago']));

                $checked = $cuota_pagada ? 'checked' : '';
                echo "<tr data-id='$usuario'>
                        <td>$nombre</td>
                        <td>$usuario</td>
                        <td>
                            <label class='switch'>
                                <input type='checkbox' class='cuota-switch' data-id='$usuario' $checked>
                                <span class='slider'></span>
                            </label>
                        </td>
                        <td class='fecha-ultimo-pago' data-id='$usuario'>$fecha_ultimo_pago</td>
                        <td class='fecha-proximo-pago' data-id='$usuario'>$fecha_proximo_pago</td>
                        <td>
                            <span class='delete-button' data-id='$usuario'><i class='fa-solid fa-trash'></i></span>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No hay socios registrados en este club</td></tr>";
        }

        desconectarBD($conexion);
        ?>
    </table>

    <script>
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function () {
                let socioUsuario = this.getAttribute('data-id');
                if (confirm('¿Estás seguro de que deseas borrar este socio?')) {
                    fetch('../instruments/borrar_socio.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({ socioUsuario: socioUsuario }).toString()
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                let row = document.querySelector(`tr[data-id='${socioUsuario}']`);
                                row.remove();
                            } else {
                                alert('Error al borrar el socio');
                            }
                        });
                }
            });
        });

        document.querySelectorAll('.cuota-switch').forEach(switchChnage => {
            let row = switchChnage.closest('tr');
            let fechaProximoPago = new Date(row.querySelector('.fecha-proximo-pago').textContent.split('-').reverse().join('-'));
            let today = new Date();

            if (today >= fechaProximoPago) {
                switchChnage.checked = false;
                actualizarCuota(switchChnage, false);
            }

            switchChnage.addEventListener('change', function () {
                actualizarCuota(this, this.checked);
            });
        });

        function actualizarCuota(switchChnage, isManualChange) {
            let row = switchChnage.closest('tr');
            let socioUsuario = row.getAttribute('data-id');
            let cuotaPagada = switchChnage.checked ? 1 : 0;
            let fechaUltimoPago = null;
            let fechaProximoPago = null;

            if (cuotaPagada) {
                let now = new Date();
                fechaUltimoPago = now.toISOString().split('T')[0];

                let nextMonth = new Date(now.setMonth(now.getMonth() + 1));
                let nextYear = nextMonth.getFullYear();
                let nextMonthNumber = nextMonth.getMonth() + 1;
                let nextDay = nextMonth.getDate();

                fechaProximoPago = new Date(nextYear, nextMonthNumber - 1, nextDay).toISOString().split('T')[0];
            }

            fetch('../instruments/actualizar_cuota_Socio.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    socioUsuario: socioUsuario,
                    cuotaPagada: cuotaPagada,
                    fechaUltimoPago: isManualChange && cuotaPagada ? fechaUltimoPago : null, // Only send if manual change and cuota is paid
                    fechaProximoPago: isManualChange && cuotaPagada ? fechaProximoPago : null // Only send if manual change and cuota is paid
                }).toString()
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (cuotaPagada) {
                            row.querySelector('.fecha-ultimo-pago').textContent = formatDate(fechaUltimoPago);
                            row.querySelector('.fecha-proximo-pago').textContent = formatDate(fechaProximoPago);
                        }
                    } else {
                        alert('Error al actualizar la cuota');
                    }
                });
        }

        function formatDate(dateString) {
            let [year, month, day] = dateString.split('-');
            return `${day}-${month}-${year}`;
        }
    </script>
</body>

</html>
