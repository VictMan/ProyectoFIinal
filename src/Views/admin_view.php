<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/styles.css">
    <script src="../instruments/jquery-3.7.1.min.js"></script>
    <title>Vista de Administrador</title>
</head>

<body>
    <h2>Administración de Socios</h2>

    <table border="1">
        <tr>
            <th>Nombre</th>
            <th>Usuario</th>
            <th>Cuota</th>
            <th>Fecha del último pago</th>
            <th>Fecha del próximo pago</th>
            <th>Borrar Socio</th>
            <th>Actualizar Cuota</th>
        </tr>
        <?php
        session_start();
        if (!isset($_SESSION['username'])) {
            header('Location: login.php');
            exit();
        }

        include_once ('../../Database/conexion.php');
        $conexion = conectarBD();

        $adminUsername = $_SESSION['username'];
        $querySelect = "SELECT Nombre FROM Club WHERE Usuario = '$adminUsername'";
        $result = $conexion->query($querySelect);
        $nombreClub = $result->fetch_assoc();
        $miClub = $nombreClub['Nombre'];
        echo '<h2>' . $miClub . '</h2>';

        $sql = "SELECT Nombre, Usuario, `Cuota pagada`, `Último pago`, `Próximo pago`
                FROM Socio
                WHERE Club = '$miClub'";
        $selectMiTabla = $conexion->prepare($sql);
        $selectMiTabla->execute();
        $result = $selectMiTabla->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $nombre = $row['Nombre'];
                $usuario = $row['Usuario'];
                $cuota_pagada = $row['Cuota pagada'];
                $fecha_ultimo_pago = $row['Último pago'];
                $fecha_proximo_pago = $row['Próximo pago'];

                $checked = $cuota_pagada ? 'checked' : '';
                echo "<tr data-id='$usuario'>
                        <td>$nombre</td>
                        <td>$usuario</td>
                        <td>
                            <input type='checkbox' class='cuota-switch' data-id='$usuario'  $checked>
                        </td>
                        <td class='fecha-ultimo-pago' data-id='$usuario'>$fecha_ultimo_pago</td>
                        <td class='fecha-proximo-pago' data-id='$usuario'>$fecha_proximo_pago</td>
                        <td>
                            <button class='delete-button' data-id='$usuario'>Borrar</button>
                        </td>
                        <td>
                            <button class='update-button' data-id='$usuario'>Actuzalizar</button>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No hay socios registrados en este club</td></tr>";
        }

        desconectarBD($conexion);
        ?>
    </table>

    <script>
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function () {
                let socioUsuario = this.getAttribute('data-id');
                if (confirm('¿Estás seguro de que deseas borrar este socio?')) {
                    fetch('borrar_socio.php', {
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
        
        document.querySelectorAll('.update-button').forEach(button => {
            button.addEventListener('click', function () {
                let row = this.closest('tr');
                let socioUsuario = row.getAttribute('data-id');
                let cuotaPagada = row.querySelector('.cuota-switch').checked ? 1 : 0;
                let fechaUltimoPago = new Date().toISOString().split('T')[0];
                let fechaProximoPago = new Date();
                fechaProximoPago.setMonth(fechaProximoPago.getMonth() + 1);
                let fechaProximoPagoString = fechaProximoPago.toISOString().split('T')[0];

                fetch('actualizar_cuota_Socio.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        socioUsuario: socioUsuario,
                        cuotaPagada: cuotaPagada,
                        fechaUltimoPago: fechaUltimoPago,
                        fechaProximoPago: fechaProximoPagoString
                    }).toString()
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            row.querySelector('.fecha-ultimo-pago').textContent = fechaUltimoPago;
                            row.querySelector('.fecha-proximo-pago').textContent = fechaProximoPagoString;
                        } else {
                            alert('Error al actualizar la cuota');
                        }
                    });
            });
        });

        // document.querySelectorAll('.cuota-switch').forEach(switchElement => {
        //     switchElement.addEventListener('change', function () {
        //         let socioUsuario = this.getAttribute('data-id');
        //         let cuotaPagada = this.checked ? 1 : 0;
        //         let fechaUltimoPago = new Date().toISOString().split('T')[0];
        //         let fechaProximoPago = new Date();
        //         fechaProximoPago.setMonth(fechaProximoPago.getMonth() + 1);
        //         let fechaProximoPagoString = fechaProximoPago.toISOString().split('T')[0];

        //         fetch('actualizar_cuota_Socio.php', {
        //             method: 'POST',
        //             headers: {
        //                 'Content-Type': 'application/json'
        //             },
        //             body: JSON.stringify({
        //                 socioUsuario: socioUsuario,
        //                 cuotaPagada: cuotaPagada,
        //                 fechaUltimoPago: fechaUltimoPago,
        //                 fechaProximoPago: fechaProximoPagoString
        //             })
        //         })
        //             .then(response => response.json())
        //             .then(data => {
        //                 if (data.success) {
        //                     let ultimoPagoElement = document.querySelector(`.fecha-ultimo-pago[data-usuario='${socioUsuario}']`);
        //                     let proximoPagoElement = document.querySelector(`.fecha-proximo-pago[data-usuario='${socioUsuario}']`);
        //                     if (ultimoPagoElement && proximoPagoElement) {
        //                         ultimoPagoElement.textContent = fechaUltimoPago;
        //                         proximoPagoElement.textContent = fechaProximoPagoStr;
        //                     }
        //                 } else {
        //                     alert('Error al actualizar la cuota');
        //                 }
        //             });
        //     });
        // });
    </script>
</body>

</html>